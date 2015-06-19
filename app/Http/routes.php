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

/*
 * Any authorized user
 */
Route::group(['middleware' => ['auth', 'roles'], 'roles' => ['administrator', 'sales', 'approver']], function () {

    // home
    Route::get('home', 'HomeController@index');

    // order history
    Route::get('orders', 'OrdersController@index');

    // AJAX requests
    Route::get('search-products-by-category/{category}', 'AJAXController@searchProductByCategory');
    Route::get('get-order-details/{order}', 'AJAXController@getOrderDetails');
    Route::get('get-user-order-count-status/{user}', 'AJAXController@getUserOrderCountStatus');

    // edit user form
    Route::get('users/{user}/edit', 'UsersController@edit');

    // update user action
    Route::put('users/{user}', 'UsersController@update');
});

/*
 * Administrator only
 */
Route::group(['middleware' => ['auth', 'roles'], 'roles' => ['administrator']], function(){
    // List of users
    Route::get('users', 'UsersController@index');

    // create user form
    Route::get('users/create', 'UsersController@create');

    // store user action
    Route::post('users', 'UsersController@store');
});

/*
 * Sales and Administrator only
 */
Route::group(['middleware' => ['auth', 'roles'], 'roles' => ['sales', 'administrator']], function(){

    /** ORDERS **/

    // create order form
    Route::get('orders/create', 'OrdersController@create');

    // create order
    Route::post('orders','OrdersController@store');

    // update order form
    Route::get('orders/{order}/edit', 'OrdersController@edit');

    // update order
    Route::put('orders/{order}', 'OrdersController@update');

    // update order status, only cancelled status is allowed
    Route::put('orders/{order}/update-customer-status/{status}', 'OrdersController@updateStatus')
        ->where('status', 'Cancelled');
});

/*
 * Approver only
 */
Route::group(['middleware' => ['auth', 'roles'], 'roles' => ['approver']], function(){
    // update order status, only approved and disapproved statuses are allowed
    Route::put('orders/{order}/update-approver-status/{status}', 'OrdersController@updateStatus')
        ->where('status', 'Approved|Disapproved');

    // update order form
    Route::get('orders/{order}/edit/approver', 'OrdersController@edit');

    // update order
    Route::put('orders/{order}/approver', 'OrdersController@update');
});