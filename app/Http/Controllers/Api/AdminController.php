<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Update the profile picture of the authenticated account.
     */
    public function updateProfilePicture(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'profile_picture' => ['required', 'image', 'max:2048']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Get the user behind the request. */
        $user = $request->user();

        /** Store the new profile picture and delete the old one. */
        $data['profile_picture'] = Storage::putFileAs('admin',
            $request->file('profile_picture'),
            $user->username . '-' . time() . '.' . $request->profile_picture->extension()
        );

        if (!$user->profile_picture || $user->profile_picture != "user_default.svg")
        {
            Storage::delete($user->profile_picture);
        }

        /** Update the account. */
        Admin::where('id', $user->id)->update($data);

        return response()->json([
            'message' => "Profile picture updated.",
        ], 200);
    }
}
