<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;

class DeleteAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:delete {admin-id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Validate input
        $validator = Validator::make($this->arguments(), [
            'admin-id' => ['required', 'integer', 'exists:admins,id']
        ]);

        // Return error message if the validation fails
        if ($validator->fails()) {
            return $this->info($validator->messages());
        }

        // Get the validated data
        $data = $validator->validated();

        // Delete the account
        Admin::find($data['admin-id'])->delete();

        return $this->info("Admin account deleted.");
    }
}
