<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function getLikes($serviceId)
    {
        $likesCount = Like::where('service_id', $serviceId)->count();
        $userLiked = Like::where('service_id', $serviceId)
            ->where('user_id', Auth::id())
            ->exists();

        return response()->json([
            'likes' => $likesCount,
            'isLiked' => $userLiked,
        ]);
    }

    public function toggleLike(Request $request)
    {
        $serviceId = $request->input('serviceId');
        $userId = Auth::id();

        $like = Like::where('service_id', $serviceId)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $isLiked = false;
        } else {
            Like::create([
                'service_id' => $serviceId,
                'user_id' => $userId,
            ]);
            $isLiked = true;
        }

        $likesCount = Like::where('service_id', $serviceId)->count();

        return response()->json([
            'likes' => $likesCount,
            'isLiked' => $isLiked,
        ]);
    }
}
