<?php namespace SimpleOMS\Http\Controllers\Auth;

use SimpleOMS\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use SimpleOMS\Role;
use Redirect;
use Session;
use Auth;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

        // Prevent these routes from redirecting to login or home page
		$this->middleware('guest', ['except' => ['getLogout', 'getRegister', 'postRegister']]);
	}

    // Override for user registration
    public function getRegister()
    {
        abort(404);
    }

    public function postRegister(Request $request)
    {
        abort(404);
    }

    public function getLogout()
    {
        Auth::logout();
        Session::flush();
        return Redirect::to('/');
    }
}