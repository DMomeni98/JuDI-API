<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post("users", [UserController::class, 'store']);
Route::put("users/{user_name}", [UserController::class, 'update_profile'])->middleware('api');
Route::get("users/{user_name}", [UserController::class, 'index'])->middleware('api');
Route::post("users/signin", [UserController::class, 'signin'])->middleware('api');
Route::post("users/signout", [UserController::class, 'signout'])->middleware('api');
Route::post("users/refresh", [UserController::class, 'refresh'])->middleware('api');
Route::post("users/me", [UserController::class, 'me'])->middleware('api');


