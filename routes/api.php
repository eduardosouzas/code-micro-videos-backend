<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => 'Api'], function() {
    Route::resource('categories', 'CategoryController', ['except' => ['create', 'edit']]);
    Route::resource('genres', 'GenreController', ['except' => ['create', 'edit']]);
});

