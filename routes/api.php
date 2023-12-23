<?php

use App\Http\Controllers\DepartmentController;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;

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


Route::post('/login', [AuthenticationController::class,'login'])->name('login');
Route::post('/register', [AuthenticationController::class,'register']);

//Route::middleware('auth:sanctum')->prefix('/user')->group(function () {
//    Route::get('/test', [AuthenticationController::class, 'test']);
//});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('department', DepartmentController::class);

});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('admin', AdminController::class);

});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('role', RoleController::class);

});

use App\Http\Controllers\SuperAdminDashboardController;

Route::prefix('superAdmin')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'dashboard']);
});

Route::get('/test',function (){
   return   $admins = Employee::with('department:id,name', 'user:id,email,name,phone,status')->where('role', 'admin')->whereHas('user',function ($query){
       $query->where('status',1);
   })->get();

});




