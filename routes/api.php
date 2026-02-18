<?php

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\ClassSessionController;
use App\Http\Controllers\ClassRegistrationController;
use App\Http\Controllers\SessionAttendanceController;
use App\Http\Controllers\EmailController;

/* TOKEN AUTHENTICATION */
Route::post('/login', [TokenController::class, 'create'])->name('login');
Route::middleware('auth:sanctum')->delete('/logout', [TokenController::class, 'delete'])->name('logout');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) : JsonResponse {
    // Get user info
    $account = $request->user();

    // Generate URL for profile image
    $account->profile_picture = Storage::url($account->profile_picture);

    // JSON response
    return response()->json([
        'message' => "Retrived information on logged in user.",
        'account' => $account
    ], 200);
});

Route::post('/class/add', [CourseClassController::class, 'create']);
Route::post('/class/register', [ClassRegistrationController::class, 'create']);
Route::get('/class/list/course', [CourseClassController::class, 'readAllForCourse']);
Route::get('/class/list/instructor', [CourseClassController::class, 'readAllForInstructor']);
Route::get('/class/list/student', [CourseClassController::class, 'readAllForStudent']);
Route::get('/class', [CourseClassController::class, 'readOne']);
Route::patch('/class/update', [CourseClassController::class, 'update']);
Route::delete('/class/delete', [CourseClassController::class, 'delete']);
Route::delete('/class/unregister', [ClassRegistrationController::class, 'delete']);

Route::post('/session/add', [ClassSessionController::class, 'create']);
Route::delete('/session/remove', [ClassSessionController::class, 'delete']);

Route::post('/student/register', [StudentController::class, 'create']);
Route::patch('/student/update', [StudentController::class, 'update']);
Route::delete('/student/delete', [StudentController::class, 'delete']);
Route::get('/student/list', [StudentController::class, 'readAll']);
Route::get('/student', [StudentController::class, 'readOne']);
Route::middleware('auth:student-api')->get('/student/class/list', [CourseClassController::class, 'readAllAsStudent']);
Route::middleware('auth:student-api')->get('/student/attendance/class', [SessionAttendanceController::class, 'readAllAsStudent']);
Route::post('/student/check-in', [SessionAttendanceController::class, 'create']);

Route::post('/instructor/add', [InstructorController::class, 'create']);
Route::get('/instructor/list', [InstructorController::class, 'readAll']);
Route::get('/instructor', [InstructorController::class, 'readOne']);
Route::middleware('auth:instructor-api')->get('/instructor/class/list', [CourseClassController::class, 'readAllAsInstructor']);
Route::middleware('auth:instructor-api')->get('/attendance/session', [SessionAttendanceController::class, 'readAllForSession']);
Route::patch('/instructor/update', [InstructorController::class, 'update']);
Route::delete('/instructor/delete', [InstructorController::class, 'delete']);

Route::post('/course/add', [CourseController::class, 'create']);
Route::get('/course/list', [CourseController::class, 'readAll']);
Route::get('/course', [CourseController::class, 'readOne']);
Route::patch('/course/update', [CourseController::class, 'update']);
Route::delete('/course/delete', [CourseController::class, 'delete']);

Route::patch('/email/update', [EmailController::class, 'updateAll']);
