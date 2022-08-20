<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginRegistController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProgramController;
//use App\Http\Controllers\CommentController;

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

//other

Route::get('/getuserbyemail/{email}',[LoginRegistController::class,'getUser']);
Route::post('/registerUser',[LoginRegistController::class,'registerUser']);
Route::post('/login',[LoginRegistController::class,'login']);
Route::get('/getuserbyid/{id}',[LoginRegistController::class,'getuserbyid']);

Route::get('images/{filename}', function ($filename)
{
    $file = \Illuminate\Support\Facades\Storage::get($filename);
    return response($file, 200)->header('Content-Type', 'image/jpeg');
});

Route::post('/createrole',[RoleController::class,'createRole']);
Route::post('/createactivite',[ProgramController::class,'createactivite']);
Route::post('/createprogramme',[ProgramController::class,'createprogramme']);

Route::get('/getactivites',[ProgramController::class,'getactivite']);
Route::get('/getroles',[RoleController::class,'getrole']);
Route::get('/getweek',[ProgramController::class,'getweek']);
Route::get('/getallusers',[LoginRegistController::class,'getAllUser']);