<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    use HasFactory;

    protected $fillable =
    [   'user_id',
        'change_gender_filter',
        'remove_ads',
        'create_business',
        'unlimited_matches',
        'unlimited_swipes',
        'spotlight',
        'get_featured',
        'message',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

}
