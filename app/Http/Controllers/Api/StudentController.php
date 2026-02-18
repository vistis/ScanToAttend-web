<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class StudentController extends Controller
{
    /* REGISTER STUDENT */
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
        $data['username'] = generateUsername($data['first_name'], $data['last_name']);

        // Generate email
        $data['email'] = generateEmail($data['username']);

        // Store image
        $data['profile_picture'] = Storage::putFileAs('student', $request->file('profile_picture'), $data['username'] . '-' . time() . '.' . $request->profile_picture->extension());

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
        $account = Student::find($data['id']);

        // Generate image URL
        $account->profile_picture = Storage::url($account->profile_picture);

        // JSON Response
        return response()->json([
            'message' => "Student information retrieved.",
            'student' => $account
        ], 200);
    }

    /* UPDATE STUDENT */
    public function update(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:students,id'],
            'fingerprint_id' => ['integer', 'unique:students,fingerprint_id'],
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

        // Find the account
        $account = Student::find($data['id']);

        // If the request contain a new profile picture
        if ($request->hasFile('profile_picture')) {
            // Store new picture
            $data['profile_picture'] = Storage::putFileAs('student', $request->file('profile_picture'), $account->username . '-' . time() . '.' . $request->profile_picture->extension());

            // Delete old picture
            Storage::delete($account->profile_picture);
        }

        // Hashify the password
        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        }

        // Update student account
        $account->update($data);

        // Grab the updated account
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
