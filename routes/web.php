<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ChargeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/register', [AuthController::class, 'register']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/login', [AuthController::class, 'login']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

Route::get('/charges', function () {
    return view('charges');
})->name('charges');
