<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\Admin;

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
        // Validate input
        $validator = Validator::make($this->arguments(), [
            'first-name' => ['required', 'string', 'max:255'],
            'last-name' => ['required', 'string', 'max:255'],
            'password' => ['required', Password::defaults()]
        ]);

        // Return error message if the validation fails
        if ($validator->fails()) {
            return $this->info($validator->messages());
        }

        // Get the validated data
        $data = $validator->validated();
        $data['first_name'] = $data['first-name'];
        $data['last_name'] = $data['last-name'];

        // Generate username
        $data['username'] = generateUsername($data['first_name'], $data['last_name']);

        // Generate email
        $data['email'] = generateEmail($data['username']);

        // Create the admin account
        $account = Admin::create($data);

        // Success message
        $this->info("Admin added.");
    }
}
