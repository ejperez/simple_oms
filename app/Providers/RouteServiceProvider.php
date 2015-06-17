<?php namespace SimpleOMS\Providers;

use Hashids\Hashids;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

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
            $hashids = new Hashids(SALT, HLEN);
            return \SimpleOMS\Order::where('id', $hashids->decode($value))->first();
        });

        $router->bind('category', function($value)
        {
            $hashids = new Hashids(SALT, HLEN);
            return \SimpleOMS\Product_Category::where('id', $hashids->decode($value))->first();
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
