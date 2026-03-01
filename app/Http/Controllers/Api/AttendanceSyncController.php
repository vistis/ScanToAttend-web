<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRegistration;
use App\Models\ClassSession;
use App\Models\CourseClass;
use App\Models\SessionAttendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceSyncController extends Controller
{
    /**
     * Process a batch of offline attendance records from the ESP32.
     * Expects: { "records": [ { "fingerprint_id": int, "scanned_at": "ISO8601" }, ... ] }
     */
    public function sync(Request $request): JsonResponse
    {
        /** Validate the wrapper. */
        $validator = Validator::make($request->all(), [
            'records' => ['required', 'array', 'min:1'],
            'records.*.fingerprint_id' => ['required', 'integer'],
            'records.*.scanned_at' => ['required', 'date']
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => $validator->messages()
            ], 400);
        }

        $records = $request->input('records');
        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($records as $record)
        {
            $result = $this->processRecord($record);

            if ($result['success'])
            {
                $successCount++;
            }
            else
            {
                $failCount++;
            }

            $results[] = $result;
        }

        return response()->json([
            'message' => "Sync completed. {$successCount} succeeded, {$failCount} failed.",
            'synced' => $successCount,
            'failed' => $failCount,
            'details' => $results
        ], 200);
    }

    /**
     * Process a single attendance record.
     */
    private function processRecord(array $record): array
    {
        $fingerprint = $record['fingerprint_id'];
        $scannedAt = Carbon::parse($record['scanned_at']);
        $checkedInOn = $scannedAt->toDateString();
        $checkedInAt = $scannedAt->toTimeString();
        $day = $scannedAt->englishDayOfWeek;

        /** Get the student. */
        $student = Student::where('fingerprint_id', $fingerprint)->first();

        if (!$student)
        {
            return [
                'success' => false,
                'fingerprint_id' => $fingerprint,
                'reason' => 'Student not found.'
            ];
        }

        /** Find matching session.
         *  Allow a 5-minute early grace period before the session starts
         *  (consistent with real-time check-in endpoint). */
        $earlyGraceMinutes = 5;
        $earliestCheckIn = Carbon::parse($checkedInAt)->copy()
            ->addMinutes($earlyGraceMinutes)
            ->toTimeString();

        $session = ClassSession::join('class_registrations', 'class_sessions.class_id', '=', 'class_registrations.class_id')
            ->where('class_registrations.student_id', '=', $student->id)
            ->where('class_sessions.day', '=', $day)
            ->where(function (Builder $query) use ($checkedInAt, $earliestCheckIn) {
                $query->where('class_sessions.start_at', '<=', $earliestCheckIn)
                    ->where('class_sessions.end_at', '>=', $checkedInAt);
            })
            ->select(
                'class_sessions.id as id',
                'class_sessions.class_id as class_id',
                'class_sessions.day',
                'class_sessions.start_at as start_at',
                'class_sessions.end_at'
            )
            ->first();

        if (!$session)
        {
            return [
                'success' => false,
                'fingerprint_id' => $fingerprint,
                'reason' => 'No matching session found.'
            ];
        }

        /** Check for duplicate. */
        if (SessionAttendance::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->where('checked_in_on', $checkedInOn)
            ->exists()
        )
        {
            return [
                'success' => false,
                'fingerprint_id' => $fingerprint,
                'reason' => 'Already checked in.'
            ];
        }

        /** Determine status. */
        $startAt = Carbon::parse($session->start_at);
        $checkedInAtObj = Carbon::parse($checkedInAt);
        $startAtStamp = ($startAt->hour * 3600) + ($startAt->minute * 60) + $startAt->second;
        $checkedInAtStamp = ($checkedInAtObj->hour * 3600) + ($checkedInAtObj->minute * 60) + $checkedInAtObj->second;

        $status = ($checkedInAtStamp - $startAtStamp) >= 15 * 60 ? 'Tardy' : 'Present';

        /** Record attendance. */
        SessionAttendance::create([
            'student_id' => $student->id,
            'session_id' => $session->id,
            'checked_in_on' => $checkedInOn,
            'checked_in_at' => $checkedInAt,
            'status' => $status
        ]);

        return [
            'success' => true,
            'fingerprint_id' => $fingerprint,
            'student' => $student->username,
            'status' => $status
        ];
    }
}
