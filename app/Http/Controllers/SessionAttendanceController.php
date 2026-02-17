<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Models\SessionAttendance;
use App\Models\Student;
use App\Models\ClassSession;
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
            'message' => "Checked in.",
            'session_info' => $sessionInfo,
        ]);
    }
}
