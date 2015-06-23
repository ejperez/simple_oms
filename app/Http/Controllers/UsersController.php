<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Http\Requests;
use SimpleOMS\Helpers\Helpers;
use SimpleOMS\User;
use SimpleOMS\Role;
use Redirect;
use Illuminate\Http\Request;
use SimpleOMS\Commands\CreateUser;
use SimpleOMS\Commands\UpdateUser;
use Session;
use Input;
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
    public function create(Request $request)
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
        $response = $this->dispatchFrom(CreateUser::class, $request, [
            'company_id' => Auth::user()->customer->company_id
        ]);

        if ($response instanceof User){
            Session::flash('success', 'User "'.$response->name.'" was registered successfully.');
            return redirect('users/create');
        } else {
            Session::flash('error_message', $response);
            return Redirect::back()->withInput(Input::all());
        }
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
        $response = $this->dispatchFrom(UpdateUser::class, $request, [
            'user' => $user
        ]);

        if ($response instanceof User){
            Session::flash('success', 'User "'.$response->name.'" was updated successfully.');
            return redirect('users/'.Input::get('hash').'/edit');
        } else {
            Session::flash('error_message', $response);
            return Redirect::back()->withInput(Input::all());
        }
    }
}