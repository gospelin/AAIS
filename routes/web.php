<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::get('/news', function () {
    return view('news');
})->name('news');
Route::get('/gallery', function () {
    return view('gallery');
})->name('gallery');
Route::get('/programs', function () {
    return view('programs');
})->name('programs');

Route::get('/admissions', function () {
    return view('admissions');
})->name('admissions');
Route::get('/newsletter', function () {
    return view('newsletter');
})->name('newsletter');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
