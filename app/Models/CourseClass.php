<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    protected $table = 'classes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'instructor_id',
        'course_id',
        'section',
    ];
}
