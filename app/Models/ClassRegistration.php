<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRegistration extends Model
{
    protected $table = 'class_registrations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'student_id',
        'class_id'
    ];
}
