<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Instructor;

class InstructorController extends Controller
{
    /* ADD INSTRUCTOR */
    public function create(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
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
        $data['profile_picture'] = Storage::putFileAs('instructor', $request->file('profile_picture'), $data['username'] . '-' . time() . '.' . $request->profile_picture->extension());

        // Create the instructor account
        $account = Instructor::create($data);

        // Return image link
        $account->profile_picture = Storage::url($account->profile_picture);

        return response()->json([
            'message' => "Instructor added.",
            'instructor' => $account
        ], 200);
    }

    /* INSTRUCTOR LIST */
    public function readAll() : JsonResponse {
        // Database query
        $accounts = Instructor::select('id', 'first_name', 'last_name', 'profile_picture', 'email')
            ->orderByDesc('first_name')
            ->get();

        // Generate URL for profile picture
        foreach ($accounts as $account) {
            $account->profile_picture = Storage::url($account->profile_picture);
        }

        // JSON Response
        return response()->json([
            'meesage' => "Instructors list retrieved.",
            'instructor' => $accounts
        ], 200);
    }

    /* INSTRUCTOR INFORMATION */
    public function readOne(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:instructors,id']
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
        $account = Instructor::find($data['id']);

        // Generate image URL
        $account->profile_picture = Storage::url($account->profile_picture);

        // JSON Response
        return response()->json([
            'message' => "Instructor information retrieved.",
            'instructor' => $account
        ], 200);
    }

    /* UPDATE INSTRUCTOR */
    public function update(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:instructors,id'],
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
        $account = Instructor::find($data['id']);

        // If the request contain a new profile picture
        if ($request->hasFile('profile_picture')) {
            // Store new picture
            $data['profile_picture'] = Storage::putFileAs('instructor', $request->file('profile_picture'), $account->username . '-' . time() . '.' . $request->profile_picture->extension());

            // Delete old picture
            Storage::delete(Instructor::find($data['id'])->profile_picture);
        }

        // Update instructor account
        $account->update($data);

        // Grab the updated account
        $account = Instructor::find($data['id']);

        // Generate profile picture URL
        $account->profile_picture = Storage::url($account->profile_picture);

        // Respond as JSON
        return response()->json([
            'message' => "Instructor updated.",
            'instructor' => $account
        ], 200);
    }

    /* DELETE INSTRUCTOR */
    public function delete(Request $request) {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:instructors,id']
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
        $account = Instructor::find($data['id']);

        // Delete profile picture file
        Storage::delete($account->profile_picture);

        // Delete account from database
        $account->delete();

        // Respond as JSON
        return response()->json([
            'message' => "Instructor deleted.",
            'instructor' => $account
        ], 200);
    }
}
