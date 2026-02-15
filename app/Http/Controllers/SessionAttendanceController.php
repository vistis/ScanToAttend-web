<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\SessionAttendance;
use App\Models\Student;
use App\Models\ClassRegistration;
use App\Models\ClassSession;
use App\Models\CourseClass;

class SessionAttendanceController extends Controller
{
    public function create(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'fingerprint_id' => ['required', 'integer', 'exists:students,fingerprint_id'],
            'checked_in_on' => ['required', 'date', 'date_format:Y-m-d'],
            'checked_in_at' => ['required', 'date_format:H:i:s']
        ]);

        // Return error message if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => $validator->messages()
            ], 400);
        }

        // The the validated data
        $fingerprint = $validator->validated()['fingerprint_id'];

        // Get day, date, and time
        $checkedInOn = Carbon::parse($validator->validated()['checked_in_on']);
        $checkedInAt = Carbon::parse($validator->validated()['checked_in_at']);
        $day = $checkedInOn->englishDayOfWeek;

        // Resolve the student information
        $studentId = Student::where('fingerprint_id', $fingerprint)
            ->first()
            ->id;

        if(!$studentId) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'fingerprint_id' => [
                        "Cannot resolve fingerprint to a registered student."
                    ]
                ]
            ], 400);
        }

        // Check if the student is enrolled into any class
        if(!ClassRegistration::where('student_id', '=', $studentId)->exists()) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'class_registration' => [
                        "Student is not enrolled in any class."
                    ]
                ]
            ], 400);
        }

        // Resolve the session the student is taking
        $session = ClassSession::join('class_registrations', 'class_sessions.class_id', '=', 'class_registrations.class_id')
            ->where('class_registrations.student_id', '=', $studentId)
            ->where('class_sessions.day', '=', $day)
            ->where('class_sessions.start_at', '<=', $checkedInAt)
            ->where('class_sessions.end_at', '>=', $checkedInAt)
            ->select('class_sessions.id as id', 'class_sessions.start_at as start_at');

        if(!$session->exists()) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'class_session' => [
                        "Failed to record. No session to attend right now."
                    ]
                ]
            ], 400);
        }

        $sessionId = $session->id;

        // Check if the student already checked in
        if (SessionAttendance::where('student_id', $studentId)
            ->where('session_id', $sessionId)
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

        // Resolve class information
        $courseId = CourseClass::where('class_id', $session->class_id)
            ->first()
            ->course_id;
        $classInfo = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.class_id', $session->class_id)
            ->where('classes.course_id', $courseId)
            ->select('courses.id as code', 'courses.name as name', 'classes.section as section', 'classes.start_at as from', 'classes.end_at as to')
            ->first();

        // Determine the status
        $startAt = Carbon::parse($session->start_at);
        $startAtMin = ($startAt->hour * 60) + $startAt->minute;
        $checkedInAtMin = ($checkedInAt->hour * 60) + $checkedInAt->minute;
        if (($checkedInAtMin - $startAtMin) > 15) {
            $status = "Tardy";
        }
        else {
            $status = "Present";
        }

        // Record the attendance
        SessionAttendance::create([
            'student_id' => $studentId,
            'session_id' => $sessionId,
            'checked_in_on' => $checkedInOn,
            'checked_in_at' => $checkedInAt,
            'status' => $status
        ]);

        // Respond as JSON
        return response()->json([
            'message' => "Checked in.",
            'class_info' => $classInfo,
        ]);
    }
}
