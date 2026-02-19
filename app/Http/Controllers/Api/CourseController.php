<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Add a course.
     */
    public function create(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:255', 'unique:courses,code'],
            'name' => ['required', 'string', 'max:255', 'unique:courses,name']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Add course to database. */
        Course::create($data);

        return response()->json([
            'message' => "Course added."
        ], 200);
    }

    /**
     * Get the list of courses.
     */
    public function readList(): JsonResponse
    {
        $courses = Course::select('id', 'code', 'name')->get();

        return response()->json([
            'message' => "Course list retrieved.",
            'courses' => $courses,
        ], 200);
    }

    /**
     * Get information of a course.
     */
    public function read(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:courses,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Retrieve the information. */
        $course = Course::find($data['id']);

        return response()->json([
            'message' => "Course information retrieved.",
            'course' => $course
        ], 200);
    }

    /**
     * Update the information of a course.
     */
    public function update(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:courses,id'],
            'code' => ['string', 'max:255', 'unique:courses,code'],
            'name' => ['string', 'max:255', 'unique:courses,name']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Update the course information. */
        Course::where('id', $data['id'])
            ->update($data);

        return response()->json([
            'message' => "Course updated."
        ]);
    }

    /**
     * Remove a course (also remove its classes).
     */
    public function delete(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:courses,id']
        ]);


        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Delete the course from the database. */
        Course::where('id', $data['id'])->delete();

        return response()->json([
            'message' => "Course removed."
        ], 200);
    }
}
