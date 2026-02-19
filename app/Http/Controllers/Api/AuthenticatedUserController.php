<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        /** Generate URL for profile picture. */
        if ($account->profile_picture)
        {
            $account->profile_picture = Storage::url($account->profile_picture);
        }

        return response()->json([
            'message' => "Retrived information of current user.",
            'user' => $account
        ], 200);
    }
}
