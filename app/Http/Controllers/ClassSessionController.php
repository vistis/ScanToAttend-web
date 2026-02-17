<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ClassSession;
use Carbon\Carbon;

class ClassSessionController extends Controller
{
    /* ADD SESSION TO A CLASS */
    public function create(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'day' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'start_at' => ['required', 'date_format:H:i:s'],
            'end_at' => ['required', 'date_format:H:i:s']
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

        // Verify timeslot
        $startAt = Carbon::parse($data['start_at']);
        $endAt = Carbon::parse($data['end_at']);
        $startAtStamp = ($startAt->hour * 3600) + ($startAt->minute * 60) + $startAt->second;
        $endAtStamp = ($endAt->hour * 3600) + ($endAt->minute * 60) + $endAt->second;
        $data['start_at'] = $startAt->toTimeString();
        $data['end_at'] = $endAt->toTimeString();

        if ($endAtStamp - $startAtStamp < 3600) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'timeslot' => [
                        "Timeslot for session must be at least 1 hour long."
                    ]
                ]
            ], 400);
        }
        elseif (ClassSession::where('class_id', $data['class_id'])
            ->where('day', '=', $data['day'])
            ->where(function (Builder $query) use ($data) {
                $query->where('start_at', '<', $data['end_at'])
                    ->where('end_at', '>', $data['start_at']);
            })
            ->exists()
        ) {
            return response()->json([
                'message' => "Errors detected.",
                'errors' => [
                    'timeslot' => [
                        "Timeslot overlaps with another session of the same class."
                    ]
                ]
            ], 400);
        }

        // Add session
        $session = ClassSession::create($data);

        // Repond as JSON
        return response()->json([
            'message' => "Session added.",
            'session' => $session
        ], 200);
    }

    /* GET SESSIONS OF A CLASS */
    public function read($classId) {
        $sessions = ClassSession::where('class_id', $classId)
            ->select('id', 'day', 'start_at', 'end_at')
            ->get();

        return $sessions;
    }

    /* REMOVE A SESSION */
    public function delete(Request $request) : JsonResponse {
        // Validate request
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:class_sessions,id']
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

        // Find the session
        $session = ClassSession::find($data['id']);

        // Delete the session
        $session->delete();

        // JSON confirmation response
        return response()->json([
            'message' => "Session removed.",
            'session' => $session
        ], 200);
    }
}
