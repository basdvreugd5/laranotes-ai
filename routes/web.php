<?php

use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\NoteViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [NoteViewController::class, 'index'])->name('dashboard');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::patch('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::patch('/notes/{note}/archive', [NoteController::class, 'archive'])->name('notes.archive');
    Route::post('/notes/{note}/summarize', [NoteController::class, 'generateSummary'])
        ->name('notes.summarize');
});

require __DIR__.'/auth.php';
