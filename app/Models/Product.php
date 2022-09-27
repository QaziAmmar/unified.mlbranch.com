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
        return $this->belongsTo(Business::class)->select('id','user_id', 'name', 'location_name', 'lat', 'long', 'is_featured');
    }

    public function product_images()
    {
        return $this->hasMany(ProductImages::class, 'product_id')->select('id','product_id','image_link');
    }

    public function product_image()
    {
        return $this->hasOne(ProductImages::class,)->select('id','product_id','image_link');
    }

    public function like()
    {
       return $this->hasMany(FavouriteProducts::class, 'product_id');
    }

    public function is_liked($user_id)
    {
        return !! FavouriteProducts::where('user_id', $user_id)->first();
    }



    protected $fillable = [
        'business_id',
        'price',
        'title',
        "description",
        "is_service"
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];
}
