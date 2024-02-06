<?php

use App\Http\Controllers\DepartmentController;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthenticationController;
use \App\Http\Controllers\SubjectController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\EmployeeController;



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


Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');


//Route::middleware('auth:sanctum')->prefix('/user')->group(function () {
//    Route::get('/test', [AuthenticationController::class, 'test']);
//});


Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('department', DepartmentController::class);
    Route::post('importDepartment',[DepartmentController::class,'importDepartment']);
    Route::get('DownloadDepartmentTemplate',[DepartmentController::class,'DownloadDepartmentTemplate']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('resetPassword/{id}',[\App\Http\Controllers\UserController::class,'resetPassword']);
    Route::get('setPassword/{id}',[\App\Http\Controllers\UserController::class,'setPassword']);

});
Route::get('/admin/DownloadAdminTemplate',[AdminController::class,'DownloadAdminTemplate']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('admin', AdminController::class);
    Route::post('importAdmin',[AdminController::class,'importAdmin']);
    Route::get('DownloadAdminTemplate',[AdminController::class,'DownloadAdminTemplate']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('role', RoleController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('subject', SubjectController::class);
});



Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('class-rooms', ClassRoomController::class);
    Route::get('class-rooms/{classRoom}/students', [ClassRoomController::class, 'students'])->name('class-rooms.students');
});

// Route::prefix('admin')->group(function () {
//     Route::post('/create-employee', [AdminController::class, 'storeEmployee']);
// });

Route::post('/employee', [EmployeeController::class, 'createEmployee']);
Route::get('/employee/{departmentId?}', [EmployeeController::class, 'index']);
Route::put('employee/{id}', [EmployeeController::class, 'updateEmployee']);
//Route::delete('employee/{id}', [AdminManageEmployeeController::class, 'deleteEmployee']);
Route::delete('/employee/{id}', [EmployeeController::class, 'toggleIsActive']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('teachers', TeacherController::class);
    Route::get('teachers/{id}', [TeacherController::class, 'show']);
});
use App\Http\Controllers\SuperAdminDashboardController;

Route::prefix('superAdmin')->group(function () {
    Route::get('/departmentDashboard', [SuperAdminDashboardController::class, 'departmentDashboard']); // Not Used
    Route::get('/mainDashboard', [SuperAdminDashboardController::class, 'superAdminDashboard']);
});

Route::get('/test', function () {
    return   Department::where('name','LIKE', 'Managerial')->first()->id ;
});


