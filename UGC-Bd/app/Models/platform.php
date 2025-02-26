<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class platform extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_id',
        'platform',
        'video_type',
        'duration',
        'price',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
