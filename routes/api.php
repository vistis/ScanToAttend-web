<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AttendanceSyncController;
use App\Http\Controllers\Api\AuthenticatedUserController;
use App\Http\Controllers\Api\ClassSessionController;
use App\Http\Controllers\Api\ClassRegistrationController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CourseClassController;
use App\Http\Controllers\Api\FingerprintController;
use App\Http\Controllers\Api\HeartbeatController;
use App\Http\Controllers\Api\InstructorController;
use App\Http\Controllers\Api\SessionAttendanceController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Route;

Route::get('/heartbeat', [HeartbeatController::class, 'index']);
Route::post('/student/check-in', [SessionAttendanceController::class, 'create']);
Route::post('/attendance/sync', [AttendanceSyncController::class, 'sync']);

Route::post('/login', [TokenController::class, 'create']);

Route::middleware('auth:sanctum')->group(function ()
{
    Route::get('/user', [AuthenticatedUserController::class, 'read']);

    Route::get('/class', [CourseClassController::class, 'read']);

    Route::delete('/logout', [TokenController::class, 'delete']);
});

Route::middleware('auth:admin-api')->group(function ()
{
    Route::post('/esp/enroll/start', [FingerprintController::class, 'startEnrollment']);
    Route::post('/esp/enroll/save', [FingerprintController::class, 'saveEnrollment']);
    Route::post('/esp/fingerprint/delete', [FingerprintController::class, 'deleteFingerprint']);
    Route::post('/esp/fingerprint/empty', [FingerprintController::class, 'emptyDatabase']);
    Route::get('/esp/status', [FingerprintController::class, 'status']);
    Route::get('/esp/enroll/students', [FingerprintController::class, 'studentList']);
    Route::get('/esp/enroll/status', [FingerprintController::class, 'enrollmentStatus']);

    Route::patch('/admin/profile-picture/update', [AdminController::class, 'updateProfilePicture']);

    Route::post('/registration/add', [ClassRegistrationController::class, 'create']);
    Route::delete('/registration/remove', [ClassRegistrationController::class, 'delete']);

    Route::post('/session/add', [ClassSessionController::class, 'create']);
    Route::delete('/session/remove', [ClassSessionController::class, 'delete']);

    Route::post('/class/add', [CourseClassController::class, 'create']);
    Route::get('/class/list/course', [CourseClassController::class, 'readListOfCourse']);
    Route::get('/class/list/instructor', [CourseClassController::class, 'readListOfInstructor']);
    Route::get('/class/list/student', [CourseClassController::class, 'readListOfStudent']);
    Route::patch('/class/assign', [CourseClassController::class, 'updateAssign']);
    Route::patch('/class/unassign', [CourseClassController::class, 'updateUnassign']);
    Route::delete('/class/remove', [CourseClassController::class, 'delete']);

    Route::post('/course/add', [CourseController::class, 'create']);
    Route::get('/course/list', [CourseController::class, 'readList']);
    Route::get('/course', [CourseController::class, 'read']);
    Route::patch('/course/update', [CourseController::class, 'update']);
    Route::delete('/course/remove', [CourseController::class, 'delete']);

    Route::post('/instructor/add', [InstructorController::class, 'create']);
    Route::get('/instructor/list', [InstructorController::class, 'readList']);
    Route::get('/instructor', [InstructorController::class, 'read']);
    Route::patch('/instructor/update', [InstructorController::class, 'update']);
    Route::delete('/instructor/remove', [InstructorController::class, 'delete']);

    Route::post('/student/add', [StudentController::class, 'create']);
    Route::get('/student/list', [StudentController::class, 'readList']);
    Route::get('/student/list-for-admin/class', [StudentController::class, 'readListOfClassAsAdmin']);
    Route::get('/student', [StudentController::class, 'read']);
    Route::patch('/student/update', [StudentController::class, 'update']);
    Route::delete('/student/remove', [StudentController::class, 'delete']);
});

Route::middleware('auth:student-api')->group(function ()
{
    Route::get('/class/student/list', [CourseClassController::class, 'readListAsStudent']);

    Route::get('/attendance/class', [SessionAttendanceController::class, 'readListOfClass']);
});

Route::middleware('auth:instructor-api')->group(function ()
{
    Route::get('/class/instructor/list', [CourseClassController::class, 'readListAsInstructor']);

    Route::get('/attendance/session', [SessionAttendanceController::class, 'readListOfSession']);
    Route::get('/attendance/dates/class', [SessionAttendanceController::class, 'readDatesOfClass']);
    Route::patch('/attendance/update', [SessionAttendanceController::class, 'updateStatus']);

    Route::get('/student/list-for-instructor/class', [StudentController::class, 'readListOfClassAsInstructor']);
});
