<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PSB extends Model
{
    use HasFactory;

    // one to many relation
    public function psb_images()
    {
        # code...
        return $this->hasMany(PSBImages::class, 'psb_id');
    }


    protected $fillable = [
        'user_id',
        'title',
        "description",
        'image',
    ];
}
