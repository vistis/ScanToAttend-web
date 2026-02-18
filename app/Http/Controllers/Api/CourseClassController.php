<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use App\Models\CourseClass;

class CourseClassController extends Controller
{
    /* ADD CLASS */
    public function create(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'course_id' => ['required', 'integer', 'exists:courses,id']
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

        // Check for existing classes of this course
        $query = CourseClass::where('course_id', $data['course_id']);
        if ($query->exists()) {
            $increment = 1;
            $records = $query->select('section')->orderBy('section')->get();

            foreach ($records as $record) {
                if ($record->section != $increment) {
                    $data['section'] = $increment;
                    break;
                }
                else {
                    $increment++;
                }

                if (!$query->where('section', $record->section + 1)->exists()) {
                    $data['section'] = $record->section + 1;
                    break;
                }
            }
        }
        else {
            $data['section'] = 1;
        }

        // Create class
        $class = CourseClass::create($data);

        return response()->json([
            'message' => "Class added.",
            'class' => $class
        ], 200);
    }

    /* GET CLASSES OF A COURSE */
    public function readAllForCourse(Request $request) : JsonResponse {
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

        // Database query
        $classes = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('courses.id', $data['id'])
            ->select('classes.id as id', 'classes.section as section', 'instructors.id as instructor_id', 'instructors.first_name as instructor_first_name', 'instructors.last_name as instructor_last_name')
            ->orderBy('section')
            ->get();

        // Get class sessions
        foreach ($classes as $class) {
            $class->sessions = getClassSession($class->id);
        }

        // Return data
        return response()->json([
            'message' => "Class list of course with ID " . $data['id'] . " retrived.",
            'classes' => $classes
        ], 200);
    }

    /* GET CLASSES OF AN INSTRUCTOR */
    public function readAllForInstructor(Request $request) : JsonResponse {
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
        $classes = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('instructors.id', $data['id'])
            ->select('classes.id as id', 'courses.id as course_id', 'courses.code as course_code', 'courses.name as course_name', 'classes.section as section')
            ->get();

        // Get class sessions
        foreach ($classes as $class) {
            $class->sessions = getClassSession($class->id);
        }

        // Return data
        return response()->json([
            'message' => "Class list of instructor with ID " . $data['id'] . " retrived.",
            'classes' => $classes
        ], 200);
    }

    /* GET CLASSES AS A INSTRUCTOR */
    public function readAllAsInstructor(Request $request) : JsonResponse {
        // Get user
        $user = $request->user();

        // Database query
        $classes = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('instructors.id', $user->id)
            ->select('classes.id as id', 'courses.id as course_id', 'courses.code as course_code', 'courses.name as course_name', 'classes.section as section')
            ->get();

        // Get class sessions
        foreach ($classes as $class) {
            $class->sessions = getClassSession($class->id);
        }

        // Return data
        return response()->json([
            'message' => "Class list retrived.",
            'classes' => $classes
        ], 200);
    }

    /* GET CLASSES OF A STUDENT */
    public function readAllForStudent(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:students,id']
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
        $classes = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->join('class_registrations', 'classes.id', '=', 'class_registrations.class_id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('class_registrations.student_id', $data['id'])
            ->select('classes.id as id', 'classes.section as section', 'courses.id as course_id', 'courses.code as course_code', 'courses.name as course_name', 'instructors.id as instructor_id', 'instructors.first_name as instructor_first_name', 'instructors.last_name as instructor_last_name')
            ->get();

        // Get class sessions
        foreach ($classes as $class) {
            $class->sessions = getClassSession($class->id);
        }

        // Return data
        return response()->json([
            'message' => "Class list of student with ID " . $data['id'] . " retrived.",
            'classes' => $classes
        ], 200);
    }

    /* GET CLASSES AS A STUDENT */
    public function readAllAsStudent(Request $request) : JsonResponse {
        // Get user
        $user = $request->user();

        // Database query
        $classes = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->join('class_registrations', 'classes.id', '=', 'class_registrations.class_id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('class_registrations.student_id', $user->id)
            ->select('classes.id as id', 'classes.section as section', 'courses.id as course_id', 'courses.code as course_code', 'courses.name as course_name', 'instructors.id as instructor_id', 'instructors.first_name as instructor_first_name', 'instructors.last_name as instructor_last_name')
            ->get();

        // Get class sessions
        foreach ($classes as $class) {
            $class->sessions = getClassSession($class->id);
        }

        // Return data
        return response()->json([
            'message' => "Class list retrived.",
            'classes' => $classes
        ], 200);
    }

    /* GET CLASS INFORMATION */
    public function readOne(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:classes,id']
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
        $class = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('classes.id', $data['id'])
            ->select('classes.id as id', 'courses.id as course_id', 'courses.code as course_code', 'courses.name as course_name', 'classes.section as section', 'instructors.id as instructor_id', 'instructors.first_name as instructor_first_name', 'instructors.last_name as instructor_last_name', 'classes.created_at as created_at', 'classes.updated_at as updated_at')
            ->first();

        // Get class sessions
        $class->sessions = getClassSession($data['id']);

        // Respond as JSON
        return response()->json([
            'message' => "Class information retrieved.",
            'class' => $class
        ], 200);
    }

    /* ASSIGN INSTRUCTOR TO CLASS */
    public function updateAssign(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:classes,id'],
            'instructor_id' => ['integer', 'exists:instructors,id']
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

        // Check for overlapping instructor schedule
        $sessions = getClassSession($data['id']);

        foreach ($sessions as $session) {
            if (CourseClass::join('class_sessions', 'class_sessions.class_id', '=', 'classes.id')
                ->where('classes.instructor_id', $data['instructor_id'])
                ->where('class_sessions.day', $session->day)
                ->where(function (Builder $query) use ($session) {
                    $query->where('class_sessions.start_at', '<', $session->end_at)
                        ->where('class_sessions.end_at', '>', $session->start_at);
                })
                ->exists()
            ) {
                return response()->json([
                    'message' => "Errors detected.",
                    'errors' => [
                        'class_session' => [
                            "The instructor schedule conflicts with this class."
                        ]
                    ]
                ], 400);
            }
        }

        // Update information
        CourseClass::where('id', $data['id'])
            ->update($data);

        // Get the updated information
        $class = CourseClass::find($data['id']);

        // Respond as JSON
        return response()->json([
            'message' => "Class updated.",
            'class' => $class
        ]);
    }

    /* UNASSIGN INSTRUCTOR FROM CLASS */
    public function updateUnassign(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:classes,id']
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

        // Set instructor to null
        CourseClass::where('id', $data['id'])
            ->update([
                'instructor_id' => null
            ]);

        // Get the updated information
        $class = CourseClass::find($data['id']);

        // Respond as JSON
        return response()->json([
            'message' => "Class updated.",
            'class' => $class
        ]);
    }

    /* DELETE CLASS */
    public function delete(Request $request) {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:classes,id']
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
        $class = CourseClass::find($data['id']);

        // Delete the course
        $class->delete();

        // Respond as JSON
        return response()->json([
            'message' => "Course deleted.",
            'course' => $class
        ], 200);
    }
}
