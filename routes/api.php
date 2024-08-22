<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\studentController;
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

 Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); 
Route::post('/checkEmailExists', [UserController::class, 'checkEmailExists']);
Route::post('/checkCinExists', [studentController::class, 'checkCinExists']);
Route::post('/sendVerificationCode', [UserController::class, 'sendVerificationCode']);
Route::post('/verify-code', [UserController::class, 'verifyCode']);
Route::post('/registerUser', [UserController::class, 'registerUser']);
Route::post('/login', [UserController::class, 'login']);



