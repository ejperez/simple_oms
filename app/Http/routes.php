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
    Route::get('get-orders-datatable', 'DatatablesController@getOrders');
    Route::get('search-address', function() {
        $term = Input::get('term');
        if(!empty($term)){
            $term = '%'.$term.'%'; // Enclose in wildcards
            $zipcodes = \SimpleOMS\Zipcode::where('major_area', 'like', $term)
                ->where('city', 'like', $term, 'OR')
                ->where('zip_code', 'like', $term, 'OR')
                ->get();

            return json_encode($zipcodes);
        } else {
            abort(404);
        }
    });
    Route::get('search-products-by-category/{category}', function($category) {
        $category->products;
        return json_encode($category);
    });
});

/*
 * Administrator only
 */
Route::group(['middleware' => ['auth', 'roles'], 'roles' => ['administrator']], function(){
    Route::get('users', function(){
        return 'Users';
    });

    Route::get('customers', function(){
        return 'Customers';
    });
});

/*
 * Sales only
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

    // update order status
    // only cancelled state is allowed
    Route::put('orders/{order}/update-customer-status/{status}', 'OrdersController@updateStatus')
        ->where('status', 'Cancelled');

    /** CUSTOMERS **/

    // create customer form
    Route::get('customers/create', 'CustomersController@create');

    // create customer
    Route::post('customers','CustomersController@store');
});

/*
 * Approver only
 */
Route::group(['middleware' => ['auth', 'roles'], 'roles' => ['approver']], function(){
    // update order status
    // only approved and disapproved state are allowed
    Route::put('orders/{order}/update-approver-status/{status}', 'OrdersController@updateStatus')
        ->where('status', 'Approved|Disapproved');
});