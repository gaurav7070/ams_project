<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/userdetails', [WebAuthController::class, 'userdetails'])->name('userdetails');
Route::post('/register', [WebAuthController::class, 'register']);
Route::delete('/deleteUser', [WebAuthController::class, 'deleteUser']);
Route::get('/transactionsapi', [ReportController::class, 'transactionapi'])->name('transactionsapi');

Route::get('/account-details', [WebAuthController::class, 'getAccountDetails']);
Route::put('/updateAccountAndUser', [WebAuthController::class, 'updateAccountAndUser']);
Route::post('/transactionsstore', [ReportController::class, 'storeTransaction']);
Route::get('/transactionsget', [ReportController::class, 'getTransactions']);



