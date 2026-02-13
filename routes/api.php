<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

// Route::middleware(['guest'])->get('/user', function (Request $request) {
//     // return $request->user();
//     return response()->json(['message' => 'orrrr'], 200);
// });

Route::post('/student/login', [StudentController::class, 'authenticate'])->name('student.login');
Route::post('/student/register', [StudentController::class, 'create'])->name('student.register');
Route::get('/student/list', [StudentController::class, 'readAll'])->name('student.list');
Route::get('/student', [StudentController::class, 'readOne'])->name('student');
