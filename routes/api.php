<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->get('/user', function (Request $request) {
    // return $request->user();
    return response()->json(['message' => 'orrrr'], 200);
});
