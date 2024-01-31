<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'lastname',
        'firstname',
        'middlename',
        'extension',
        'birthdate',
        'occupation',
        'phone',
        'lat',
        'lon',
        'user_id',
    ];
}
