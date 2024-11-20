<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Route::get('users',function(){
//     return "Abubakar";
// });

// Route::post('user',function(){
//     return response()->json("Post Data Hit Successfully.");
// });
// Route::delete('user/{id}',function($id){
//     return response($id,200);
// });

// Route::put('user/{id}',function($id){
//     return response($id,200);
// });

Route::post('users/store','App\Http\Controllers\Api\UserController@store');

Route::get('/users',function(){
    p("Working"); 
});

Route::get('user/get/{flag}',[UserController::class,'index']);
Route::get('user/{id}',[UserController::class,'show']);
Route::delete('user/delete/{id}',[UserController::class,'destroy']);
Route::put('update/{id}',[UserController::class,'update']);
Route::patch('change-password/{id}',[UserController::class,'changePassword']);