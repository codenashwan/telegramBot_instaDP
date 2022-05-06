<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['cors',  'json']], function () {
    Route::post('/', [\App\Http\Controllers\Api\Api::class , 'start']);
    
});
Route::get('/', [\App\Http\Controllers\Api\Api::class , 'start2']);
