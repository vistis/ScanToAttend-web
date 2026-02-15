<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\SessionAttendanceController;

/* TOKEN AUTHENTICATION */
Route::post('/login', [TokenController::class, 'create'])->name('login');
Route::middleware('auth:sanctum')->delete('/logout', [TokenController::class, 'delete'])->name('logout');

Route::post('/student/register', [StudentController::class, 'create'])->name('student.register');
Route::patch('/student/update', [StudentController::class, 'update'])->name('student.update');
Route::delete('/student/delete', [StudentController::class, 'delete'])->name('student.delete');
Route::get('/student/list', [StudentController::class, 'readAll'])->name('student.list');
Route::get('/student', [StudentController::class, 'readOne'])->name('student');
Route::post('/student/check-in', [SessionAttendanceController::class, 'create'])->name('student.register');
