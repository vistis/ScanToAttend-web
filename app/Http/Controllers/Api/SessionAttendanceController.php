<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRegistration;
use App\Models\ClassSession;
use App\Models\CourseClass;
use App\Models\SessionAttendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SessionAttendanceController extends Controller
{
    /**
     * Record attendnace of a student for a session.
     */
    public function create(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'fingerprint_id' => ['required', 'integer', 'exists:students,fingerprint_id'],
            'scanned_at' => ['required', 'date']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();
        $fingerprint = $data['fingerprint_id'];

        /** Parse the timestamp into date, time, and day of week. */
        $scannedAt = Carbon::parse($data['scanned_at']);
        $checkedInOn = $scannedAt->toDateString();
        $checkedInAt = $scannedAt->toTimeString();
        $day = $scannedAt->englishDayOfWeek;

        /** Get the student information. */
        $student = Student::where('fingerprint_id', $fingerprint)->first();

        if(!$student)
        {
            return response()->json([
                'message' => "Cannot resolve fingerprint to a registered student."
            ], 400);
        }

        /** Check for session the student is attending. */
        $session = ClassSession::join('class_registrations', 'class_sessions.class_id', '=', 'class_registrations.class_id')
            ->where('class_registrations.student_id', '=', $student->id)
            ->where('class_sessions.day', '=', $day)
            ->where(function (Builder $query) use ($checkedInAt)
            {
                $query->where('class_sessions.start_at', '<=', $checkedInAt)
                    ->where('class_sessions.end_at', '>=', $checkedInAt);
            })
            ->select(
                'class_sessions.id as id',
                'class_sessions.class_id as class_id',
                'class_sessions.day',
                'class_sessions.start_at as start_at',
                'class_sessions.end_at'
            )
            ->first();

        if(!$session)
        {
            return response()->json([
                'message' => "Failed to record. No session to attend right now."
            ], 400);
        }

        /** Check if the student already checked for this session today. */
        if (SessionAttendance::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->where('checked_in_on', $checkedInOn)
            ->exists()
        )
        {
            return response()->json([
                'message' => "Already checked in for this session."
            ], 400);
        }

        /** Get the information of the session. */
        $sessionInfo = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.id', $session->class_id)
            ->select('courses.code as code', 'courses.name as name', 'classes.section as section')
            ->first();
        $sessionInfo->day = $session->day;
        $sessionInfo->from = $session->start_at;
        $sessionInfo->to = $session->end_at;

        /** Determine the status. */
        $startAt = Carbon::parse($session->start_at);
        $checkedInAtObject = Carbon::parse($checkedInAt);
        $startAtStamp = ($startAt->hour * 3600) + ($startAt->minute * 60) + $startAt->second;
        $checkedInAtStamp = ($checkedInAtObject->hour * 3600) + ($checkedInAtObject->minute * 60) + $checkedInAtObject->second;
        if (($checkedInAtStamp - $startAtStamp) >= 15 * 60)
        {
            $status = "Tardy";
        }
        else {
            $status = "Present";
        }

        /** Record attendance. */
        SessionAttendance::create([
            'student_id' => $student->id,
            'session_id' => $session->id,
            'checked_in_on' => $checkedInOn,
            'checked_in_at' => $checkedInAt,
            'status' => $status
        ]);

        return response()->json([
            'message' => $student->username . " checked in.",
            'session_info' => $sessionInfo,
        ]);
    }

    /**
     * Get the attendance records of a class as an authenticated student.
     */
    public function readListOfClass(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:classes,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Get the student behind the request. */
        $student = $request->user();

        /** Check if the student is registered in the class. */
        if (!ClassRegistration::where('student_id', $student->id)
            ->where('class_id', $data['id'])
            ->exists()
        )
        {
            return response()->json([
                'message' => "You are not registered in this class."
            ], 401);
        }

        /** Get the attendance records. */
        $dates = SessionAttendance::join('class_sessions', 'session_attendance.session_id', '=', 'class_sessions.id')
            ->where('class_sessions.class_id', $data['id'])
            ->select('session_attendance.checked_in_on')
            ->distinct()
            ->orderBy('session_attendance.checked_in_on')
            ->get();

        $records = array();

        foreach ($dates as $date)
        {
            $entry = SessionAttendance::join('class_sessions', 'session_attendance.session_id', '=', 'class_sessions.id')
                ->where('class_sessions.class_id', $data['id'])
                ->where('session_attendance.checked_in_on', $date->checked_in_on)
                ->where('session_attendance.student_id', $student->id);

            $entryInfo = SessionAttendance::join('class_sessions', 'session_attendance.session_id', '=', 'class_sessions.id')
                ->where('class_sessions.class_id', $data['id'])
                ->where('session_attendance.checked_in_on', $date->checked_in_on)
                ->select('class_sessions.day', 'class_sessions.start_at', 'class_sessions.end_at', 'session_attendance.checked_in_on as date')
                ->first();

            if ($entry->exists())
            {
                $entryInfo->status = $entry->first()->status;
            }
            else {
                $entryInfo->status = "Absent";
            }

            $records[] = $entryInfo;
        }

        return response()->json([
            'message' => "Attendance records retrived.",
            'records' => $records
        ]);
    }

    /**
     * Get the dates of class that has attendance record.
     */
    public function readDatesOfClass (Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:classes,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Get the instructor behind the request. */
        $instructor = $request->user();

        /** Check if the instructor teaches this class. */
        if (!CourseClass::where('id', $data['id'])
            ->where('instructor_id', $instructor->id)
            ->exists()
        )
        {
            return response()->json([
                'message' => "You do not have access to this class."
            ], 401);
        }

        /** Retrive the dates. */
        $entries = SessionAttendance::join('class_sessions', 'session_attendance.session_id', '=', 'class_sessions.id')
            ->where('class_sessions.class_id', $data['id'])
            ->select('session_attendance.checked_in_on')
            ->distinct()
            ->get();

        $dates = array();

        foreach ($entries as $entry)
        {
            $dates[] = $entry->checked_in_on;
        }

        return response()->json([
            'message' => "Attendance dates retrived.",
            'dates' => $dates
        ]);
    }

    /**
     * Get the attendance record of a particular session.
     */
    public function readListOfSession (Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:class_sessions,id'],
            'date' => ['required', 'date', 'date_format:Y-m-d']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Get the instructor behind the request. */
        $instructor = $request->user();

        /** Find the class the session is apart of. */
        $class = ClassSession::join('classes', 'class_sessions.class_id', '=', 'classes.id')
            ->where('class_sessions.id', $data['id'])
            ->where('classes.instructor_id', $instructor->id)
            ->select('classes.id as id')
            ->first();

        /** Check if the instructor teaches this class */
        if (!$class)
        {
            return response()->json([
                'message' => "No access to the attendance record of this session."
            ], 401);
        }

        /** Check if the session proceeded on the requested date */
        if (!SessionAttendance::where('session_id', $data['id'])
            ->where('checked_in_on', $data['date'])
            ->exists()
        )
        {
            return response()->json([
                'message' => "The requested session did not proceed on this date."
            ], 404);
        }

        /** Get the student list. */
        $students = ClassRegistration::join('classes', 'class_registrations.class_id', '=', 'classes.id')
            ->join('students', 'class_registrations.student_id', '=', 'students.id')
            ->where('classes.id', $class->id)
            ->select(
                'students.id as id',
                'students.first_name as first_name',
                'students.last_name as last_name',
                'students.profile_picture as profile_picture',
                'students.email as email'
            )
            ->orderByDesc('first_name')
            ->get();

        /** Get the attendance status. */
        foreach ($students as $student)
        {
            $entry = SessionAttendance::where('checked_in_on', $data['date'])
                ->where('student_id', $student->id)
                ->where('session_id', $data['id'])
                ->first();

            if ($entry)
            {
                $student->status = $entry->status;
            }
            else {
                $student->status = "Absent";
            }

            /** Also generate URL for profile picture. */
            $student->profile_picture = Storage::url($student->profile_picture);
        }

        return response()->json([
            'message' => "Attendance record retrieved.",
            'students' => $students,
        ]);
    }
}
