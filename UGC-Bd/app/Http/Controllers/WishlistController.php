<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Service;

class WishlistController extends Controller
{
    public function addToWishlist(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $user = auth()->user();

        $exists = Wishlist::where('user_id', $user->id)
            ->where('service_id', $request->service_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'الفيديو محفوظ مسبقًا.'], 400);
        }

        Wishlist::create([
            'user_id' => $user->id,
            'service_id' => $request->service_id,
        ]);

        return response()->json(['message' => 'تم حفظ الفيديو بنجاح.']);
    }

    public function getWishlist()
    {
        $user = auth()->user();

        $wishlist = Wishlist::with(['service', 'service.user.profile'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) {
                $service = $item->service;

                if ($service) {
                    $uploadedFiles = json_decode($service->uploaded_files, true);
                    if ($uploadedFiles) {
                        $uploadedFiles = array_map(function ($file) {
                            return str_replace(['\\'], '', $file);
                        }, $uploadedFiles);
                    }
                }

                return [
                    'video_url' => $uploadedFiles[0] ?? 'fallback-video.mp4',
                    'creator_name' => $service->user
                        ? $service->user->first_name . ' ' . $service->user->last_name
                        : 'Unknown User',
                    'service_id' => $service->id ?? null,
                    'profile_image' => $service->user->profile->profile_image ?? 'default-profile-image.png', // جلب صورة البروفايل من الجدول المنفصل
                ];
            });

        return response()->json($wishlist);
    }
}
