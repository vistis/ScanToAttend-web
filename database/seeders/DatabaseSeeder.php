<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Instructor;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /**
         * ── Admin ──
         */
        Admin::create([
            'first_name' => 'System',
            'last_name' => 'Admin',
            'username' => 'admin',
            'email' => generateEmail('admin'),
            'password' => 'password',
        ]);

        /**
         * ── Instructors (3) ──
         */
        $instructors = [
            ['first_name' => 'John', 'last_name' => 'Smith'],
            ['first_name' => 'Sarah', 'last_name' => 'Johnson'],
            ['first_name' => 'Michael', 'last_name' => 'Chen'],
        ];

        foreach ($instructors as $data)
        {
            $username = generateUsername($data['first_name'], $data['last_name']);
            Instructor::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'username' => $username,
                'email' => generateEmail($username),
                'password' => 'password',
            ]);
        }

        /**
         * ── Students (12) – no fingerprint yet (must register on device first) ──
         */
        $students = [
            ['first_name' => 'Alice', 'last_name' => 'Wang'],
            ['first_name' => 'Bob', 'last_name' => 'Martinez'],
            ['first_name' => 'Charlie', 'last_name' => 'Lee'],
            ['first_name' => 'Diana', 'last_name' => 'Patel'],
            ['first_name' => 'Ethan', 'last_name' => 'Brown'],
            ['first_name' => 'Fiona', 'last_name' => 'Davis'],
            ['first_name' => 'George', 'last_name' => 'Wilson'],
            ['first_name' => 'Hannah', 'last_name' => 'Taylor'],
            ['first_name' => 'Ivan', 'last_name' => 'Anderson'],
            ['first_name' => 'Julia', 'last_name' => 'Thomas'],
            ['first_name' => 'Aaron', 'last_name' => 'Wang'],       // awang1 (duplicate with Alice Wang)
            ['first_name' => 'Amy', 'last_name' => 'Wang'],         // awang2 (another duplicate)
        ];

        foreach ($students as $data)
        {
            $username = generateUsername($data['first_name'], $data['last_name']);
            Student::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'username' => $username,
                'email' => generateEmail($username),
                'password' => 'password',
            ]);
        }

        /**
         * ── Courses (5) ──
         */
        $courses = [
            ['code' => 'CS101', 'name' => 'Introduction to Computer Science'],
            ['code' => 'CS201', 'name' => 'Data Structures and Algorithms'],
            ['code' => 'CS301', 'name' => 'Database Systems'],
            ['code' => 'CS397', 'name' => 'IoT Systems'],
            ['code' => 'MATH201', 'name' => 'Linear Algebra'],
        ];

        foreach ($courses as $data)
        {
            Course::create($data);
        }

        /**
         * ── Classes (8) ──
         * Assign instructors to classes.
         */
        $classes = [
            ['course_id' => 1, 'section' => 1, 'instructor_id' => 1],  // CS101-1  → Smith
            ['course_id' => 1, 'section' => 2, 'instructor_id' => 2],  // CS101-2  → Johnson
            ['course_id' => 2, 'section' => 1, 'instructor_id' => 1],  // CS201-1  → Smith
            ['course_id' => 3, 'section' => 1, 'instructor_id' => 2],  // CS301-1  → Johnson
            ['course_id' => 3, 'section' => 2, 'instructor_id' => 3],  // CS301-2  → Chen
            ['course_id' => 4, 'section' => 1, 'instructor_id' => 3],  // CS397-1  → Chen
            ['course_id' => 5, 'section' => 1, 'instructor_id' => 1],  // MATH201-1 → Smith
            ['course_id' => 5, 'section' => 2, 'instructor_id' => 2],  // MATH201-2 → Johnson
        ];

        foreach ($classes as $data)
        {
            CourseClass::create($data);
        }

        /**
         * ── Class Sessions ──
         * Each class meets 2–3 times per week.
         */
        $sessions = [
            // CS101-1 (class 1): Mon & Wed 08:00–09:30
            ['class_id' => 1, 'day' => 'Monday',    'start_at' => '08:00:00', 'end_at' => '09:30:00'],
            ['class_id' => 1, 'day' => 'Wednesday',  'start_at' => '08:00:00', 'end_at' => '09:30:00'],

            // CS101-2 (class 2): Tue & Thu 08:00–09:30
            ['class_id' => 2, 'day' => 'Tuesday',   'start_at' => '08:00:00', 'end_at' => '09:30:00'],
            ['class_id' => 2, 'day' => 'Thursday',  'start_at' => '08:00:00', 'end_at' => '09:30:00'],

            // CS201-1 (class 3): Mon & Wed & Fri 10:00–11:30
            ['class_id' => 3, 'day' => 'Monday',    'start_at' => '10:00:00', 'end_at' => '11:30:00'],
            ['class_id' => 3, 'day' => 'Wednesday',  'start_at' => '10:00:00', 'end_at' => '11:30:00'],
            ['class_id' => 3, 'day' => 'Friday',    'start_at' => '10:00:00', 'end_at' => '11:30:00'],

            // CS301-1 (class 4): Tue & Thu 10:00–11:30
            ['class_id' => 4, 'day' => 'Tuesday',   'start_at' => '10:00:00', 'end_at' => '11:30:00'],
            ['class_id' => 4, 'day' => 'Thursday',  'start_at' => '10:00:00', 'end_at' => '11:30:00'],

            // CS301-2 (class 5): Mon & Wed 13:00–14:30
            ['class_id' => 5, 'day' => 'Monday',    'start_at' => '13:00:00', 'end_at' => '14:30:00'],
            ['class_id' => 5, 'day' => 'Wednesday',  'start_at' => '13:00:00', 'end_at' => '14:30:00'],

            // CS397-1 (class 6): Tue & Thu 13:00–14:30
            ['class_id' => 6, 'day' => 'Tuesday',   'start_at' => '13:00:00', 'end_at' => '14:30:00'],
            ['class_id' => 6, 'day' => 'Thursday',  'start_at' => '13:00:00', 'end_at' => '14:30:00'],

            // MATH201-1 (class 7): Mon & Wed & Fri 15:00–16:30
            ['class_id' => 7, 'day' => 'Monday',    'start_at' => '15:00:00', 'end_at' => '16:30:00'],
            ['class_id' => 7, 'day' => 'Wednesday',  'start_at' => '15:00:00', 'end_at' => '16:30:00'],
            ['class_id' => 7, 'day' => 'Friday',    'start_at' => '15:00:00', 'end_at' => '16:30:00'],

            // MATH201-2 (class 8): Tue & Thu 15:00–16:30
            ['class_id' => 8, 'day' => 'Tuesday',   'start_at' => '15:00:00', 'end_at' => '16:30:00'],
            ['class_id' => 8, 'day' => 'Thursday',  'start_at' => '15:00:00', 'end_at' => '16:30:00'],
        ];

        foreach ($sessions as $data)
        {
            ClassSession::create($data);
        }

    }
}
