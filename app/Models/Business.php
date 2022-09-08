<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    public function post_products()
    {
        # code...
        return $this->hasMany(Product::class);
    }

    protected $fillable = [
        'user_id',
        'name',
        'location_name',
        "lat",
        "long",
        "description",
        "bannar_img",
        "firebase_id",
        "business_img"
        
    ];
}
