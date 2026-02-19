<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class InstructorController extends Controller
{
    /**
     * Add an instructor account.
     */
    public function create(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'profile_picture' => ['required', 'image', 'max:2048'],
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

        /** Set the data property for the account. */
        $data['username'] = generateUsername($data['first_name'], $data['last_name']);
        $data['email'] = generateEmail($data['username']);
        $data['profile_picture'] = Storage::putFileAs('instructor',
            $request->file('profile_picture'),
            $data['username'] . '-' . time() . '.' . $request->profile_picture->extension()
        );

        /** Create the account. */
        Instructor::create($data);

        return response()->json([
            'message' => "Instructor account added."
        ], 200);
    }

    /**
     * Get the list of all instructor.
     */
    public function readList(): JsonResponse
    {
        $accounts = Instructor::select('id', 'first_name', 'last_name', 'profile_picture', 'email')
            ->orderByDesc('first_name')
            ->get();

        /** Generate URL for profile picture of each instructor. */
        foreach ($accounts as $account)
        {
            $account->profile_picture = Storage::url($account->profile_picture);
        }

        return response()->json([
            'meesage' => "Instructor list retrieved.",
            'instructor' => $accounts
        ], 200);
    }

    /**
     * Get the information of an instructor.
     */
    public function read(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:instructors,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Fetch the information. */
        $account = Instructor::find($data['id']);

        /** Generate URL for profile picture. */
        $account->profile_picture = Storage::url($account->profile_picture);

        return response()->json([
            'message' => "Instructor information retrieved.",
            'instructor' => $account
        ], 200);
    }

    /**
     * Update the profile picture and/or password of an instructor account.
     */
    public function update(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:instructors,id'],
            'profile_picture' => ['image', 'max:2048'],
            'password' => [Password::defaults()]
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Find the account. */
        $account = Instructor::find($data['id']);

        /** If update the profile picture, delete the old one. */
        if ($request->hasFile('profile_picture'))
        {
            $data['profile_picture'] = Storage::putFileAs('instructor',
                $request->file('profile_picture'),
                $account->username . '-' . time() . '.' . $request->profile_picture->extension()
            );
            Storage::delete($account->profile_picture);
        }

        /** Hashify the password */
        if ($request->password)
        {
            $data['password'] = Hash::make($data['password']);
        }

        /** Update the account. */
        $account->update($data);

        return response()->json([
            'message' => "Instructor account updated."
        ], 200);
    }

    /**
     * Remove an instructor account.
     */
    public function delete(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:instructors,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Find the account. */
        $account = Instructor::find($data['id']);

        /** Delete the profile picture file. */
        Storage::delete($account->profile_picture);

        /** Delete the account from database. */
        $account->delete();

        return response()->json([
            'message' => "Instructor account removed."
        ], 200);
    }
}
