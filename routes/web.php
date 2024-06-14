<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

//handle of home route for users 
Route::get('/home', [TaskController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('tasks/index', [TaskController::class, 'index'])->name('Tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks/store', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}}', [TaskController::class, 'delete'])->name('tasks.delete');
    Route::post('/tasks/{task}}', [TaskController::class, 'complete'])->name('tasks.complete');
});

Route::get('/admin', [TaskController::class, 'admin']);
Route::post('/admin', [TaskController::class, 'checkAdmin'])->name('admin');

Auth::routes(['verify'=>true]);