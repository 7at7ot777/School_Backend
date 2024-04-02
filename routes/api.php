<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\StudentGradesController;
use App\Http\Controllers\StudentNoteController;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthenticationController;
use \App\Http\Controllers\SubjectController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\EmployeesAttendanceController;


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

//Department
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('department', DepartmentController::class);
    Route::post('importDepartment',[DepartmentController::class,'importDepartment']);
    Route::get('DownloadDepartmentTemplate',[DepartmentController::class,'DownloadDepartmentTemplate']);
});

//User
Route::middleware('auth:sanctum')->group(function () {

    Route::get('resetPassword/{id}',[\App\Http\Controllers\UserController::class,'resetPassword']);
    Route::get('setPassword/{id}',[\App\Http\Controllers\UserController::class,'setPassword']);
    Route::post('uploadAvatar/{id}',[\App\Http\Controllers\UserController::class,'uploadAvatar']);
    Route::post('user/update',[\App\Http\Controllers\UserController::class,'update']);

});

//Admin
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('admin', AdminController::class);
    Route::post('importAdmin',[AdminController::class,'importAdmin']);
    Route::get('DownloadAdminTemplate',[AdminController::class,'DownloadAdminTemplate']);
    Route::get('admin/dashboard/{dept_id}',[AdminController::class,'dashboard']);
    Route::get('adminDashboard',[AdminController::class,'adminDashboard']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('role', RoleController::class);
});

//Subject
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('subject', SubjectController::class);
    Route::post('importSubject',[SubjectController::class,'importSubject']);
    Route::get('DownloadSubjectTemplate',[SubjectController::class,'DownloadSubjectTemplate']);
});


//ClassRooms
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('class-rooms', ClassRoomController::class);
    Route::get('class-rooms/{classRoom}/students', [ClassRoomController::class, 'students'])->name('class-rooms.students');
    Route::post('/importClassroom',[ClassroomController::class,'importClassroom']);
    Route::get('/DownloadClassroomTemplate',[ClassroomController::class,'DownloadClassroomTemplate']);

});

//Employees
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/employee/{dept_id}', [EmployeeController::class, 'index']);
    Route::get('/DownloadEmployeeTemplate', [EmployeeController::class, 'DownloadEmployeeTemplate']);
    Route::post('/importEmployee/{dept_id}',[EmployeeController::class,'importEmployee']);
    Route::delete('/employee/{id}', [EmployeeController::class, 'toggleIsActive']);
    Route::apiResource('employee', EmployeeController::class);
});

//Student
 Route::middleware('auth:sanctum')->group(function () {
     Route::apiResource('student', StudentController::class);
     Route::delete('/student/{id}', [StudentController::class, 'toggleIsActive']);
     Route::post('importStudent',[StudentController::class,'importStudent']);
     Route::get('DownloadStudentTemplate',[StudentController::class,'DownloadStudentTemplate']);
     Route::post('generatePaymentCodeForStudent',[StudentController::class,'generatePaymentCodeForStudent']);
     Route::post('createStudent',[StudentController::class,'createStudent']);
     Route::get('assignCodeToAllStudents',[StudentController::class,'assignCodeToAllStudents']);
     Route::post('generatePaymentCodePerGrade',[StudentController::class,'generatePaymentCodePerGrade']);

 });



Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('teachers', TeacherController::class);
    Route::get('teachers/{id}', [TeacherController::class, 'show']);
    Route::get('getTeachersSubjects/{teacher_id}', [TeacherController::class, 'getTeachersSubjects']);
    Route::get('teacher/dashboard', [TeacherController::class, 'dashboard']);
});

//parent
Route::post('/parent', [ParentController::class, 'create']);
Route::get('/parent', [ParentController::class, 'index']);
Route::put('/parent/{id}', [ParentController::class, 'update']);
Route::delete('/parent/{id}', [ParentController::class, 'destroy']);


//StudentAttendance
use App\Http\Controllers\StudentAttendanceController;
Route::post('/attendance', [StudentAttendanceController::class, 'recordAttendance']);
Route::get('/students/absences/{studentId}', [StudentAttendanceController::class, 'calculateAbsenceDays']);


//EmployeeAttendance
Route::post('/employee/attendance', [EmployeesAttendanceController::class, 'recordAttendance']);
Route::get('/employee/absences/{employeeId}', [EmployeesAttendanceController::class, 'calculateAbsenceDays']);


//Lecture
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('lectures', LectureController::class);
    Route::get('/getSubjectLectures/{subject_id}',[LectureController::class,'getSubjectLectures']);
});
//Timetables
Route::middleware('auth:sanctum')->prefix('/timetable')->group(function () {
    Route::get('/getTeacherTable/{teacher_id}', [\App\Http\Controllers\TimetableController::class, 'getTeacherTable']);
    Route::get('/getDataForMakeTable', [\App\Http\Controllers\TimetableController::class, 'getDataForMakeTable']);
    Route::post('/addNewPeriod', [\App\Http\Controllers\TimetableController::class, 'addNewPeriod']);
    Route::get('/getClassTable/{class_id}', [\App\Http\Controllers\TimetableController::class, 'getClassTable']);

    // Add routes for edit and delete
    Route::put('/editPeriod/{id}', [\App\Http\Controllers\TimetableController::class, 'editPeriod']);
    Route::delete('/deletePeriod/{id}', [\App\Http\Controllers\TimetableController::class, 'deletePeriod']);
});


//Student Notes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('student-notes', StudentNoteController::class);
    Route::get('/showAllNotesForSpecificStudent/{student_id}',[StudentNoteController::class,'showAllNotesForSpecificStudent']);
    Route::get('/showAllNotesFor1StudentAnd1Subject/{student_id}/{subject_id}',[StudentNoteController::class,'showAllNotesFor1StudentAnd1Subject']);
});

//Student Notes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/student-grades/index/{subjectId}',[StudentGradesController::class,'index']);
    Route::get('/student-grades/show/{subjectId}/{studentId}',[StudentGradesController::class,'show']);
    Route::get('/student-grades/getStudentGrade/{studentId}',[StudentGradesController::class,'getStudentGrade']);
    Route::apiResource('student-grades', StudentGradesController::class);

});


Route::prefix('superAdmin')->group(function () {
    Route::get('/departmentDashboard', [SuperAdminDashboardController::class, 'departmentDashboard']); // Not Used
    Route::get('/mainDashboard', [SuperAdminDashboardController::class, 'superAdminDashboard']);
});

Route::get('/test', function () {

    return date('l');
});


//Payments
Route::get('/paymentInstatiantion',[\App\Http\Controllers\PaymentController::class, 'createPaymentCode']);
Route::get('/orderRegestrationAPI',[\App\Http\Controllers\PaymentController::class,'orderRegestrationAPI']);

//Face Detection
Route::get('/listAllFaces',[\App\Http\Controllers\FaceRecognitionController::class,'listAllFaces']);
Route::post('/addFace',[\App\Http\Controllers\FaceRecognitionController::class,'addFace']);


