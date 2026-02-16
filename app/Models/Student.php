<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Student extends Model
{
    use HasApiTokens;

    protected $table = 'students';
    protected $primaryKey = 'id';

    protected $fillable = [
        'fingerprint_id',
        'first_name',
        'last_name',
        'username',
        'profile_picture',
        'email',
        'password'
    ];

    protected $hidden = [
        'fingerprint_id',
        'password',
        'remember_token'
    ];

    protected function casts(): array {
        return [
            'password' => 'hashed'
        ];
    }
}
