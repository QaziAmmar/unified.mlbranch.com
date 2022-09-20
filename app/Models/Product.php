<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImages;

class Product extends Model
{
    use HasFactory;

    public function business()
    {
        return $this->hasOne(Business::class);
    }

    public function post_images()
    {
        return $this->hasMany(ProductImages::class);
    }

    protected $fillable = [
        'business_id',
        'price',
        'title',
        "description",
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];
}
