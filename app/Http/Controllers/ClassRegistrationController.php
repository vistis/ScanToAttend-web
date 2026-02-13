<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\ClassRegistration;

class ClassRegistrationController extends Controller
{
    public function resolve(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['string', 'in:classes,id'],
        ]);

        // Return error message if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages(),
            ], 400);
        }

        $students = ClassRegistration::join('classes', 'class_registration.class_id', '=', 'classes.id')
            ->join('students', 'class_registration.student_id', '=', 'students.id')
            ->where('classes.id', $request->class_id)
            ->select('students.id as id', 'students.first_name as first_name', 'students.last_name as last_name', 'students.profile_picture as profile_picture', 'students.email as email')
            ->orderByDesc('first_name')
            ->get();

        // Generate URL for profile picture
        foreach ($students as $student) {
            $student->profile_picture = Storage::url($student->profile_picture);
        }

        // JSON Response
        return response()->json([
            'students' => $students,
        ], 200);
    }
}
