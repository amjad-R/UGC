<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\platform;
use App\Models\Wishlist;


class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'title',
        'platforms',
        'category',
        'description',
        'hashtags',
        'is_free',
        'price',
        'uploaded_files',
    ];

    protected $casts = [
        'platforms' => 'array',
        'uploaded_files' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }


    public function platforms()
    {
        return $this->hasMany(platform::class);
    }
    public function Wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'service_id');
    }


    protected static function booted()
    {
        static::saved(function ($service) {
            event(new \App\Events\StatisticsUpdated($service->user_id));
        });

        static::deleted(function ($service) {
            event(new \App\Events\StatisticsUpdated($service->user_id));
        });
    }
}
