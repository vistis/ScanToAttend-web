<?php

use Illuminate\Support\Facades\Route;
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

Route::post('/class/add', [CourseClassController::class, 'create'])->name('class.add');
Route::post('/class/register', [ClassRegistrationController::class, 'create'])->name('class.register');
Route::get('/class/list/course', [CourseClassController::class, 'readCourse'])->name('class.list.course');
Route::get('/class/list/instructor', [CourseClassController::class, 'readInstructor'])->name('class.list.instructor');
Route::get('/class/list/student', [CourseClassController::class, 'readStudent'])->name('class.list.student');
Route::get('/class', [CourseClassController::class, 'readOne'])->name('class');
Route::patch('/class/update', [CourseClassController::class, 'update'])->name('class.update');
Route::delete('/class/delete', [CourseClassController::class, 'delete'])->name('class.delete');
Route::delete('/class/unregister', [ClassRegistrationController::class, 'delete'])->name('class.unregister');

Route::post('/session/add', [ClassSessionController::class, 'create'])->name('session.add');
Route::delete('/session/remove', [ClassSessionController::class, 'delete'])->name('session.remove');

Route::post('/student/register', [StudentController::class, 'create'])->name('student.register');
Route::patch('/student/update', [StudentController::class, 'update'])->name('student.update');
Route::delete('/student/delete', [StudentController::class, 'delete'])->name('student.delete');
Route::get('/student/list', [StudentController::class, 'readAll'])->name('student.list');
Route::get('/student/list/class', [ClassRegistrationController::class, 'readStudent'])->name('student.list.class');
Route::get('/student', [StudentController::class, 'readOne'])->name('student');
Route::post('/student/check-in', [SessionAttendanceController::class, 'create'])->name('student.register');

Route::post('/instructor/add', [InstructorController::class, 'create'])->name('instructor.add');
Route::get('/instructor/list', [InstructorController::class, 'readAll'])->name('instructor.list');
Route::get('/instructor', [InstructorController::class, 'readOne'])->name('instructor');
Route::patch('/instructor/update', [InstructorController::class, 'update'])->name('instructor.update');
Route::delete('/instructor/delete', [InstructorController::class, 'delete'])->name('instructor.delete');

Route::post('/course/add', [CourseController::class, 'create'])->name('course.add');
Route::get('/course/list', [CourseController::class, 'readAll'])->name('course.list');
Route::get('/course', [CourseController::class, 'readOne'])->name('course');
Route::patch('/course/update', [CourseController::class, 'update'])->name('course.update');
Route::delete('/course/delete', [CourseController::class, 'delete'])->name('course.delete');

Route::patch('/email/update', [EmailController::class, 'updateAll'])->name('email.updateAll');
