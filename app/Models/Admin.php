<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'admins';
    protected $primaryKey = 'id';

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'profile_picture',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected function casts(): array {
        return [
            'password' => 'hashed'
        ];
    }
}
