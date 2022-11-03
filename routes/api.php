<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StudentController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/students', [StudentController::class, 'index']);

Route::get('/classes', [ClassesController::class, 'index']);
Route::get('/sections', [SectionController::class, 'index']);

Route::delete('student/{student}/delete', [StudentController::class, 'destroy']);
Route::delete('students/{students}/massDestroy', [StudentController::class, 'massDestroy']);

Route::get('students/{students}/export', [StudentController::class, 'export']);
