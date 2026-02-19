<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class RemoveAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:remove {admin-id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove an admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /** Validate input. */
        $validator = Validator::make($this->arguments(), [
            'admin-id' => ['required', 'integer', 'exists:admins,id']
        ]);

        if ($validator->fails())
        {
            return $this->info($validator->messages());
        }

        /** Get the validated data. */
        $data = $validator->validated();

        /** Delete the account. */
        Admin::where('id', $data['admin-id'])->delete();

        return $this->info("Admin account deleted.");
    }
}
