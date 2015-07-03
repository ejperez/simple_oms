<?php namespace SimpleOMS\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use SimpleOMS\User;
use SimpleOMS\Customer;
use Hash;

class UpdateUser extends Command implements SelfHandling
{
    protected $user;
    protected $name;
    protected $email;
    protected $customer;
    protected $password;
    protected $current_password;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, $name, $email, array $customer, $password, $current_password)
	{
       $this->user              = $user;
       $this->name              = $name;
       $this->email             = $email;
       $this->customer          = $customer;
       $this->password          = $password;
       $this->current_password  = $current_password;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
        // Check if password is correct
        if (!Hash::check($this->current_password, $this->user->password))
        {
            return "Wrong password.";
        }

        // Get customer record
        $customer = $this->user->customer;

        // Check if user wants to change password
        $password = $this->password != '' ? $this->password : $this->current_password;

        $this->user->fill([
            'name'      => $this->name,
            'email'     => $this->email,
            'password'  => Hash::make($password)
        ]);
        $this->user->update();

        $customer->fill([
            'first_name'    => $this->customer['first_name'],
            'middle_name'   => $this->customer['middle_name'],
            'last_name'     => $this->customer['last_name']
        ]);
        $customer->update();

        \SimpleOMS\Audit_Log::create(['user_id' => \Auth::user()->id, 'activity' => 'Updated user '.$this->user->name, 'data' => json_encode($this->user->toArray())]);

        return $this->user;
	}
}