<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

const PAGINATE=5;
///////////////// login ///////////////////

Route::group(['middleware'=>['api','checkPassword','checkLanguage'],'namespace'=>'App\Http\Controllers\API\User'],function (){

    Route::post('register','LoginController@register');
    Route::post('login','LoginController@login');

});
/////////////////////// logout //////////////////////////
Route::group(['middleware'=>['auth.guard:user-api','checkPassword','checkLanguage'],'namespace'=>'App\Http\Controllers\API\User'],function(){
    Route::post('logout','LoginController@logout');
});

/////////////////// user //////////////
Route::group(['middleware'=>['api','checkLanguage'],'namespace'=>'App\Http\Controllers\API\User'],function (){
    Route::get('showUser','UserController@show');
    Route::get('editUser/{id}','UserController@edit');
    Route::post('updateUser/{id}','UserController@update');
    Route::post('deleteUser/{id}','UserController@delete');
    Route::post('forgetPassword','UserController@forgetPassword');

});


/////////////////////// Admin ///////////////////

Route::group(['middleware'=>['guest:admin','checkPassword','checkLanguage'],'namespace'=>'App\Http\Controllers\API\Admin'],function (){
    Route::group(['prefix'=>'admin'],function (){
        Route::post('register','LoginController@register');
        Route::post('login','LoginController@login');

    });


});
///   Admin
Route::group(['middleware'=>['guest:admin','checkLanguage'],'namespace'=>'App\Http\Controllers\API\Admin'],function (){
    Route::group(['prefix'=>'admin'],function () {

        Route::get('show', 'AdminController@show');
        Route::get('edit/{id}', 'AdminController@edit');
        Route::post('update/{id}', 'AdminController@update');
        Route::post('delete/{id}', 'AdminController@delete');
        Route::post('forgetPassword', 'AdminController@forgetPassword');

        Route::group(['prefix'=>'category'],function (){
            Route::post('add', 'CategoryController@add');
            Route::post('delete/{id}', 'CategoryController@delete');
            Route::get('show', 'CategoryController@show');

            Route::get('edit/{id}', 'CategoryController@edit');
            Route::post('update/{id}', 'CategoryController@update');


        });


    });
});
