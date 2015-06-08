<?php namespace SimpleOMS\Providers;

use Config;
use Hashids\Hashids;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            $hashids = new Hashids(Config::get('constants.SALT'), Config::get('constants.HLEN'));
            return \SimpleOMS\Order::where('id', $hashids->decode($value))->first();
        });

        $router->bind('category', function($value)
        {
            $hashids = new Hashids(Config::get('constants.SALT'), Config::get('constants.HLEN'));
            return \SimpleOMS\Product_Category::where('id', $hashids->decode($value))->first();
        });

        //$router->model('category', 'SimpleOMS\Product_Category');
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
