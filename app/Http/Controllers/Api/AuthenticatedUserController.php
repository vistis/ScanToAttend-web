<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuthenticatedUserController extends Controller
{
    /**
     * Get the information of the authenticated user.
     */
    public function read(Request $request): JsonResponse
    {
        /** Get the user behind the request. */
        $account = $request->user();

        /** Determine which guard authenticated this user. */
        $guard = null;
        foreach (['student', 'instructor', 'admin'] as $g)
        {
            if (Auth::guard($g)->check())
            {
                $guard = $g;
                break;
            }
        }

        /** Generate URL for profile picture. */
        if ($account->profile_picture)
        {
            $account->profile_picture = Storage::url($account->profile_picture);
        }

        if (!$guard)
        {
            /** Non SPA response. */
            return response()->json([
                'message' => "Retrived information of current user.",
                'user' => $account
            ], 200);
        }
        else {
            /** Return flat user object with guard for SPA. */
            $userData = $account->toArray();
            $userData['guard'] = $guard;

            return response()->json($userData, 200);
        }
    }
}
