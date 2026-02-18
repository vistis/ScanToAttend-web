<?php

use App\Models\Student;
use App\Models\Instructor;
use App\Models\Admin;
use App\Models\Username;

function generateUsername($firstName, $lastName) {
    // Username is the first letter of the first name, followed by the full last name all lowercase
    $username = strtolower(substr($firstName, 0, 1)) . strtolower($lastName);

    // Check if this username has already been generated
    $record = Username::where('initial', $username);
    if ($record->exists()) {
        // If it does, append the historical count of same username generation
        $recordRow = $record->first();
        $username = $username . $recordRow->count;

        // Update the username count
        $record->update([
            'count' => ($recordRow->count + 1)
        ]);
    }
    else {
        // If not, record the new username
        Username::create([
            'initial' => $username,
            'count' => 1
        ]);
    }

    return $username;
}

function generateEmail($username) {
    return $username . '@' . env('MAIL_DOMAIN', 'university.edu');
}

function getClassSessions($classId) {
    $sessions = ClassSession::where('class_id', $classId)
        ->select('id', 'day', 'start_at', 'end_at')
        ->get();

    return $sessions;
}
