<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $guard = 'admin';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

//    public function setPasswordAttribute($value)
//    {
//        $this->attributes['password'] = Hash::make('password');
//    }

    public function scopeIsActive($query)
    {
        return $query->where('is_active',1);
    }
}
