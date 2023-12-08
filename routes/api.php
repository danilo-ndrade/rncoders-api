<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ClientController;

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

Route::post('/login', [AuthController::class, 'login']);

Route::group([
    'middleware' => 'jwt.verify',
], function () {
    Route::apiResources([
        'users' => UserController::class,
        'tags' => TagController::class,
        'categories' => CategoryController::class,
        'post' => PostController::class,
        'team' => TeamController::class,
        'portfolio' => PortfolioController::class,
        'client' => ClientController::class,
    ]);

});



// Password recovery
Route::post('forgot-password', [NewPasswordController::class , 'forgotPassword']);
Route::put('reset-password', [NewPasswordController::class, 'resetPassword']);

