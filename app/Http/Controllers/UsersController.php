<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Http\Requests;
use SimpleOMS\Helpers\Helpers;
use SimpleOMS\Customer;
use SimpleOMS\Customer_Credit;
use SimpleOMS\User;
use SimpleOMS\Role;
use Redirect;
use Session;
use Input;
use Hash;
use Auth;

class UsersController extends Controller {

    public function create()
    {
        // Get list of roles
        $roles = Role::all();

        $title = 'Register User';

        return view('auth.register', compact('roles', 'title'));
    }

    public function store(Requests\StoreUserRequest $request)
    {
        $user = new User();
        $user->fill([
            'name' => Input::get('name'),
            'email' => Input::get('email'),
            'password' => Hash::make(DEFAULT_PW),
            'role_id' => Input::get('role_id')
        ]);
        // Save user profile
        $user->save();

        // Save customer profile
        $customer = new Customer();
        $customer->fill([
            'id' => $user->id,
            'first_name' => Input::get('customer')['first_name'],
            'middle_name' => Input::get('customer')['middle_name'],
            'last_name' => Input::get('customer')['last_name'],
            'company_id' => Auth::user()->customer->company_id,
        ]);
        $customer->save();

        // Create credit record for administrator and sales roles
        if ($user->hasRole(['administrator', 'sales'])){
            $credit = new Customer_Credit();
            $credit->fill([
                'customer_id' => $user->id,
                'credit_remaining' => DEFAULT_CREDIT
            ]);
            $credit->save();
        }

        Session::flash('success', 'User "'.$user->name.'" was registered successfully.');
        return redirect('users/create');
    }

    /**
     * Update user form
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        // Get customer record
        $user->customer;

        $title = 'Update User';

        $hash = Helpers::hash($user->id);

        return view('auth.register', compact('user', 'title', 'hash'));
    }

    /**
     * Update user form
     * @return \Illuminate\View\View
     */
    public function update(Requests\StoreUserRequest $request, User $user)
    {
        // Check if password is correct
        if (!Hash::check(Input::get('current_password'), $user->password))
        {
            Session::flash('error_message', "Wrong password.");
            return Redirect::back()->withInput(Input::all());
        }

        // Get customer record
        $customer = $user->customer;

        // Check if user wants to change password
        $password = Input::get('password') != '' ? Input::get('password') : Input::get('current_password');

        $user->fill([
            'name' => Input::get('name'),
            'email' => Input::get('email'),
            'password' => Hash::make($password)
        ]);
        $user->update();

        $customer->fill([
            'first_name' => Input::get('customer')['first_name'],
            'middle_name' => Input::get('customer')['middle_name'],
            'last_name' => Input::get('customer')['last_name']
        ]);
        $customer->update();

        Session::flash('success', 'User "'.$user->name.'" was updated successfully.');
        return redirect('users/'.Input::get('hash').'/edit');
    }
}
