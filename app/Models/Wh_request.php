<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Wh_request extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'wh_request';
    protected $primaryKey = 'wh_request_id';
    protected $fillable = [
        'year',
        'request_date', 
    ];

  
}
