<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomVerificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/{any?}', function () {
    return view('welcome');
 });
// Auth::routes(['verify' => true]);

/* // Routes de vérification d'email personnalisées
Route::get('email/verify', [CustomVerificationController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('email/verify/{id}/{hash}', [CustomVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('email/resend', [CustomVerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');

Route::get('/{any}', function () {
    return view('app');  // Remplace 'app' par le nom de ta vue Blade qui charge l'application Vue.js
    })->where('any', '.*')->middleware(['auth', 'verified']); */
    
/* Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home'); */
