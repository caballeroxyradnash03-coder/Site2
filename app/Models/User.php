<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class User extends Model
{
    public $timestamps = false;
    protected $table = 'users';
    // allow mass-assigning gender when creating users
    protected $fillable = ['username', 'password', 'gender'];

    // hide sensitive/internal fields from JSON responses
    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];
}


