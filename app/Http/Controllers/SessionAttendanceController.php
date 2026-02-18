<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\SessionAttendance;
use App\Models\Student;
use App\Models\ClassSession;
use App\Models\ClassRegistration;
use App\Models\CourseClass;

class SessionAttendanceController extends Controller
{
    public function create(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'fingerprint_id' => ['required', 'integer', 'exists:students,fingerprint_id'],
            'scanned_at' => ['required', 'date']
        ]);

        // Return error message if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => $validator->messages()
            ], 400);
        }

        // Get the validated data
        $data = $validator->validated();

        // The the validated data
        $fingerprint = $data['fingerprint_id'];

        // Get day, date, and time
        $scannedAt = Carbon::parse($data['scanned_at']);
        $checkedInOn = $scannedAt->toDateString();
        $checkedInAt = $scannedAt->toTimeString();
        $day = $scannedAt->englishDayOfWeek;

        // Resolve the student information
        $student = Student::where('fingerprint_id', $fingerprint)->first();

        if(!$student) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'fingerprint_id' => [
                        "Cannot resolve fingerprint to a registered student."
                    ]
                ]
            ], 400);
        }

        // Resolve the session the student is taking
        $session = ClassSession::join('class_registrations', 'class_sessions.class_id', '=', 'class_registrations.class_id')
            ->where('class_registrations.student_id', '=', $student->id)
            ->where('class_sessions.day', '=', $day)
            ->where(function (Builder $query) use ($checkedInAt) {
                $query->where('class_sessions.start_at', '<=', $checkedInAt)
                    ->where('class_sessions.end_at', '>=', $checkedInAt);
            })
            ->select('class_sessions.id as id', 'class_sessions.class_id as class_id', 'class_sessions.start_at as start_at')
            ->first();

        if(!$session) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'class_session' => [
                        "Failed to record. No session to attend right now."
                    ]
                ]
            ], 400);
        }

        // Check if the student already checked in
        if (SessionAttendance::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->where('checked_in_on', $checkedInOn)
            ->exists()
        ) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'session_attendance' => [
                        "Already checked in for this session."
                    ]
                ]
            ], 400);
        }

        // Resolve session information
        $sessionInfo = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.id', $session->class_id)
            ->select('courses.code as code', 'courses.name as name', 'classes.section as section')
            ->first();
        $sessionInfo->day = $session->day;
        $sessionInfo->from = $session->start_at;
        $sessionInfo->to = $session->end_at;

        // Determine the status
        $startAt = Carbon::parse($session->start_at);
        $checkedInAtObject = Carbon::parse($checkedInAt);
        $startAtStamp = ($startAt->hour * 3600) + ($startAt->minute * 60) + $startAt->second;
        $checkedInAtStamp = ($checkedInAtObject->hour * 3600) + ($checkedInAtObject->minute * 60) + $checkedInAtObject->second;
        if (($checkedInAtStamp - $startAtStamp) >= 15 * 60) {
            $status = "Tardy";
        }
        else {
            $status = "Present";
        }

        // Record the attendance
        SessionAttendance::create([
            'student_id' => $student->id,
            'session_id' => $session->id,
            'checked_in_on' => $checkedInOn,
            'checked_in_at' => $checkedInAt,
            'status' => $status
        ]);

        // Respond as JSON
        return response()->json([
            'message' => $student->username . " checked in.",
            'session_info' => $sessionInfo,
        ]);
    }

    /* GET ATTENDANCE RECORD OF A CLASS AS STUDENT */
    public function readAllAsStudent(Request $request) {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:classes,id']
        ]);

        // Return error message if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => $validator->messages()
            ], 400);
        }

        // Get the validated data
        $data = $validator->validated();

        // Get student information
        $student = $request->user();

        // Check if the student is registered in the class
        if (!ClassRegistration::where('student_id', $student->id)
            ->where('class_id', $data['id'])
            ->exists()
        ) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'class_registration' => [
                        "You are not registered in this class."
                    ]
                ]
            ], 404);
        }

        // Get attendance record
        $dates = SessionAttendance::join('class_sessions', 'session_attendance.session_id', '=', 'class_sessions.id')
            ->where('class_sessions.class_id', $data['id'])
            ->select('session_attendance.checked_in_on')
            ->distinct()
            ->orderBy('session_attendance.checked_in_on')
            ->get();

        $records = array();

        foreach ($dates as $date) {
            $entry = SessionAttendance::join('class_sessions', 'session_attendance.session_id', '=', 'class_sessions.id')
                ->where('class_sessions.class_id', $data['id'])
                ->where('session_attendance.checked_in_on', $date->checked_in_on)
                ->where('session_attendance.student_id', $student->id);

            $entryInfo = SessionAttendance::join('class_sessions', 'session_attendance.session_id', '=', 'class_sessions.id')
                ->where('class_sessions.class_id', $data['id'])
                ->where('session_attendance.checked_in_on', $date->checked_in_on)
                ->select('class_sessions.day', 'class_sessions.start_at', 'class_sessions.end_at', 'session_attendance.checked_in_on as date')
                ->first();

            if ($entry->exists()) {
                $entryInfo->status = $entry->first()->status;
            }
            else {
                $entryInfo->status = "Absent";
            }

            $records[] = $entryInfo;
        }

        // Respond as JSON
        return response()->json([
            'message' => "Retrieved attendance record for class with ID " . $data['id'] . ".",
            'records' => $records
        ]);
    }

    /* GET THE ATTENDANCE RECORD OF A SESSION AS INSTRUCTOR */
    public function readAllForSession (Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:class_sessions,id'],
            'date' => ['required', 'date', 'date_format:Y-m-d']
        ]);

        // Return error message if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => $validator->messages()
            ], 400);
        }

        // Get the validated data
        $data = $validator->validated();

        // Get the instructor information
        $instructor = $request->user();

        // Get the class the session is apart of
        $class = ClassSession::join('classes', 'class_sessions.class_id', '=', 'classes.id')
            ->where('class_sessions.id', $data['id'])
            ->where('classes.instructor_id', $instructor->id)
            ->select('classes.id as id')
            ->first();

        // Check if the instructor teaches this class
        if (!$class){
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'session_attendance' => [
                        "No access to the attendance record of this session."
                    ]
                ]
            ], 403);
        }

        // Check if the session proceeded on the requested date
        if (!ClassSession::where('id', $data['id'])
            ->where('day', Carbon::parse($data['date'])->englishDayOfWeek)
            ->exists()
        ) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'class_session' => [
                        "The requested session did not proceed on this date."
                    ]
                ]
            ], 404);
        }

        // Get the student list
        $students = ClassRegistration::join('classes', 'class_registrations.class_id', '=', 'classes.id')
            ->join('students', 'class_registrations.student_id', '=', 'students.id')
            ->where('classes.id', $class->id)
            ->select('students.id as id', 'students.first_name as first_name', 'students.last_name as last_name', 'students.profile_picture as profile_picture', 'students.email as email')
            ->orderByDesc('first_name')
            ->get();

        // Get the attendance status
        foreach ($students as $student) {
            $entry = SessionAttendance::where('checked_in_on', $data['date'])
                ->where('student_id', $student->id)
                ->where('session_id', $data['id'])
                ->first();

            if ($entry) {
                $student->status = $entry->status;
            }
            else {
                $student->status = "Absent";
            }

            // Also generate URL for profile picture
            $student->profile_picture = Storage::url($student->profile_picture);
        }

        // Respond as JSON
        return response()->json([
            'message' => "Retrieved attendance list on " . $data['date'] . " for session with ID " . $data['id'] . ".",
            'students' => $students,
        ]);
    }
}
