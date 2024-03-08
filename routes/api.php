<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

const PAGINATE=20;
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
    Route::post('deleteUser','UserController@delete');
    Route::post('forgetPassword','UserController@forgetPassword');

});


/////////////////////// Admin ///////////////////

Route::group(['middleware'=>['guest:admin','checkPassword','checkLanguage'],'namespace'=>'App\Http\Controllers\API\Admin'],function (){
    Route::group(['prefix'=>'admin'],function (){
        Route::post('register','LoginController@register');
        Route::post('login','LoginController@login');
    });

});
