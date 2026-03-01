<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class FingerprintController extends Controller
{
    /**
     * Get the ESP32 base URL from config.
     */
    private function espUrl(string $path = ''): string
    {
        $ip = env('ESP32_IP', '192.168.0.1');
        return "http://{$ip}{$path}";
    }

    /**
     * Get the ESP32 device status.
     */
    public function status(): JsonResponse
    {
        try {
            $response = Http::timeout(5)->get($this->espUrl('/status'));
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Cannot connect to ESP32',
                'details' => $e->getMessage()
            ], 503);
        }
    }

    /**
     * Get the list of students with their fingerprint enrollment status.
     */
    public function studentList(): JsonResponse
    {
        $students = Student::select('id', 'fingerprint_id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get();

        // Determine auto-assigned next fingerprint ID
        $maxFpId = Student::whereNotNull('fingerprint_id')->max('fingerprint_id');
        $nextId = ($maxFpId ?? 0) + 1;

        return response()->json([
            'students' => $students,
            'next_fingerprint_id' => $nextId,
        ]);
    }

    /**
     * Start fingerprint enrollment on the ESP32.
     * Proxies the request to the ESP32 /enroll endpoint.
     */
    public function startEnrollment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'fingerprint_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()], 400);
        }

        $data = $validator->validated();
        $fingerprintId = $data['fingerprint_id'];

        // Check if this fingerprint_id is already assigned to another student
        $existing = Student::where('fingerprint_id', $fingerprintId)->first();
        if ($existing && $existing->id !== (int)$data['student_id']) {
            return response()->json([
                'message' => "Fingerprint ID {$fingerprintId} is already assigned to {$existing->first_name} {$existing->last_name}."
            ], 400);
        }

        try {
            $response = Http::timeout(10)->get($this->espUrl("/enroll"), [
                'id' => $fingerprintId,
            ]);
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Cannot connect to ESP32',
                'details' => $e->getMessage()
            ], 503);
        }
    }

    /**
     * Poll enrollment status from the ESP32.
     */
    public function enrollmentStatus(): JsonResponse
    {
        try {
            $response = Http::timeout(5)->get($this->espUrl('/enroll/status'));
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Cannot connect to ESP32',
                'details' => $e->getMessage()
            ], 503);
        }
    }

    /**
     * Save the fingerprint_id to the student record after successful enrollment.
     */
    public function saveEnrollment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'fingerprint_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()], 400);
        }

        $data = $validator->validated();

        $student = Student::find($data['student_id']);
        $student->fingerprint_id = $data['fingerprint_id'];
        $student->save();

        return response()->json([
            'message' => "Fingerprint enrolled for {$student->first_name} {$student->last_name}.",
            'student_id' => $student->id,
            'fingerprint_id' => $student->fingerprint_id,
        ]);
    }

    /**
     * Delete a fingerprint from the ESP32 sensor.
     */
    public function deleteFingerprint(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fingerprint_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()], 400);
        }

        $fingerprintId = $validator->validated()['fingerprint_id'];

        try {
            $response = Http::timeout(5)->get($this->espUrl('/delete'), [
                'id' => $fingerprintId,
            ]);
            $result = $response->json();

            // Also clear fingerprint_id from the student record if it exists
            $student = Student::where('fingerprint_id', $fingerprintId)->first();
            if ($student) {
                $student->fingerprint_id = null;
                $student->save();
            }

            return response()->json($result, $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Cannot connect to ESP32',
                'details' => $e->getMessage()
            ], 503);
        }
    }

    /**
     * Clear all fingerprints from the ESP32 sensor and reset database records.
     */
    public function emptyDatabase(): JsonResponse
    {
        try {
            $response = Http::timeout(5)->post($this->espUrl('/empty'));
            $result = $response->json();

            // Also clear all fingerprint_id values in the database
            if (isset($result['status']) && $result['status'] === 'cleared') {
                Student::whereNotNull('fingerprint_id')->update(['fingerprint_id' => null]);
            }

            return response()->json($result, $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Cannot connect to ESP32',
                'details' => $e->getMessage()
            ], 503);
        }
    }
}
