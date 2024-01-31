<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;
    protected $database = 'mysql_tupaics';
    protected $fillable = [
        'precintno',
        'lastname',
        'firstname',
        'middlename',
        'extension',
        'birthdate',
        'phone',
    ];
}
