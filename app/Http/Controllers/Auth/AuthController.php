<?php namespace SimpleOMS\Http\Controllers\Auth;

use SimpleOMS\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use SimpleOMS\Role;
use Session;
use Redirect;
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

		$this->middleware('guest', ['except' => ['getLogout', 'getRegister', 'postRegister']]);
	}

    // Override for user registration
    public function getRegister(){
        // Only administrators can register users
        if (Auth::check() && Auth::user()->hasRole(['administrator'])){
            // Get list of roles
            $roles = Role::all();
            return view('auth.register', compact('roles'));
        } else {
            return response([
                'error' => [
                    'code' => 'INSUFFICIENT_ROLE',
                    'description' => 'You are not authorized to access this resource.'
                ]
            ], 401);
        }
    }

    public function postRegister(Request $request)
    {
        $validator = $this->registrar->validator($request->all());

        if ($validator->fails())
        {
            $this->throwValidationException(
                $request, $validator
            );
        }

        // Create user
        $user = $this->registrar->create($request->all());

        // Redirect to list of orders
        Session::flash('success', 'User "'.$user->name.'" was registered succesfully.');
        return redirect('auth/register');
    }

    public function getLogout()
    {
        Auth::logout();
        Session::flush();
        return Redirect::to('/');
    }
}