<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class HeartbeatController extends Controller
{
    /**
     * Return a simple heartbeat response for the ESP32 to verify backend connectivity.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String()
        ], 200);
    }
}
