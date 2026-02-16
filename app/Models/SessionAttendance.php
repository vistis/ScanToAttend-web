<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionAttendance extends Model
{
    protected $table = 'session_attendance';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'session_id',
        'checked_in_on',
        'checked_in_at',
        'status'
    ];
}
