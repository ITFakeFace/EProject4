<?php

use Illuminate\Support\Facades\Route;
include 'tam.php';
include 'son.php';
include 'hoai.php';
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

Route::middleware(['check_login'])->group(function () {

    //Route::get('/', 'DemoController@viewIndex');
    Route::get('/post', 'DemoController@postAddUser');
    
    Route::get('/demo/get', 'DemoController@testGet');
    Route::get('/demo/example-data-get', 'DemoController@exampleDataGet');

});
