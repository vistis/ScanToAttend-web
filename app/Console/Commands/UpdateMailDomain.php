<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateMailDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:update-domain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the domain of existing email to a new one set in .env';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $students = Student::select('username')->get();
        foreach ($students as $student) {
            Student::where('username', $student->username)->update([
                'email' => generateEmail($student->username)
            ]);
        }

        $instructors = Instructor::select('username')->get();
        foreach ($instructors as $instructor) {
            Instructor::where('username', $instructor->username)->update([
                'email' => generateEmail($instructor->username)
            ]);
        }

        $admins = Admin::select('username')->get();
        foreach ($admins as $admin) {
            Admin::where('username', $admin->username)->update([
                'email' => generateEmail($instructor->username)
            ]);
        }

        $this->info("Mail domain updated");
    }
}
