<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => 'Api'], function() {
    $exceptCreateAndEdit = [
        'except' => ['create', 'edit']
    ];
    Route::resource('categories', 'CategoryController',$exceptCreateAndEdit);
    Route::resource('genres', 'GenreController', $exceptCreateAndEdit);
});

