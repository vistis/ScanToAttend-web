<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class ChangeAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:change-password {admin-id} {new-password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the password of an admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Validate input
        $validator = Validator::make($this->arguments(), [
            'admin-id' => ['required', 'integer', 'exists:admins,id'],
            'new-password' => ['required', Password::defaults()]
        ]);

        // Return error message if the validation fails
        if ($validator->fails()) {
            return $this->info($validator->messages());
        }

        // Get the validated data
        $data = $validator->validated();

        // Hashify the new password
        $password = Hash::make($data['new-password']);

        // Update the passowrd
        Admin::where('id', $data['id'])
            ->update([
                'password' => $password
            ]);

        return $this->info("Password changed.");
    }
}
