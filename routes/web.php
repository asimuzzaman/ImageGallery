<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'ImagesController@show');
Route::post('/image/save', 'ImagesController@save');
Route::get('/image/search', 'ImagesController@search');
Route::get('/image/remove', 'ImagesController@remove');
