<?php

use App\Http\Controllers\Auth\SessionController;
use Illuminate\Support\Facades\Route;

Route::post('login', [SessionController::class, 'create']);
Route::post('logout', [SessionController::class, 'delete']);
