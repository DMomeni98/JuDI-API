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


//users
  Route::post("users", [UserController::class, 'store']);
Route::put("users/{user_name}", [UserController::class, 'update_profile'])->middleware('api');
Route::put("users/{user_name}/change_password", [UserController::class, 'change_password'])->middleware('api');
Route::post("users/{user_name}/upload_avatar", [UserController::class, 'upload_avatar'])->middleware('api');
Route::post("users/signout", [UserController::class, 'signout'])->middleware('api');
Route::get("users/{user_name}", [UserController::class, 'index'])->middleware('api');
Route::post("users/signin", [UserController::class, 'signin'])->middleware('api');
Route::post("users/refresh", [UserController::class, 'refresh'])->middleware('api');

//cards
Route::post("cards", [CardController::class, 'store']);
