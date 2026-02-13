<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;

class StudentController extends Controller
{
    public function create(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'fingerprint_id' => ['required', 'integer', 'unique:students,fingerprint_id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'profile_picture' => ['required', 'image', 'max:2048'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'password' => ['required', Password::defaults()],
        ]);

        // Return error message if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages(),
            ], 400);
        }

        // Get the validated data
        $data = $validator->validated();

        // Store image
        $data['profile_picture'] = Storage::putFileAs('student', $request->file('profile_picture'), time() . '.' . $request->profile_picture->extension());

        // Create the student account
        $account = Student::create($data);

        // Return image link
        $account->profile_picture = Storage::url($account->profile_picture);

        return response()->json([
            'message' => "New student registered.",
            'account' => $account,
        ], 200);
    }

    public function authenticate(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255', 'exists:students,email'],
            'password' => ['required', Password::defaults()],
        ]);

        // Return error message if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages(),
            ], 400);
        }

        // Get the validated data
        $data = $validator->validated();

        // Grab password
        $password = $data->string('password')->toString();

        // Try to authenticate
        $student = Student::where('email', $data->email)->first();
        if (!Hash::check($password, $student->password)) {
            // Failed to authenticate
            return response()->json([
                'errors' => "Password is invalid",
            ], 400);
        }

        // Generate image URL
        $student->profile_picture = Storage::url($student->profile_picture);

        // Authentication attempt successful
        return response()->json([
            'message' => "Logged in as student",
            'account' => $student,
            'token' => $student->createToken('student-api')->plainTextToken, // Generate token
        ], 200);
    }

    public function readOne(Request $request) {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:students,id'],
        ]);

        // Return error message if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->messages(),
            ], 400);
        }

        // Get the validated data
        $data = $validator->validated();

        // Database query
        $student = $students = Student::where('id', $data['id'])
            ->select('id', 'first_name', 'last_name', 'profile_picture', 'email',)
            ->first();

        // Generate image URL
        $student->profile_picture = Storage::url($student->profile_picture);

        // JSON Response
        return response()->json([
            'student' => $student,
        ], 200);
    }

    public function readAll() : JsonResponse {
        // Database query
        $students = Student::select('id', 'first_name', 'last_name', 'profile_picture', 'email',)
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
