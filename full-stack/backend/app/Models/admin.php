<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
     /**
     * The attributes that should be hidden for arrays and JSON responses.
     *
     * @var array
     */
    protected $hidden = ['password'];
    protected $fillable = ['name', 'email','password','role'];

}
