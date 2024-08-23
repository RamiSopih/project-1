<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

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
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('addFavorite','addFavorite');
    Route::get('view_catigory','view_catigory');
    Route::get('view_places','view_places');
    Route::post('addlikes','addlikes');
    Route::get('show_my_events','show_my_events');
    Route::get('show_catigory_types','show_catigory_types');
    Route::get('show_type_of_event','show_type_of_event');
    Route::post('get_names_of_type','get_names_of_type');
    Route::post('add_catigory','add_catigory');
    Route::post('addFavorite','addFavorite');
    Route::get('show_favorite','show_favorite');
    Route::post('search','search');
    Route::post('add_cart','add_cart');
    Route::post('cat','cat');
    Route::post('delete_from_cart','delete_from_cart');
    Route::post('add_place','add_place');
    Route::get('show_type_place','show_type_place');
    Route::post('add_type_place','add_type_place');
    Route::post('add_type_places','add_type_places');
    Route::get('show_finall','show_finall');
    Route::post('select_place','select_place');













});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('email', [MailController::class, 'sendEmail']);
