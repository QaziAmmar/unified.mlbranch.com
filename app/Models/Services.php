<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    use HasFactory;


    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    protected $fillable = [
        'business_id',
        'price',
        'title',
        "description",
        "duration",
        
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

}
