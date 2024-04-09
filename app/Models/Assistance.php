<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assistance extends Model
{
    use HasFactory;
    protected $table = 'assistance';
    protected $fillable = [
        'assistance',
        'amount',
        'released_at',
        'profile_id',
    ];
}
