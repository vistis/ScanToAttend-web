<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Instructor;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class TokenController extends Controller
{
    /**
     * Authenticate a user by providing a token.
     */
    public function create(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', Password::defaults()]
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Find the user with this username. */
        $query = Student::where('username', $data['username']);

        if ($query->exists())
        {
            $guard = 'student';
        }
        else
        {
            $query = Instructor::where('username', $data['username']);

            if ($query->exists())
            {
                $guard = 'instructor';
            }
            else
            {
                $query = Admin::where('username', $data['username']);
                if ($query->exists())
                {
                    $guard = 'admin';
                }
                else {
                    return response()->json([
                        'message' => "User not found."
                    ], 404);
                }
            }
        }

        $account = $query->first();

        /** Check password. */
        if (!Hash::check($data['password'], $account->password))
        {
            return response()->json([
                'message' => "Invalid password."
            ], 400);
        }

        /** Generate access token. */
        $token = $account->createToken($guard)->plainTextToken;

        return response()->json([
            'message' => "Logged in.",
            'guard' => $guard,
            'token' => $token
        ], 200);
    }

    /**
     * Invalidate an access token.
     */
    public function delete(Request $request): JsonResponse
    {
        /** Delete the currently in-use token of the user making the request */
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => "Logged out."
        ], 200);
    }
}
