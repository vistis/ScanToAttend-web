<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use App\Models\ClassSession;

class CourseController extends Controller
{
    /* ADD COURSE */
    public function create(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:255', 'unique:courses,code'],
            'name' => ['required', 'string', 'max:255', 'unique:courses,name']
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

        // Create course
        $course = Course::create($data);

        return response()->json([
            'message' => "Course added.",
            'course' => $course
        ], 200);
    }

    /* GET COURSE LIST */
    public function readAll() : JsonResponse {
        // Database query
        $courses = Course::select('id', 'code', 'name')->get();

        // Respond as JSON
        return response()->json([
            'message' => "Course list retrieved.",
            'courses' => $courses,
        ], 200);
    }

    /* GET COURSE INFORMATION */
    public function readOne(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:courses,id']
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

        // Fetch data
        $course = Course::find($data['id']);

        // Get classes of course
        $classes = app('App\Http\Controllers\CourseClassController')->readCourse($data['id']);

        // Respond as JSON
        return response()->json([
            'message' => "Course information retrieved.",
            'course' => $course,
            'classes' => $classes
        ], 200);
    }

    /* UPDATE COURSE */
    public function update(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:courses,id'],
            'code' => ['required', 'string', 'max:255', 'unique:courses,code'],
            'name' => ['required', 'string', 'max:255', 'unique:courses,name']
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

        // Update information
        Course::where('id', $data['id'])
            ->update($data);

        // Get the updated information
        $course = Course::find($data['id']);

        // Respond as JSON
        return response()->json([
            'message' => "Course updated.",
            'course' => $course
        ]);
    }

    /* DELETE COURSE */
    public function delete(Request $request) {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:courses,id']
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

        // Find the course
        $course = Course::find($data['id']);

        // Delete the course
        $course->delete();

        // Respond as JSON
        return response()->json([
            'message' => "Course deleted.",
            'course' => $course
        ], 200);
    }
}
