<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');
Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

Route::group(['middleware' => ['auth', 'roles']], function () {

    Route::get('home', [
        'roles' => ['administrator', 'sales', 'customer'],
        'uses' => 'HomeController@index'
    ]);

    Route::get('orders', [
        'roles' => ['administrator', 'sales', 'customer'],
        'uses' => 'OrdersController@index'
    ]);

    Route::get('create-order', [
        'roles' => ['administrator', 'customer'],
        'uses' => 'OrdersController@create'
    ]);

    Route::post('store-order',[
        'roles' => ['administrator', 'customer', 'sales'],
        'uses' => 'OrdersController@storeOrder'
    ]);

    Route::get('get-products-datatable', [
        'roles' => ['administrator', 'customer', 'sales'],
        'uses' => 'OrdersController@getProductsDataTable'
    ]);

    Route::get('get-transactions-datatable', [
        'roles' => ['administrator', 'customer', 'sales'],
        'uses' => 'OrdersController@getTransactionsDataTable'
    ]);

    Route::get('orders/{order}/edit', [
        'roles' => ['administrator', 'customer', 'sales'],
        'uses' => 'OrdersController@editOrder'
    ]);

    Route::group(['roles' => 'administrator'], function(){
    });

    Route::group(['roles' => 'sales'], function(){
    });

    Route::group(['roles' => 'customer'], function(){
    });
});