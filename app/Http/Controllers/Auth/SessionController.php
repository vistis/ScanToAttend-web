<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Instructor;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class SessionController extends Controller
{
    /**
     * Authenticate a user via session (Sanctum SPA / cookie auth).
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

        /** Log the user in via the session guard. */
        Auth::guard($guard)->login($account);
        $request->session()->regenerate();

        return response()->json([
            'message' => "Logged in.",
            'guard' => $guard,
        ], 200);
    }

    /**
     * Log the user out by invalidating the session.
     */
    public function delete(Request $request): JsonResponse
    {
        /** Find whichever guard is authenticated and log out. */
        foreach (['student', 'instructor', 'admin'] as $guard)
        {
            if (Auth::guard($guard)->check())
            {
                Auth::guard($guard)->logout();
                break;
            }
        }

        return response()->json([
            'message' => "Logged out."
        ], 200);
    }
}
