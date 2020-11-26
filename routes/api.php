<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;

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
Route::post("users/{user_name}/cards", [CardController::class, 'store'])->middleware('api');
ROute::get("users/{user_name}/cards/get", [CardController::class, 'show'])->middleware('api');
Route::get("users/{user_name}/cards/get/{due}", [CardController::class, 'show_one_due'])->middleware('api');
Route::get("users/{user_name}/cards/{id}", [CardController::class, 'show_one_card'])->middleware('api');
Route::get("users/{user_name}/cards/remove/{id}", [CardController::class, 'destroy'])->middleware('api');
Route::put("users/{user_name}/cards/update/{id}", [CardController::class, 'update_root'])->middleware('api');
Route::get("users/{user_name}/weekboard", [CardController::class, 'weekboard'])->middleware('api');