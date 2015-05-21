<?php namespace SimpleOMS\Handlers\Events;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Session;

class AuthLoginEventHandler {

	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param  Events  $event
	 * @return void
	 */
	public function handle(\SimpleOMS\User $user, $remember)
	{
		// Get settings from database and store to session
        $settings = \SimpleOMS\Setting::all();

        foreach ($settings as $setting){
            Session::set($setting->s_key, $setting->s_value);
        }
	}

}