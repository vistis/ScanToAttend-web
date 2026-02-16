<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Username;

class UsernameController extends Controller
{
    /* GENERATE USERNAME */
    public function create($firstName, $lastName) {
        // Username is the first letter of the first name, followed by the full last name all lowercase
        $username = strtolower(substr($firstName, 0, 1)) . strtolower($lastName);

        // Check if this username has already been generated
        $record = Username::where('initial', $username);
        if ($record->exists()) {
            // If it does, the count of this username to have ever been created to the username
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
}
