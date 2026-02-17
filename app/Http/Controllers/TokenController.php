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
            'username' => ['required', 'string', 'max:255'],
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

        // Check if the username is a registered account
        // Try student
        $query = Student::where('username', $data['username']);
        if ($query->exists()) {
            $account = $query->first();
            $guard = 'student-api';
        }
        else {
            // Try instructor
            $query = Instructor::where('username', $data['username']);
            if ($query->exists()) {
                $account = $query->first();
                $guard = 'instructor-api';
            }

            else {
                // Try admin
                $query = Admin::where('username', $data['username']);
                if ($query->exists()) {
                    $account = $query->first();
                    $guard = 'admin-api';
                }

                // username is invalid
                else {
                    return response()->json([
                        'message' => "Errors detected.",
                        'errors' => [
                            'username' => [
                                "The provided username is invalid."
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

        // Generate profile picture URL
        $account->profile_picture = Storage::url($account->profile_picture);

        // Generate token
        $token = $account->createToken($guard)->plainTextToken;

        // Authentication attempt successful
        return response()->json([
            'message' => "Logged in.",
            'account' => $account,
            'guard' => $guard,
            'token' => $token
        ], 200);
    }

    public function delete(Request $request) : JsonResponse {
        // Delete the currently in-use token of the user making the request
        $request->user()->currentAccessToken()->delete;

        // Confirmation message
        return response()->json([
            'message' => "Logged out.",
            'account' => $request->user()
        ], 200);
    }
}
