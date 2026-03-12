<?php

use App\Http\Controllers\Api\NoteController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return new UserResource($request->user());
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    Route::get('/notes', [NoteController::class, 'index']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::patch('/notes/{note}', [NoteController::class, 'update']);
    Route::post('/notes/{note}/archive', [NoteController::class, 'archive']);

    Route::post('/notes/{note}/summarize', [NoteController::class, 'generateSummary'])
        ->middleware('throttle:10,1')
        ->name('notes.summarize');
});
