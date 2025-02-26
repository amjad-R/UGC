<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonthlyStat; // استيراد النموذج الخاص بالإحصائيات الشهرية
use App\Models\User;        // استيراد نموذج المستخدم
use App\Models\Service;     // استيراد نموذج الخدمة
use App\Models\Order;       // استيراد نموذج الطلب
use Carbon\Carbon;          // استيراد مكتبة Carbon للتعامل مع التواريخ

class SaveMonthlyStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:save-monthly'; // يجب أن يكون مطابقًا للجدولة

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save monthly statistics for the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentMonth = Carbon::now()->startOfMonth();

        try {
            // احفظ الإحصائيات الشهرية
            MonthlyStat::updateOrCreate(
                ['month' => $currentMonth],
                [
                    'business_users' => User::where('user_type', 'business')->count(),
                    'creator_users' => User::where('user_type', 'creator')->count(),
                    'services_count' => Service::count(),
                    'revenue' => Order::sum('price'),
                ]
            );

            $this->info('Monthly stats saved successfully!');
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
