<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
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
            'fingerprint_id' => ['required', 'integer', 'unique:students,fingerprint_id'],
            'checked_in_on' => ['required', 'date', 'format:(Y-m-d)'],
            'checked_in_at' => ['required', 'time', 'format:(H:i:s)']
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
        $checkedInOn = $validator->validated()['checked_in_on'];
        $checkedInAt = $validator->validated()['checked_in_at'];
        $day = $checkedInOn->format('l');

        // Resolve the student information
        $studentId = Student::where('fingerprint_id', $fingerprint)
            ->select('id')
            ->first()
            ->id;

        if ($studentId) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'fingerprint_id' => [
                        "Cannot resolve fingerprint to a registered student"
                    ]
                ]
            ], 400);
        }

        // Resolve the session the student is taking
        $session = ClassRegistration::join('class_sessions', 'class_sessions.class_id', '=', 'class_registration.class_id')
            ->join('students', 'students.id', '=', 'class_registration.student_id')
            ->where('students.id', '=', $studentId)
            ->where('class_sessions.day', '=', $day)
            ->where('class_sessions.start_time', '>=', $checkedInAt)
            ->where($checkedInAt, '<=', 'class_sessions.end_time')
            ->select('class_sessions.id')
            ->first();
        $sessionId = $session->id;

        if (!$sessionId) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'class_sessions' => [
                        "Failed to record. No session to attend right now."
                    ]
                ]
            ], 400);
        }

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
                        "Already checked in for this session"
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

        SessionAttendance::create([
            'student_id' = $studentId,
            'session_id' = $sessionId,
            'checked_in_on' = $checkedInOn,
            'checked_in_at' = $checkedInAt,
        ]);

        return response()->json([
            'message' => "Checked in successfully",
            'class_info' => $classInfo,
        ]);
    }
}
