<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\DB;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [WebAuthController::class, 'showLogin'])->name('login');
Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [WebAuthController::class, 'register']);

Route::post('/login', [WebAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');

Route::get('/profile', [ReportController::class, 'profile']);

Route::get('/transactionpopup', [ReportController::class, 'transactionpopup']);

Route::get('/complaint', [ReportController::class, 'complaint']);

Route::post('/save-complaint', [ReportController::class, 'store'])->name('complaint.store');
Route::get('/transactions', [ReportController::class, 'transaction'])->name('transactions');
Route::get('/download-pdf', [ReportController::class, 'downloadPDF'])->name('download.pdf');
Route::post('/save-transaction', [ReportController::class, 'transactionstore'])->name('transaction.store');
Route::get('/profile', [ReportController::class, 'getProfile'])->name('profile.get');
Route::put('/profile/update', [ReportController::class, 'updateProfile'])->name('profile.update');

