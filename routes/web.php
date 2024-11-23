<?php

use App\Http\Controllers\ProfileController;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    // User::factory()->count(20)->create();
    // Category::factory()->count(20)->create();
    // News::factory()->count(20)->create();

    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
