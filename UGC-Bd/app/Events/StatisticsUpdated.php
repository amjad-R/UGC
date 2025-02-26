<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class StatisticsUpdated
{
    use Dispatchable;

    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }
}
