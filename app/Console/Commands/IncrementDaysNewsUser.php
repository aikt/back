<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\DB;


class IncrementDaysNewsUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:incrementdays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //get new users from users table
        $new_users = \DB::select("SELECT email, increment_days, email_new_user  FROM users;");

        foreach($new_users as $user)
        {
            # si es mayor/igual a 1 y menor/igual a 7 
            if($user->increment_days >= 0 &&
               $user->increment_days < 7)
            {
                $increment_day = \DB::update("UPDATE users SET increment_days = increment_days + 1 WHERE email = '{$user->email}';");
            }
            else
            {
                # cuando llega a los 7 dias pasa a ser usuario normal
                $change_mod_user = \DB::update("UPDATE users SET email_new_user = 0, increment_days = 1  WHERE email = '{$user->email}';");
            }
        }
        
    }
}
