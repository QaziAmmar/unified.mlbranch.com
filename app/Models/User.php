<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    public function post_psbs()
    {
        # code...
        return $this->hasMany(PSB::class);
    }

    public function profile_sub_images()
    {
        # code...
        return $this->hasMany(User_profile_images::class);
    }

    public function institute()
    {
        return $this->hasOne(Institute::class);
    }

    public function favourite_products()
    {
        return $this->hasMany(FavouriteProducts::class);
    }

    public function education()
    {
        return $this->hasMany(Education::class);
    }


    public function suggestions()
    {
        return $this->belongsToMany(User::class, 'suggestions', 'friend_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        "age",
        "role",
        "looking_for",
        "gender",
        "firebase_id",
        "profile_pic",
        "bio",
        "institute_id"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
    ];



    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'skills' => 'array',
        'interstes' => 'array',
    ];

    public function getProfilePicAttribute($value)
    {
        $value = asset('storage/' . $value);
        return $value;
    }
}
