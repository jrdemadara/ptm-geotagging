<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $table = "profiles";
    protected $fillable = [
        "precinct",
        "lastname",
        "firstname",
        "middlename",
        "extension",
        "birthdate",
        "occupation",
        "phone",
        "lat",
        "lon",
        "municipality",
        "barangay",
        "purok",
        "qrcode",
        "has_ptmid",
        "is_muslim",
        "user_id",
    ];
}
