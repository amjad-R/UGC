<?php

namespace App\Http\Controllers;

use App\Console\Commands\SaveMonthlyStats;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\MonthlyStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use App\Models\UserStatistic;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'email' => 'required|email',
            'website' => 'nullable|url',
            'message' => 'required|string',
            'facebook' => 'nullable|integer',
            'instagram' => 'nullable|integer',
            'tiktok' => 'nullable|integer',
            'youtube' => 'nullable|integer',
            'platform_data' => 'nullable|json',
            'price' => 'nullable|numeric',
            'status' => 'nullable',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $orderData = array_merge($validated, [
            'status' => 'pending',
            'user_id' => $service->user_id,
            'creator_id' => $user->id,
        ]);

        $order = Order::create($orderData);

        return response()->json(['message' => 'Order saved successfully!', 'data' => $order], 201);
    }


    public function updateStatus($orderId, Request $request)
    {
        $request->validate([
            'status' => 'required|in:accepted,cancelled',
        ]);

        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if ($order) {
            $order->status = $request->status;
            $order->save();

            return response()->json(['message' => 'Order status updated successfully!', 'data' => $order], 200);
        }

        return response()->json(['message' => 'Order not found'], 404);
    }

    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::where('user_id', Auth::id())
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        return response()->json(['data' => $orders], 200);
    }

    public function showOrderDetails($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found or unauthorized'], 404);
        }

        $platformData = json_decode($order->platform_data, true);

        return response()->json([
            'data' => [
                'first_name' => $order->first_name,
                'last_name' => $order->last_name,
                'start_date' => $order->start_date,
                'end_date' => $order->end_date,
                'email' => $order->email,
                'website' => $order->website,
                'message' => $order->message,
                'platform_data' => $platformData,
                'price' => $order->price,
                'status' => $order->status,
            ]
        ], 200);
    }





    public function getOrderDetails($serviceId)
    {
        if (!$serviceId || !is_numeric($serviceId)) {
            return response()->json(['error' => 'Invalid Service ID'], 400);
        }

        $service = Service::find($serviceId);

        if (!$service) {
            return response()->json(['error' => "Service with ID $serviceId not found"], 404);
        }

        $order = [
            'item' => $service->title,
            'fullName' => 'John Doe',
            'startDate' => '2025-01-10',
            'endDate' => '2025-01-15',
            'email' => 'johndoe@example.com',
            'website' => 'www.example.com',
            'message' => 'This is a sample message.',
            'platforms' => $service->platforms ?? [],
        ];

        return response()->json($order);
    }

    // public function getUserOrders(Request $request)
    // {
    //     $user = Auth::user();
    //     if (!$user) {
    //         return response()->json(['message' => 'Unauthenticated'], 401);
    //     }

    //     $status = $request->query('status', 'all');

    //     // جلب الطلبات بناءً على الحالة
    //     $orders = Order::where('creator_id', $user->id)
    //         ->when($status !== 'all', function ($query) use ($status) {
    //             $query->where('status', $status);
    //         })
    //         ->get();

    //     return response()->json(['data' => $orders], 200);
    // }
    public function showOrderDetails2($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found or unauthorized'], 404);
        }

        return response()->json([
            'data' => [
                'service_name' => $order->service->title ?? 'N/A',
                'first_name' => $order->first_name,
                'last_name' => $order->last_name,
                'email' => $order->email,
                'price' => $order->price,
                'message' => $order->message,
                'status' => $order->status,
            ],
        ], 200);
    }

    public function getUserOrders(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $status = $request->query('status', 'all');

        $orders = Order::where('creator_id', $user->id)
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found for the specified status.'], 404);
        }

        return response()->json(['data' => $orders], 200);
    }

    public function getOrderDetails2($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $order = Order::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found or unauthorized'], 404);
        }

        return response()->json(['data' => $order], 200);
    }


    public function getStats()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $transactions = Order::where('creator_id', $user->id)->sum('price');

        $profit = Order::where('creator_id', $user->id)->count();

        $payments = Order::where('creator_id', $user->id)
            ->where('status', 'accepted')
            ->count();

        $sales = Service::where('user_id', $user->id)->count();

        return response()->json([
            'transactions' => $transactions,
            'profit' => $profit,
            'payments' => $payments,
            'sales' => $sales,
        ], 200);
    }

    public function getStatistics(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $transactions = Order::where('user_id', $user->id)->sum('price');
        $payments = Order::where('user_id', $user->id)->where('status', 'accepted')->count();
        $profit = Order::where('user_id', $user->id)->count();
        $sales = Service::where('user_id', $user->id)->count();

        $previousStats = UserStatistic::where('user_id', $user->id)->first();

        if (!$previousStats) {
            UserStatistic::create([
                'user_id' => $user->id,
                'transactions' => $transactions,
                'payments' => $payments,
                'profit' => $profit,
                'sales' => $sales,
                'transactions_change' => 0,
                'payments_change' => 0,
                'profit_change' => 0,
                'sales_change' => 0,
            ]);

            return response()->json([
                'transactions' => $transactions,
                'payments' => $payments,
                'profit' => $profit,
                'sales' => $sales,
                'transactionsChange' => 0,
                'paymentsChange' => 0,
                'profitChange' => 0,
                'salesChange' => 0,
            ]);
        }

        $transactionsChange = ($transactions != $previousStats->transactions)
            ? round((($transactions - $previousStats->transactions) / $previousStats->transactions) * 100, 2)
            : $previousStats->transactions_change;

        $paymentsChange = ($payments != $previousStats->payments)
            ? round((($payments - $previousStats->payments) / $previousStats->payments) * 100, 2)
            : $previousStats->payments_change;

        $profitChange = ($profit != $previousStats->profit)
            ? round((($profit - $previousStats->profit) / $previousStats->profit) * 100, 2)
            : $previousStats->profit_change;

        $salesChange = ($sales != $previousStats->sales)
            ? round((($sales - $previousStats->sales) / $previousStats->sales) * 100, 2)
            : $previousStats->sales_change;

        UserStatistic::where('user_id', $user->id)->update([
            'transactions' => $transactions,
            'payments' => $payments,
            'profit' => $profit,
            'sales' => $sales,
            'transactions_change' => $transactionsChange,
            'payments_change' => $paymentsChange,
            'profit_change' => $profitChange,
            'sales_change' => $salesChange,
        ]);

        return response()->json([
            'transactions' => $transactions,
            'payments' => $payments,
            'profit' => $profit,
            'sales' => $sales,
            'transactionsChange' => $transactionsChange,
            'paymentsChange' => $paymentsChange,
            'profitChange' => $profitChange,
            'salesChange' => $salesChange,
        ]);
    }


    public function getChartData()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $dailyOrders = Order::selectRaw('DATE(created_at) as order_day, COUNT(*) as order_count')
            ->where('user_id', $user->id)
            ->groupBy('order_day')
            ->orderBy('order_day', 'asc')
            ->get()
            ->map(function ($order) {
                return [
                    'day' => $order->order_day,
                    'count' => (int)$order->order_count,
                ];
            });

        $orderStatus = Order::selectRaw("
        SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as approved_orders,
        COUNT(*) as total_orders
    ")
            ->where('user_id', $user->id)
            ->first();

        $approvedOrders = (int)($orderStatus->approved_orders ?? 0);
        $totalOrders = (int)($orderStatus->total_orders ?? 0);

        if ($approvedOrders > $totalOrders) {
            $approvedOrders = $totalOrders;
        }

        return response()->json([
            'dailyOrders' => $dailyOrders,
            'orderStatus' => [
                'approved_orders' => $approvedOrders,
                'total_orders' => $totalOrders,
            ],
        ], 200);
    }

    public function getDashboardStats()
    {
        $businessUsersCount = User::where('user_type', 'business')->count();
        $creatorUsersCount = User::where('user_type', 'creator')->count();
        $servicesCount = Service::count();
        $totalRevenue = Order::sum('price');

        $previousStats = MonthlyStat::where('month', now()->subMonth()->startOfMonth())->first();

        $previousRevenue = $previousStats->revenue ?? 0;
        $previousServices = $previousStats->services_count ?? 0;
        $previousBusinessUsers = $previousStats->business_users ?? 0;
        $previousCreatorUsers = $previousStats->creator_users ?? 0;

        $revenueGrowth = $previousRevenue > 0 ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        $servicesGrowth = $previousServices > 0 ? (($servicesCount - $previousServices) / $previousServices) * 100 : 0;
        $businessGrowth = $previousBusinessUsers > 0 ? (($businessUsersCount - $previousBusinessUsers) / $previousBusinessUsers) * 100 : 0;
        $creatorGrowth = $previousCreatorUsers > 0 ? (($creatorUsersCount - $previousCreatorUsers) / $previousCreatorUsers) * 100 : 0;

        return response()->json([
            'business_users' => $businessUsersCount,
            'business_growth' => round($businessGrowth, 2),
            'creator_users' => $creatorUsersCount,
            'creator_growth' => round($creatorGrowth, 2),
            'services_count' => $servicesCount,
            'services_growth' => round($servicesGrowth, 2),
            'total_revenue' => $totalRevenue,
            'revenue_growth' => round($revenueGrowth, 2),
        ]);
    }

    public function getMonthlyUserStats()
    {
        $months = [];
        $userCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('F Y');
            $months[] = $month;

            $userCount = User::whereYear('created_at', Carbon::now()->subMonths($i)->year)
                ->whereMonth('created_at', Carbon::now()->subMonths($i)->month)
                ->count();

            $userCounts[] = $userCount;
        }

        return response()->json([
            'months' => $months,
            'user_counts' => $userCounts,
        ]);
    }
}
