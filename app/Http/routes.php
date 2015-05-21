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

Route::get('home', [
    'middleware' => ['auth', 'roles'],
    'roles' => ['administrator', 'sales', 'customer'],
    'uses' => 'HomeController@index'
]);

Route::get('orders', [
    'middleware' => ['auth', 'roles'],
    'roles' => ['administrator', 'sales', 'customer'],
    'uses' => 'OrdersController@index'
]);

Route::get('create-order', [
    'middleware' => ['auth', 'roles'],
    'roles' => ['administrator', 'customer'],
    'uses' => 'OrdersController@create'
]);

Route::get('get-products', [
    'middleware' => ['auth', 'roles'],
    'roles' => ['administrator', 'customer', 'sales'],
    'uses' => 'OrdersController@getProducts'
]);

Route::get('get-cart', [
    'middleware' => ['auth', 'roles'],
    'roles' => ['administrator', 'customer', 'sales'],
    'uses' => 'OrdersController@getCart'
]);

Route::post('store-product-item',[
    'middleware' => ['auth', 'roles'],
    'roles' => ['administrator', 'customer', 'sales'],
    'uses' => 'OrdersController@storeProductItem'
]);