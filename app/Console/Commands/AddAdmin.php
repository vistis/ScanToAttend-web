<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AddAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:add {first-name} {last-name} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add an admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /** Validate input. */
        $validator = Validator::make($this->arguments(), [
            'first-name' => ['required', 'string', 'max:255'],
            'last-name' => ['required', 'string', 'max:255'],
            'password' => ['required', Password::defaults()]
        ]);

        if ($validator->fails())
        {
            return $this->info($validator->messages());
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Set data properties to be used for the new account. */
        $data['first_name'] = $data['first-name'];
        $data['last_name'] = $data['last-name'];
        $data['username'] = generateUsername($data['first_name'], $data['last_name']);
        $data['email'] = generateEmail($data['username']);

        /** Create a new admin account using the data. */
        $account = Admin::create($data);

        $this->info("Account added.");
    }
}
