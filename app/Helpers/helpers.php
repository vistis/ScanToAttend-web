<?php

use App\Models\ClassSession;
use App\Models\Username;
use Illuminate\Database\Eloquent\Collection;

/**
 * Generate a username for a given first name and last name.
 */
function generateUsername($firstName, $lastName): string
{
    $username = strtolower(substr($firstName, 0, 1) . $lastName);

    /** Check if this username has already been generated before. */
    $record = Username::where('initial', $username);
    if ($record->exists())
    {
        /** If it does, append the historical count of same username generation and update the record. */
        $recordRow = $record->first();
        $username = $username . $recordRow->count;
        $record->update([
            'count' => ($recordRow->count + 1)
        ]);
    }
    else
    {
        /**  If not, record the new username occurance. */
        Username::create([
            'initial' => $username,
            'count' => 1
        ]);
    }

    return $username;
}

/**
 * Generate an email address for a given username
 * with the domain set in the app environment.
 */
function generateEmail($username): string
{
    return $username . '@' . env('MAIL_DOMAIN', 'university.edu');
}

/**
 * Retrieve the sessions of a given class.
 */
function getClassSessions($classId): Collection
{
    $sessions = ClassSession::where('class_id', $classId)
        ->select('id', 'day', 'start_at', 'end_at')
        ->get();

    return $sessions;
}
