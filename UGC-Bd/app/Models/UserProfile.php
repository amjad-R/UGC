<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'instagram',
        'facebook',
        'tiktok',
        'youtube',
        'service_description',
        'hashtags',
        'identity_verification',
        'profile_image',
        'global_followers',
        'category',
        'select_price',
        'video_type',
        'role',
        'is_online',
        'last_seen',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'last_seen' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
}
