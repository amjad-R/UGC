<?php

namespace App\Listeners;

use App\Events\StatisticsUpdated;
use App\Models\Order;
use App\Models\Service;
use App\Models\UserStatistic;
use Illuminate\Support\Facades\Log;

class UpdateUserStatistics
{
    public function handle(StatisticsUpdated $event)
    {
        $userId = $event->userId;

        $transactions = Order::where('user_id', $userId)->sum('price');
        $payments = Order::where('user_id', $userId)->where('status', 'accepted')->count();
        $profit = Order::where('user_id', $userId)->count();
        $sales = Service::where('user_id', $userId)->count();

        $previousStats = UserStatistic::where('user_id', $userId)->first();

        if (!$previousStats) {
            $previousStats = new UserStatistic([
                'transactions' => 0,
                'payments' => 0,
                'profit' => 0,
                'sales' => 0,
                'transactions_change' => 0,
                'payments_change' => 0,
                'profit_change' => 0,
                'sales_change' => 0,
            ]);
        }

        $transactionsChange = ($previousStats->transactions > 0 && $transactions != $previousStats->transactions)
            ? round((($transactions - $previousStats->transactions) / $previousStats->transactions) * 100, 2)
            : $previousStats->transactions_change;

        $paymentsChange = ($previousStats->payments > 0 && $payments != $previousStats->payments)
            ? round((($payments - $previousStats->payments) / $previousStats->payments) * 100, 2)
            : $previousStats->payments_change;

        $profitChange = ($previousStats->profit > 0 && $profit != $previousStats->profit)
            ? round((($profit - $previousStats->profit) / $previousStats->profit) * 100, 2)
            : $previousStats->profit_change;

        $salesChange = ($previousStats->sales > 0 && $sales != $previousStats->sales)
            ? round((($sales - $previousStats->sales) / $previousStats->sales) * 100, 2)
            : $previousStats->sales_change;

        UserStatistic::updateOrCreate(
            ['user_id' => $userId],
            [
                'transactions' => $transactions != $previousStats->transactions ? $transactions : $previousStats->transactions,
                'payments' => $payments != $previousStats->payments ? $payments : $previousStats->payments,
                'profit' => $profit != $previousStats->profit ? $profit : $previousStats->profit,
                'sales' => $sales != $previousStats->sales ? $sales : $previousStats->sales,
                'transactions_change' => $transactionsChange,
                'payments_change' => $paymentsChange,
                'profit_change' => $profitChange,
                'sales_change' => $salesChange,
            ]
        );

        Log::info("User statistics updated successfully for user {$userId}");
    }
}
