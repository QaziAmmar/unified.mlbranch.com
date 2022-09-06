<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'institute_id',
        'course',
        'city'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'institute_id' => 'string'
    ];
}
