<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\ClassRegistration;
use App\Models\CourseClass;

class ClassRegistrationController extends Controller
{
    /* REGISTER A STUDENT INTO A CLASS */
    public function create(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id']
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

        // Check if the student is already registered in this course
        $courseId = CourseClass::find($data['class_id'])->course_id;
        if (ClassRegistration::join('classes', 'class_registrations.class_id', '=', 'classes.id')
            ->join('courses', 'classes.course_id', '=', 'courses.id')
            ->where('class_registrations.student_id', $data['student_id'])
            ->where('courses.id', $courseId)
            ->exists()
        ) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'course' => [
                        "Student is already registered in a class of the same course."
                    ]
                ]
            ], 400);
        }

        // Add registration
        $classRegistration = ClassRegistration::create($data);

        // Respond as JSON
        return response()->json([
            'message' => "Class registration added.",
            'class_registration' => $classRegistration
        ], 200);
    }

    /* GET THE LIST OF STUDENT REGISTERED IN A CLASS */
    public function readStudent(Request $request) : JsonResponse {
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

        // Get the student list
        $students = ClassRegistration::join('classes', 'class_registrations.class_id', '=', 'classes.id')
            ->join('students', 'class_registrations.student_id', '=', 'students.id')
            ->where('classes.id', $data['id'])
            ->select('students.id as id', 'students.first_name as first_name', 'students.last_name as last_name', 'students.profile_picture as profile_picture', 'students.email as email')
            ->orderByDesc('first_name')
            ->get();

        // Generate URL for profile picture
        foreach ($students as $student) {
            $student->profile_picture = Storage::url($student->profile_picture);
        }

        // JSON Response
        return response()->json([
            'message' => "Student list of class with ID " . $data['id'] . " retrived.",
            'students' => $students
        ], 200);
    }

    /* UNREGISTER A STUDENT FROM A CLASS */
    public function delete(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id']
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

        // Find the registration record
        $record = ClassRegistration::where('student_id', $data['student_id'])
            ->where('class_id', $data['class_id'])->first();

        if (!$record) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'relation' => [
                        "No registration record of this relation found."
                    ]
                ]
            ], 404);
        }

        // Delete the record
        $record->delete();

        // JSON Response
        return response()->json([
            'message' => "Class registration removed.",
            'class_registration' => $record
        ], 200);
    }
}
