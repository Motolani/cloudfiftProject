<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TransactionController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
    
});

Route::group(['middleware' => ['CheckApiAuth', 'ApiKey']], function () {
    Route::post('/createAccount', [AccountController::class, 'createAccount']);
    Route::post('/transfer', [TransactionController::class, 'transfer']);
    
    Route::post('/getbalance', [AccountController::class, 'getBalance']);
    Route::post('/transferHistory', [TransactionController::class, 'transferHistory']);
    
    Route::post('/createAdmin', [AdminController::class, 'createAdmin']);
    
        
    
});
