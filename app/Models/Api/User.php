<?php

namespace App\Models\Api;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{  
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'username',
        'email',
        'phone',
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
