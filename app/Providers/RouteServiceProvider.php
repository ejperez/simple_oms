<?php namespace SimpleOMS\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use SimpleOMS\Helpers\Helpers;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'SimpleOMS\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot(Router $router)
	{
		parent::boot($router);

        // Model bindings
        $router->bind('order', function($value)
        {
            return \SimpleOMS\Order::where('id', Helpers::unhash($value))->first();
        });

        $router->bind('category', function($value)
        {
            return \SimpleOMS\Product_Category::where('id', Helpers::unhash($value))->first();
        });

        $router->bind('user', function($value)
        {
            return \SimpleOMS\User::where('id', Helpers::unhash($value))->first();
        });
	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function map(Router $router)
	{
		$router->group(['namespace' => $this->namespace], function($router)
		{
			require app_path('Http/routes.php');
		});
	}

}
