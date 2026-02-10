<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $table = 'class_sessions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'class_id',
        'day',
        'start_at',
        'end_at',
    ];
}
