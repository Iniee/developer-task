<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Authentication


// Verify email
Route::get('email/verify/{id}', [AuthController::class, 'verify'])->name('verification.verify');
Route::get('email/resend', [AuthController::class, 'resend'])->name('verification.resend');
Route::post('register/user', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot/password', [AuthController::class, 'forgot']);
Route::post('reset/password', [AuthController::class, 'reset'])->name('password.reset');;
//Create Task
Route::middleware(['auth:sanctum', 'role:client'])->group(function () {
    
    //Logout
    Route::post('logout', [AuthController::class, 'logout']);
   //CRUD Operation
    Route::post('create/task', [TaskController::class, 'createTask'])->name('create_task');
    Route::get('retrieve/task/{id}', [TaskController::class, 'retrieveTask']);
    Route::get('retrieve/all/task', [TaskController::class, 'retrieveAllTask']);
    Route::put('update/task/{task}', [TaskController::class, 'updateTask'])->name('update_task');
    Route::delete('delete/task/{task}', [TaskController::class, 'deleteTask'])->name('delete_task');
});