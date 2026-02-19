<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\CourseClass;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassSessionController extends Controller
{
    /**
     * Add a session to a class.
     */
    public function create(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'day' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'start_at' => ['required', 'date_format:H:i:s'],
            'end_at' => ['required', 'date_format:H:i:s']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Parse the time. */
        $startAt = Carbon::parse($data['start_at']);
        $endAt = Carbon::parse($data['end_at']);
        $data['start_at'] = $startAt->toTimeString();
        $data['end_at'] = $endAt->toTimeString();

        /** Convert the time to seconds for calculation. */
        $startAtStamp = ($startAt->hour * 3600) + ($startAt->minute * 60) + $startAt->second;
        $endAtStamp = ($endAt->hour * 3600) + ($endAt->minute * 60) + $endAt->second;

        /** Ensure session last at least 50 minutes. */
        if ($endAtStamp - $startAtStamp < 50 * 60)
        {
            return response()->json([
                'message' => "Session must be at least 50 minutes long."
            ], 400);
        }

        /** Check for overlapping timeslot with other sessions of the same class. */
        if (ClassSession::where('class_id', $data['class_id'])
            ->where('day', '=', $data['day'])
            ->where(function (Builder $query) use ($data)
            {
                $query->where('start_at', '<', $data['end_at'])
                    ->where('end_at', '>', $data['start_at']);
            })
            ->exists()
        )
        {
            return response()->json([
                'message' => "Timeslot overlaps with another session of the same class."
            ], 409);
        }

        /** Check if the session overlaps with the schedule of the assigned class instructor. */
        $instructorId = CourseClass::find($data['class_id'])->instructor_id;

        if ($instructorId)
        {
            if (ClassSession::join('classes', 'class_sessions.class_id', '=', 'classes.id')
                ->where('classes.instructor_id', $instructorId)
                ->where('class_sessions.day', $data['day'])
                ->where(function (Builder $query) use ($data)
                {
                    $query->where('class_sessions.start_at', '<', $data['end_at'])
                        ->where('class_sessions.end_at', '>', $data['start_at']);
                })
                ->exists()
            )
            {
                return response()->json([
                    'message' => "The instructor schedule conflicts with this session."
                ], 409);
            }
        }

        /** Check if the session overlaps with the schedule of students. */
        $students = CourseClass::join('class_registrations', 'class_registrations.class_id', '=', 'classes.id')
            ->where('classes.id', $data['class_id'])
            ->select('class_registrations.student_id as id')
            ->get();

        foreach ($students as $student)
        {
            if (ClassSession::join('class_registrations', 'class_registrations.class_id', '=', 'class_sessions.class_id')
                ->where('class_registrations.student_id', $student->id)
                ->where('class_sessions.day', $data['day'])
                ->where(function (Builder $query) use ($data)
                {
                    $query->where('class_sessions.start_at', '<', $data['end_at'])
                        ->where('class_sessions.end_at', '>', $data['start_at']);
                })
                ->exists()
            )
            {
                return response()->json([
                    'message' => "The new session conflicts with registered students' schedule."
                ], 409);
            }
        }

        /** Add the session. */
        ClassSession::create($data);

        return response()->json([
            'message' => "Session added."
        ], 200);
    }

    /**
     * Remove a session (also remove attendance record of this session).
     */
    public function delete(Request $request): JsonResponse
    {
        /** Validate request. */
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:class_sessions,id']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Delete the session. */
        ClassSession::where('id', $data['id'])->delete();

        return response()->json([
            'message' => "Session removed."
        ], 200);
    }
}
