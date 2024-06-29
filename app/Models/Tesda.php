<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tesda extends Model
{
    use HasFactory;
    protected $table = 'tesda';
    protected $fillable = [
        'name',
        'profile_id',
    ];
}
