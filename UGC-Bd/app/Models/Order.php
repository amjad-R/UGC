<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id',
        'service_id',
        'first_name',
        'last_name',
        'start_date',
        'end_date',
        'email',
        'website',
        'message',
        'facebook',
        'instagram',
        'tiktok',
        'youtube',
        'platform_data',
        'price',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function booted()
    {
        static::saved(function ($order) {
            event(new \App\Events\StatisticsUpdated($order->user_id));
        });

        static::deleted(function ($order) {
            event(new \App\Events\StatisticsUpdated($order->user_id));
        });
    }
}
