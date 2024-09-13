<?php

use App\Http\Controllers\adminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\studentController;
use App\Http\Controllers\teacherController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/ 
Route::options('{any}', function (Request $request) {
    return response()->json([], 200);
})->where('any', '.*'); 
Route::middleware(['cors'])->group(function(){
Route::post('/checkEmailExists', [UserController::class, 'checkEmailExists']);
Route::post('/checkCinExists', [studentController::class, 'checkCinExists']);
Route::post('/sendVerificationCode', [UserController::class, 'sendVerificationCode']);
Route::post('/verify-code', [UserController::class, 'verifyCode']);
Route::post('/registerUser', [UserController::class, 'registerUser']);
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getUser']);
Route::middleware('auth:sanctum')->put('/userEdit', [UserController::class,'updateUser']);
//studentController

Route::get('/student-info', [studentController::class, 'getLoggedInStudent']);
Route::get('/students-by-specialty', [studentController::class, 'getStudentsBySpecialty']);
Route::middleware('auth:sanctum')->post('/projects', [studentController::class, 'store']);
Route::middleware('auth:sanctum')->get('/fetchDataProject', [studentController::class, 'show']);
Route::middleware('auth:sanctum')->put('/update', [studentController::class, 'update']);
Route::middleware('auth:sanctum')->post('/upload-specs', [studentController::class, 'uploadSpecs']);
Route::middleware('auth:sanctum')->post('/complaints', [studentController::class, 'storeComplaint']);
Route::middleware('auth:sanctum')->get('/check-edit-form-complaint', [studentController::class, 'checkEditFormComplaint']);

// teacherController

Route::middleware('auth:sanctum')->get('/teachers', [teacherController::class, 'getTeacherEmails']);
Route::middleware('auth:sanctum')->get('/getStudents', [teacherController::class, 'getStudentsByTeacherEmail']);
Route::middleware('auth:sanctum')->post('/teacher-complaints', [teacherController::class, 'storeComplaint']);
Route::middleware('auth:sanctum')->post('/get-project-details', [teacherController::class,'getProjectDetailsByEmail']);
Route::middleware('auth:sanctum')->post('/availabilities', [teacherController::class, 'store']);
Route::middleware('auth:sanctum')->get('/availabilities', [teacherController::class, 'index']);
Route::middleware('auth:sanctum')->delete('/availabilities/{id}', [teacherController::class, 'destroy']);
Route::middleware('auth:sanctum')->put('/availabilities/{id}', [teacherController::class, 'update']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/complaints', [adminController::class, 'getComplaints']);
    Route::get('/teacherComplaints', [adminController::class, 'getTeacherComplaints']);
    Route::put('/updateStatus/{id}/status', [adminController::class,'updateStatus']);
    Route::get('/users', [adminController::class, 'allUsers']);
    Route::get('/user/{userId}', [adminController::class,'getUserDetails']);
    Route::put('/updateUser/{userId}', [adminController::class,'updateUser']);
});

});



