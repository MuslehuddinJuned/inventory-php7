<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('/user', 'Auth\UserController@current');

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');
    Route::get('inventorybalance/{y1}/{m1}/{d1}/{y2}/{m2}/{d2}', 'InventoryController@balance');
    // Route::get('inventorybalance/{date_1}', 'InventoryController@balance');
    Route::resource('inventory', 'InventoryController');
    Route::resource('inventoryreceive', 'InventoryreceiveController');
    Route::resource('invenrecall', 'InvenrecallController');
    Route::resource('inventoryissue', 'InventoryissueController');
    Route::resource('rechead', 'RecheadController');
    Route::resource('recdetails', 'RecdetailsController');
    Route::resource('productdetails', 'ProductdetailsController');
    Route::resource('producthead', 'ProductheadController');
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('email/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'Auth\VerificationController@resend');

    Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});
