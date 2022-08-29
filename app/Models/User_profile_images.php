<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_profile_images extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'picture'
    ];

}
