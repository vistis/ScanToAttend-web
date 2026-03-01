<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseClassController extends Controller
{
    /**
     * Add a class.
     */
    public function create(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'course_id' => ['required', 'integer', 'exists:courses,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Check for existing classes of this course. */
        $query = CourseClass::where('course_id', $data['course_id']);

        if ($query->exists())
        {
            $increment = 1;
            $records = $query->select('section')->orderBy('section')->get();

            foreach ($records as $record)
            {
                if ($record->section != $increment)
                {
                    $data['section'] = $increment;
                    break;
                }
                else
                {
                    $increment++;
                }

                if (!$query->where('section', $record->section + 1)->exists())
                {
                    $data['section'] = $record->section + 1;
                    break;
                }
            }
        }
        else {
            $data['section'] = 1;
        }

        /** Create class. */
        $class = CourseClass::create($data);

        return response()->json([
            'message' => "Class added."
        ], 200);
    }

    /**
     * Get list of class of a course.
     */
    public function readListOfCourse(Request $request): JsonResponse
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

        /** Get the class list. */
        $classes = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('courses.id', $data['id'])
            ->select(
                'classes.id as id',
                'classes.section as section',
                'instructors.id as instructor_id',
                'instructors.first_name as instructor_first_name',
                'instructors.last_name as instructor_last_name'
            )
            ->orderBy('section')
            ->get();

        /** Get sessions of each class. */
        foreach ($classes as $class)
        {
            $class->sessions = getClassSessions($class->id);
        }

        return response()->json([
            'message' => "Class list retrieved.",
            'classes' => $classes
        ], 200);
    }

    /**
     * Get list of class an instructor teaches.
     */
    public function readListOfInstructor(Request $request): JsonResponse
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

        /** Get the class list. */
        $classes = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('instructors.id', $data['id'])
            ->select(
                'classes.id as id',
                'courses.id as course_id',
                'courses.code as course_code',
                'courses.name as course_name',
                'classes.section as section'
            )
            ->orderBy('course_code')
            ->get();

        /** Get the sessions of each class. */
        foreach ($classes as $class)
        {
            $class->sessions = getClassSessions($class->id);
        }

        return response()->json([
            'message' => "Class list retrieved.",
            'classes' => $classes
        ], 200);
    }

    /**
     * Get the class list associated with the authenticated instructor.
     */
    public function readListAsInstructor(Request $request): JsonResponse
    {
        /** Get the instructor behind the request */
        $user = $request->user();

        /** Get the class list. */
        $classes = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('instructors.id', $user->id)
            ->select(
                'classes.id as id',
                'courses.id as course_id',
                'courses.code as course_code',
                'courses.name as course_name',
                'classes.section as section'
            )
            ->orderBy('course_code')
            ->get();

        /** Get the sessions of each class. */
        foreach ($classes as $class)
        {
            $class->sessions = getClassSessions($class->id);
        }

        return response()->json([
            'message' => "Class list retrieved.",
            'classes' => $classes
        ], 200);
    }

    /**
     * Get the class list of a student that they are registered in.
     */
    public function readListOfStudent(Request $request): JsonResponse
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

        /** Get the class list. */
        $classes = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->join('class_registrations', 'classes.id', '=', 'class_registrations.class_id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('class_registrations.student_id', $data['id'])
            ->select(
                'classes.id as id',
                'classes.section as section',
                'courses.id as course_id',
                'courses.code as course_code',
                'courses.name as course_name',
                'instructors.id as instructor_id',
                'instructors.first_name as instructor_first_name',
                'instructors.last_name as instructor_last_name'
            )
            ->orderBy('course_code')
            ->get();

        /** Get the sessions of each class. */
        foreach ($classes as $class)
        {
            $class->sessions = getClassSessions($class->id);
        }

        return response()->json([
            'message' => "Class list retrieved.",
            'classes' => $classes
        ], 200);
    }

    /**
     * Get the class list associated with the authenticated student.
     */
    public function readListAsStudent(Request $request): JsonResponse
    {
        /** Get student behind the request. */
        $user = $request->user();

        /** Get the class list. */
        $classes = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->join('class_registrations', 'classes.id', '=', 'class_registrations.class_id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('class_registrations.student_id', $user->id)
            ->select(
                'classes.id as id',
                'classes.section as section',
                'courses.id as course_id',
                'courses.code as course_code',
                'courses.name as course_name',
                'instructors.id as instructor_id',
                'instructors.first_name as instructor_first_name',
                'instructors.last_name as instructor_last_name'
            )
            ->orderBy('course_code')
            ->get();

        /** Get the sessions of each class */
        foreach ($classes as $class)
        {
            $class->sessions = getClassSessions($class->id);
        }

        return response()->json([
            'message' => "Class list retrived.",
            'classes' => $classes
        ], 200);
    }

    /**
     * Get the information of a specific class.
     */
    public function read(Request $request): JsonResponse
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

        /** Get the class. */
        $class = CourseClass::join('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('instructors', 'instructors.id', '=', 'classes.instructor_id')
            ->where('classes.id', $data['id'])
            ->select('classes.id as id', 'courses.id as course_id', 'courses.code as course_code', 'courses.name as course_name', 'classes.section as section', 'instructors.id as instructor_id', 'instructors.first_name as instructor_first_name', 'instructors.last_name as instructor_last_name', 'classes.created_at as created_at', 'classes.updated_at as updated_at')
            ->first();

        /** Get the sessions of the class. */
        $class->sessions = getClassSessions($data['id']);

        return response()->json([
            'message' => "Class information retrieved.",
            'class' => $class
        ], 200);
    }

    /**
     * Assign an instructor to a class.
     */
    public function updateAssign(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:classes,id'],
            'instructor_id' => ['required', 'integer', 'exists:instructors,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Check for overlaps with instructor schedule. */
        $sessions = getClassSessions($data['id']);

        foreach ($sessions as $session)
        {
            if (CourseClass::join('class_sessions', 'class_sessions.class_id', '=', 'classes.id')
                ->where('classes.instructor_id', $data['instructor_id'])
                ->where('class_sessions.day', $session->day)
                ->where(function (Builder $query) use ($session)
                {
                    $query->where('class_sessions.start_at', '<', $session->end_at)
                        ->where('class_sessions.end_at', '>', $session->start_at);
                })
                ->exists()
            )
            {
                return response()->json([
                    'message' => "The instructor schedule conflicts with this class."
                ], 409);
            }
        }

        /** Update the class instructor. */
        CourseClass::where('id', $data['id'])
            ->update($data);

        return response()->json([
            'message' => "Assigned instructor to class."
        ]);
    }

    /**
     * Set class to having no instructor.
     */
    public function updateUnassign(Request $request): JsonResponse
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

        /** Set instructor to null. */
        CourseClass::where('id', $data['id'])
            ->update([
                'instructor_id' => null
            ]);

        return response()->json([
            'message' => "The class now has no instructor.",
        ], 200);
    }

    /**
     * Remove a class (also remove its sessions, and registration record.)
     */
    public function delete(Request $request): JsonResponse
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

        /** Delete the class.. */
        CourseClass::where('id', $data['id'])->delete();

        return response()->json([
            'message' => "Class deleted."
        ], 200);
    }
}
