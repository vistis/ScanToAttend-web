<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRegistration;
use App\Models\CourseClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassRegistrationController extends Controller
{
    /**
     * Register a student into a class.
     */
    public function create(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Check if the student is already registered in this course. */
        $courseId = CourseClass::find($data['class_id'])->course_id;

        if (ClassRegistration::join('classes', 'class_registrations.class_id', '=', 'classes.id')
            ->join('courses', 'classes.course_id', '=', 'courses.id')
            ->where('class_registrations.student_id', $data['student_id'])
            ->where('courses.id', $courseId)
            ->exists()
        )
        {
            return response()->json([
                'message' => "Student is already registered in a class of the same course."
            ], 409);
        }

        /** Check if the student has schedule overlaps. */
        $sessions = getClassSessions($data['class_id']);

        foreach ($sessions as $session)
        {
            if (ClassRegistration::join('class_sessions', 'class_registrations.class_id', '=', 'class_sessions.class_id')
                ->where('class_registrations.student_id', $data['student_id'])
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
                    'message' => "The student's schedule conflicts with this class."
                ], 409);
            }
        }

        /** Add the registration detail into the record. */
        ClassRegistration::create($data);

        return response()->json([
            'message' => "Registered student into class.",
        ], 200);
    }

    /**
     * Unregister a student from a given class.
     */
    public function delete(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Find the relation in the registration record. */
        $record = ClassRegistration::where('student_id', $data['student_id'])
            ->where('class_id', $data['class_id'])->first();

        if (!$record)
        {
            return response()->json([
                'message' => "The student is not registered in this class."
            ], 404);
        }

        /** Delete the relation from the record. */
        $record->delete();

        return response()->json([
            'message' => "Unregistered student from class.",
        ], 200);
    }
}
