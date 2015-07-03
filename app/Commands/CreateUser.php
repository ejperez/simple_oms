<?php namespace SimpleOMS\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use SimpleOMS\User;
use SimpleOMS\Customer;
use SimpleOMS\Customer_Credit;
use Hash;

class CreateUser extends Command implements SelfHandling
{
    protected $name;
    protected $email;
    protected $role_id;
    protected $customer;
    protected $company_id;
    protected $password;
    protected $credits;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($name, $email, $role_id, $password, $credits, array $customer, $company_id)
	{
        $this->name        = $name;
        $this->email       = $email;
        $this->role_id     = $role_id;
        $this->customer    = $customer;
        $this->company_id  = $company_id;
        $this->password    = $password;
        $this->credits     = $credits;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
        $user = new User();
        $user->fill([
            'name'      => $this->name,
            'email'     => $this->email,
            'password'  => Hash::make($this->password),
            'role_id'   => $this->role_id
        ]);
        // Save user profile
        $user->save();

        // Save customer profile
        $customer = new Customer();
        $customer->fill([
            'id'            => $user->id,
            'first_name'    => $this->customer['first_name'],
            'middle_name'   => $this->customer['middle_name'],
            'last_name'     => $this->customer['last_name'],
            'company_id'    => $this->company_id,
        ]);
        $customer->save();

        // Create credit record for administrator and sales roles
        if ($user->hasRole(['administrator', 'sales'])){
            $credit = new Customer_Credit();
            $credit->fill([
                'customer_id'       => $user->id,
                'credit_remaining'  => $this->credits
            ]);
            $credit->save();
        }

        \SimpleOMS\Audit_Log::create(['user_id' => \Auth::user()->id, 'activity' => 'Created user '.$user->name, 'data' => json_encode($user->toArray())]);

        return $user;
	}
}