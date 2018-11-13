<?php

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

// Route::get('/', 'MainController@index');

Auth::routes();

Route::get('/', 'MainController@index')->name('home');
Route::post('/admin', 'MainController@addDevice')->name('addDevice');
Route::get('/admin', 'MainController@adminPage')->name('admin');
Route::get('admin/{id}', 'MainController@deleteDevice');
