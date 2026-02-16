<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Instructor;
use App\Models\Admin;

class EmailController extends Controller
{
    /* GENERATE EMAIL */
    public function create($username) {
        return $username . '@' . env('MAIL_DOMAIN', 'university.edu');
    }

    /* UPDATE ALL ACCOUNT EMAIL TO A NEW DOMAIN SET IN ENVIRONMENT */
    public function updateAll() {
        $students = Student::select('username')->get();
        foreach ($students as $student) {
            Student::where('username', $student->username)->update([
                'email' => $student->username . '@' . env('MAIL_DOMAIN', 'university.edu')
            ]);
        }

        $instructors = Instructor::select('username')->get();
        foreach ($instructors as $instructor) {
            Instructor::where('username', $instructor->username)->update([
                'email' => $instructor->username . '@' . env('MAIL_DOMAIN', 'university.edu')
            ]);
        }

        $admins = Admin::select('username')->get();
        foreach ($admins as $admin) {
            Admin::where('username', $admin->username)->update([
                'email' => $admin->username . '@' . env('MAIL_DOMAIN', 'university.edu')
            ]);
        }

        return response()->json([
            'message' => "Email domain updated."
        ], 200);
    }
}
