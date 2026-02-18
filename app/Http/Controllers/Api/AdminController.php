<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /* UPDATE PROFILE PICTURE */
    public function updateProfilePicture(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:students,id'],
            'profile_picture' => ['required', 'image', 'max:2048']
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
        $account = Admin::find($data['id']);

        // Store new picture
        $data['profile_picture'] = Storage::putFileAs('student', $request->file('profile_picture'), $account->username . '-' . time() . '.' . $request->profile_picture->extension());

        // Delete old picture
        if (!$account->profile_picture) {
            Storage::delete($account->profile_picture);
        }

        // Update admin account
        $account->update($data);

        // Grab the updated account
        $account = Admin::find($data['id']);

        // Generate profile picture URL
        $account->profile_picture = Storage::url($account->profile_picture);

        // Respond as JSON
        return response()->json([
            'message' => "Admin updated.",
            'admin' => $account
        ], 200);
    }
}
