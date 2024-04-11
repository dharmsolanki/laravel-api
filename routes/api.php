<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/user',[UserController::class,'index']);

//find single user
Route::get('/user/{id}',[UserController::class,'show']);

//insert user
Route::post('/user',[UserController::class,'store']);

//update user
Route::put('/user/{id}',[UserController::class,'updateUser']);

//delete user
Route::delete('user/{id}',[UserController::class,'delete']);

//upload image
Route::post('/upload',[UserController::class,'upload']);

Route::post('/image/{id}', [UserController::class, 'updateImage']);