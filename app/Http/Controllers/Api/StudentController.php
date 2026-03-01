<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class StudentController extends Controller
{
    /**
     * Add a student account,
     */
    public function create(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'fingerprint_id' => ['nullable', 'integer', 'unique:students,fingerprint_id'],
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

        /** Set the data property for the account. */
        $data = $validator->validated();
        $data['username'] = generateUsername($data['first_name'], $data['last_name']);
        $data['email'] = generateEmail($data['username']);
        $data['profile_picture'] = Storage::putFileAs('student',
            $request->file('profile_picture'),
            $data['username'] . '-' . time() . '.' . $request->profile_picture->extension()
        );

        /** Create the account. */
        Student::create($data);

        return response()->json([
            'message' => "Student account added."
        ], 200);
    }

    /**
     * Get the list of all student.
     */
    public function readList(): JsonResponse
    {
        $accounts = Student::select('id', 'fingerprint_id', 'first_name', 'last_name', 'profile_picture', 'email')
            ->orderByDesc('first_name')
            ->get();

            /** Generate URL for profile picture of each student. */
        foreach ($accounts as $account)
        {
            $account->profile_picture = Storage::url($account->profile_picture);
        }

        return response()->json([
            'meesage' => "Student list retrieved.",
            'students' => $accounts
        ], 200);
    }

    /**
     * Get the information of a student.
     */
    public function read(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:students,id']
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
        $account = Student::find($data['id']);

        /** Generate URL for profile picture. */
        $account->profile_picture = Storage::url($account->profile_picture);

        return response()->json([
            'message' => "Student information retrieved.",
            'student' => $account
        ], 200);
    }

    /**
     * Get the list of student registered in a given class as an authenticated instructor.
     */
    public function readListOfClassAsInstructor(Request $request): JsonResponse
    {
        /** Validate request */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:classes,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data */
        $data = $validator->validated();

        /** Get the instructor behind the request. */
        $instructor = $request->user();

        /** Check if the instructor teaches this class. */
        if (!CourseClass::where('id', $data['id'])
            ->where('instructor_id', $instructor->id)
            ->exists()
        )
        {
            return response()->json([
                'message' => "You do not have access to this class."
            ], 401);
        }

        /** Get the student list. */
        $students = Student::join('class_registrations', 'class_registrations.student_id', '=', 'students.id')
            ->join('classes', 'class_registrations.class_id', '=', 'classes.id')
            ->where('classes.id', $data['id'])
            ->select('students.id as id', 'students.first_name as first_name', 'students.last_name as last_name', 'students.profile_picture as profile_picture', 'students.email as email')
            ->orderBy('students.username')
            ->get();

        /** Generate URL for the profile picture of each student. */
        foreach ($students as $student)
        {
            $student->profile_picture = Storage::url($student->profile_picture);
        }

        return response()->json([
            'message' => "Student list retrieved.",
            'students' => $students
        ], 200);
    }

    /**
     * Get the list of student registered in a given class as an authenticated admin.
     */
    public function readListOfClassAsAdmin(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:classes,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Get the student list. */
        $students = Student::join('class_registrations', 'class_registrations.student_id', '=', 'students.id')
            ->join('classes', 'class_registrations.class_id', '=', 'classes.id')
            ->where('classes.id', $data['id'])
            ->select('students.id as id', 'students.first_name as first_name', 'students.last_name as last_name', 'students.profile_picture as profile_picture', 'students.email as email')
            ->orderBy('students.username')
            ->get();

        /** Generate URL for the profile picture of each student. */
        foreach ($students as $student)
        {
            $student->profile_picture = Storage::url($student->profile_picture);
        }

        return response()->json([
            'message' => "Student list retrieved.",
            'students' => $students
        ], 200);
    }

    /**
     * Update the profile picture and/or password and/or fingerprint of a student account.
     */
    public function update(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:students,id'],
            'fingerprint_id' => ['integer', 'unique:students,fingerprint_id'],
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
        $account = Student::find($data['id']);

        /** If update the profile picture, delete the old one. */
        if ($request->hasFile('profile_picture'))
        {
            $data['profile_picture'] = Storage::putFileAs('student',
                $request->file('profile_picture'),
                $account->username . '-' . time() . '.' . $request->profile_picture->extension()
            );

            if ($account->profile_picture != "user_default.svg")
            {
                Storage::delete($account->profile_picture);
            }
        }

        /** Hashify the password */
        if ($request->password)
        {
            $data['password'] = Hash::make($data['password']);
        }

        /** Update the account. */
        $account->update($data);

        return response()->json([
            'message' => "Student account updated."
        ], 200);
    }

    /**
     * Remove an student account.
     */
    public function delete(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:students,id']
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
        $account = Student::find($data['id']);

        /** Delete the profile picture file. */
        if ($account->profile_picture != "user_default.svg")
        {
            Storage::delete($account->profile_picture);
        }

        /** Delete the account from database. */
        $account->delete();

        return response()->json([
            'message' => "Student account removed."
        ], 200);
    }
}
