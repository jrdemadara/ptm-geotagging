<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesdaCourse extends Model
{
    use HasFactory;
    protected $table = "tesda_course";
    protected $fillable = [
        'course',
        'tesda_id',
    ];
}
