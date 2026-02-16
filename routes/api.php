<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SessionAttendanceController;
use App\Http\Controllers\EmailController;

/* TOKEN AUTHENTICATION */
Route::post('/login', [TokenController::class, 'create'])->name('login');
Route::middleware('auth:sanctum')->delete('/logout', [TokenController::class, 'delete'])->name('logout');

Route::post('/student/register', [StudentController::class, 'create'])->name('student.register');
Route::patch('/student/update', [StudentController::class, 'update'])->name('student.update');
Route::delete('/student/delete', [StudentController::class, 'delete'])->name('student.delete');
Route::get('/student/list', [StudentController::class, 'readAll'])->name('student.list');
Route::get('/student', [StudentController::class, 'readOne'])->name('student');
Route::post('/student/check-in', [SessionAttendanceController::class, 'create'])->name('student.register');

Route::post('/course/add', [CourseController::class, 'create'])->name('course.add');
Route::get('/course/list', [CourseController::class, 'readAll'])->name('course.list');
Route::get('/course', [CourseController::class, 'readOne'])->name('course');
Route::patch('/course/update', [CourseController::class, 'update'])->name('course.update');
Route::delete('/course/delete', [CourseController::class, 'delete'])->name('course.delete');

Route::patch('/email/update', [EmailController::class, 'updateAll'])->name('email.updateAll');
