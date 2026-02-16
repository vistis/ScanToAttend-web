<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassSession;

class ClassSessionController extends Controller
{
    /* GET SESSIONS OF A CLASS */
    public function read($classId) {
        $sessions = ClassSession::where('class_id', $classId)->get();
        $list = array();

        foreach ($sessions as $session) {
            $list[] = $session->day . ' ' . $session->start_at . '-' . $session->end_at;
        }

        return $list;
    }
}
