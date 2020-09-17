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
    Route::get('settings/roles', 'Settings\ProfileController@allRoles');
    Route::resource('settings/profile', 'Settings\ProfileController')->middleware('can:user_management_View');
    Route::patch('settings/password', 'Settings\PasswordController@update');
    Route::get('inventorybalance/{y1}/{m1}/{d1}/{y2}/{m2}/{d2}', 'InventoryController@balance')->middleware('can:InventoryItem_View');
    Route::get('inventoryinout/{id}/{y1}/{m1}/{d1}/{y2}/{m2}/{d2}', 'InventoryController@inout')->middleware('can:InventoryItem_View');
    Route::get('etd', 'InventoryController@etd')->middleware('can:InventoryItem_View');
    Route::resource('inventory', 'InventoryController')->middleware('can:InventoryItem_View');
    Route::resource('store', 'StoreController');
    Route::resource('inventoryreceive', 'InventoryreceiveController')->middleware('can:ItemReceive_View');
    Route::resource('invenrecall', 'InvenrecallController')->middleware('can:ItemReceive_View');
    Route::resource('inventoryissue', 'InventoryissueController')->middleware('can:ItemIssue_View');
    Route::resource('rechead', 'RecheadController')->middleware('can:requisition_View');
    Route::resource('recdetails', 'RecdetailsController')->middleware('can:requisition_View');
    Route::resource('productdetails', 'ProductdetailsController')->middleware('can:product_details_View');
    Route::resource('producthead', 'ProductheadController')->middleware('can:product_details_View');
    Route::resource('polist', 'PolistController')->middleware('can:po_list_View');

    Route::resource('employee', 'EmployeeController')->middleware('can:employee_profile_View');
    Route::resource('holiday', 'HolidayController')->middleware('can:holiday_management_View');
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
