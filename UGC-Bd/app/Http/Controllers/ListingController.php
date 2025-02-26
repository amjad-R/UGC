<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        try {
            $listings = Service::all();

            if ($request->expectsJson()) {
                return response()->json($listings);
            }

            return view('listings.index', compact('listings'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'فشل في جلب الخدمات: ' . $e->getMessage()], 500);
        }
    }
}
