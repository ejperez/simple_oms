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
use DB;

class UsersController extends Controller
{
    public function index()
    {
        \DB::enableQueryLog();

        // Get query parameters
        $filters = json_decode(Input::get('f'));
        $sort_column = Input::has('s') ? Input::get('s') : 'created_at';
        $sort_direction = Input::has('d') ? Input::get('d') : 'desc';

        // Query view
        $users = DB::table('users_vw');

        // Search parameters
        if (isset($filters->username) && !empty($filters->username))
            $users->where('username', 'like', "%$filters->username%");

        if (isset($filters->email) && !empty($filters->email))
            $users->where('email', 'like', "%$filters->email%");

        if (isset($filters->name) && !empty($filters->name))
            $users->where('name', 'like', "%$filters->name%");

        if (isset($filters->created_at) && !empty($filters->created_at))
            $users->where('created_at', '=', $filters->created_at);

        if (isset($filters->role) && !empty($filters->role))
            $users->whereIn('role', $filters->role);

        // Sorting
        $users = $users->orderBy($sort_column, $sort_direction)
            ->paginate(PER_PAGE);

        // Get role
        $role = Auth::user()->role->name;

        // Get order status
        $roles = Role::all();

        $title = 'List of Users';

        return view('users.index', compact('title', 'users', 'role', 'roles', 'filters'));
    }
    /**
     * Show create user form
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get list of roles
        $roles = Role::all();

        $title = 'Register User';

        return view('auth.register', compact('roles', 'title'));
    }

    /**
     * Store user action
     * @param Requests\StoreUserRequest $request
     * @return Redirect
     */
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
