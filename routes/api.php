<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::post('/student/login', [StudentController::class, 'authenticate'])->name('student.login');
Route::post('/student/register', [StudentController::class, 'create'])->name('student.register');
Route::patch('/student/update', [StudentController::class, 'update'])->name('student.update');
Route::delete('/student/delete', [StudentController::class, 'delete'])->name('student.delete');
Route::get('/student/list', [StudentController::class, 'readAll'])->name('student.list');
Route::get('/student', [StudentController::class, 'readOne'])->name('student');
