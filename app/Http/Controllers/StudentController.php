<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\Username;

class StudentController extends Controller
{
    /* RGISTER STUDENT */
    public function create(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'fingerprint_id' => ['required', 'integer', 'unique:students,fingerprint_id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'profile_picture' => ['required', 'image', 'max:2048'],
            'password' => ['required', Password::defaults()]
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

        // Generate username
        $data['username'] = app('App\Http\Controllers\UsernameController')
            ->create($data['first_name'], $data['last_name']);

        // Generate email
        $data['email'] = app('App\Http\Controllers\EmailController')
            ->create($data['username']);

        // Store image
        $data['profile_picture'] = Storage::putFileAs('student', $request->file('profile_picture'), time() . '.' . $request->profile_picture->extension());

        // Create the student account
        $account = Student::create($data);

        // Return image link
        $account->profile_picture = Storage::url($account->profile_picture);

        return response()->json([
            'message' => "Student registered.",
            'student' => $account
        ], 200);
    }

    /* STUDENT LIST */
    public function readAll() : JsonResponse {
        // Database query
        $accounts = Student::select('id', 'first_name', 'last_name', 'profile_picture', 'email')
            ->orderByDesc('first_name')
            ->get();

        // Generate URL for profile picture
        foreach ($accounts as $account) {
            $account->profile_picture = Storage::url($account->profile_picture);
        }

        // JSON Response
        return response()->json([
            'meesage' => "Students list retrieved.",
            'students' => $accounts
        ], 200);
    }

    /* STUDENT INFORMATION */
    public function readOne(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:students,id']
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

        // Database query
        $account = $students = Student::find($data['id']);

        // Generate image URL
        $account->profile_picture = Storage::url($account->profile_picture);

        // JSON Response
        return response()->json([
            'message' => "Student list retrieved.",
            'student' => $account
        ], 200);
    }

    /* UPDATE STUDENT */
    public function update(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:students,id'],
            'fingerprint_id' => ['integer', 'unique:students,fingerprint_id'],
            'first_name' => ['string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'profile_picture' => ['image', 'max:2048'],
            'password' => [Password::defaults()]
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

        // If the request contain a new profile picture
        if ($request->hasFile('profile_picture')) {
            // Store new picture
            $data['profile_picture'] = Storage::putFileAs('student', $request->file('profile_picture'), time() . '.' . $request->profile_picture->extension());

            // Delete old picture
            Storage::delete(Student::find($data['id'])->profile_picture);
        }

        // Update student account
        Student::where('id', $data['id'])
            ->update($data);

        // Get the information of the new account
        $account = Student::find($data['id']);

        // Generate profile picture URL
        $account->profile_picture = Storage::url($account->profile_picture);

        // Respond as JSON
        return response()->json([
            'message' => "Student updated.",
            'student' => $account
        ], 200);
    }

    /* DELETE STUDENT */
    public function delete(Request $request) {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:students,id']
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

        // Get the account
        $account = Student::find($data['id']);

        // Delete profile picture file
        Storage::delete($account->profile_picture);

        // Delete account from database
        $account->delete();

        // Respond as JSON
        return response()->json([
            'message' => "Student deleted.",
            'student' => $account
        ], 200);
    }
}
