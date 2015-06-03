<?php namespace SimpleOMS\Services;

use SimpleOMS\User;
use SimpleOMS\Customer;
use SimpleOMS\Customer_Credit;
use SimpleOMS\Setting;
use Validator;
use Auth;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;

class Registrar implements RegistrarContract {

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		return Validator::make($data, [
			'name' => 'required|max:255|unique:users,name',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
			'role_id' => 'required|exists:roles,id',
			'first_name' => 'required',
			'last_name' => 'required',
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	public function create(array $data)
	{
        $user = new User();
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => $data['role_id']
        ]);

        // Save customer profile
        $customer = new Customer();
        $customer->fill([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'company_id' => Auth::user()->customer->company_id
        ]);
        $customer->save();

        // Save user profile
        $user->customer_id = $customer->id;
        $user->save();

        // Create credit record for administrator and sales roles
        if ($user->hasRole(['administrator', 'sales'])){
            $default_credit = Setting::getValue('DEFAULT_CREDIT');

            $credit = new Customer_Credit();
            $credit->fill([
                'customer_id' => $customer->id,
                'credit_amount' => $default_credit,
                'credit_remaining' => $default_credit
            ]);
            $credit->save();
        }

        return $user;
	}

}
