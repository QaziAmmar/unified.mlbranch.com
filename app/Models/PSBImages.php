<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PSBImages extends Model
{
    use HasFactory;

    protected $fillable = [
        'psb_id',
        'image_link'
    ];
}
