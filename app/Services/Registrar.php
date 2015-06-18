<?php namespace SimpleOMS\Services;

use Illuminate\Contracts\Auth\Registrar as RegistrarContract;
use SimpleOMS\Customer_Credit;
use SimpleOMS\Customer;
use SimpleOMS\Setting;
use SimpleOMS\User;
use Validator;
use Auth;
use Hash;

class Registrar implements RegistrarContract {

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		//
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	public function create(array $data)
	{
        //
	}
}