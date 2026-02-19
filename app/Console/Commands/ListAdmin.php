<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class ListAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admins = Admin::select('id', 'first_name', 'last_name', 'username', 'created_at')
            ->orderByDesc('first_name')
            ->get()
            ->toArray();

        $this->table(["ID", "First Name", "Last Name", "Username", "Created At"], $admins);

        return 0;
    }
}
