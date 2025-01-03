<?php

use App\Http\Controllers\MediaController;
use Inertia\Inertia;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;

Route::group(['middleware' => ['auth','verified']],function(){
    Route::get('/media',[MediaController::class,'index'])->name('media.index');
    Route::post('/media',[MediaController::class,'store'])->name('media.store');
    Route::get('/media/create', [MediaController::class,'create'])->name('media.create');
    Route::delete('/media/destroy', [MediaController::class,'destroy'])->name('media.destroy');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
