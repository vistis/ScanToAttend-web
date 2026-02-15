<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Admin;

class TokenController extends Controller
{
    public function create(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
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

        // Check if the email is a registered account
        // Try student
        $query = Student::where('email', $data['email']);
        if ($query->exists()) {
            $account = $query->first();
            $guard = 'student-api';
        }
        else {
            // Try instructor
            $query = Instructor::where('email', $data['email']);
            if ($query->exists()) {
                $account = $query->first();
                $guard = 'instructor-api';
            }

            else {
                // Try admin
                $query = Admin::where('email', $data['email']);
                if ($query->exists()) {
                    $account = $query->first();
                    $guard = 'admin-api';
                }

                // Email is invalid
                else {
                    return response()->json([
                        'message' => "Errors detected.",
                        'errors' => [
                            'email' => [
                                "The provided email is invalid."
                            ]
                        ]
                    ], 400);
                }
            }
        }

        // Check password
        if (!Hash::check($data['password'], $account->password)) {
            // Failed to authenticate
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'password' => [
                        "The provided password is invalid."
                    ]
                ]
            ], 400);
        }

        // Generate image URL
        $account->profile_picture = Storage::url($account->profile_picture);

        // Generate token
        $token = $account->createToken($guard)->plainTextToken;

        // Authentication attempt successful
        return response()->json([
            'message' => "Logged in.",
            'account' => $account,
            'token' => $token
        ], 200);
    }

    public function delete(Request $request) : JsonResponse {
        // Delete the currently in-use token of the user making the request
        $request->user()->currentAccessToken()->delete;

        return response()->json([
            'message' => 'Logged out.'
        ], 200);
    }
}
