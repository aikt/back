<?php

use Illuminate\Support\Facades\Route;
use Encore\Latlong\Http\Controllers\LatlongController;



// use App\Admin\Controllers\UploadController;

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('latlong', LatlongController::class.'@index');

// Route::post('/admin/upload','UploadController@index');

Route::post('/admin/upload','UploadController@index');


