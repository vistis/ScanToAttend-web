<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Username extends Model
{
    protected $table = 'usernames';
    protected $primaryKey = 'id';

    protected $fillable = [
        'initial',
        'count'
    ];
}
