<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessExternalLinks extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_id',
        "external_link",
        "category",
    ];
}
