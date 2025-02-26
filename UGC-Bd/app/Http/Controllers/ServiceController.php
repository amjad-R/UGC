<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\platform;
use App\Models\UserProfile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if ($request->has('platforms') && is_string($request->input('platforms'))) {
                $request->merge(['platforms' => json_decode($request->input('platforms'), true)]);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category' => 'required|string',
                'hashtags' => 'nullable|string',
                'uploadedFiles' => 'required|file|mimetypes:video/mp4,video/mpeg',
                'platforms' => 'required|array',
                'platforms.*.name' => 'required|string|in:facebook,instagram,tiktok,youtube',
                'platforms.*.videoType' => 'required|string',
                'platforms.*.time' => 'required|string',
                'platforms.*.price' => 'nullable|numeric|min:0',
            ]);

            $filePath = $request->file('uploadedFiles')->store('uploadedFiles');
            $uploadedFiles = [asset('storage/' . $filePath)];

            $service = new Service([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'hashtags' => $validated['hashtags'],
                'uploaded_files' => json_encode($uploadedFiles),
            ]);

            $service->save();

            foreach ($validated['platforms'] as $platform) {
                $service->platforms()->create([
                    'platform' => $platform['name'],
                    'video_type' => $platform['videoType'],
                    'duration' => $platform['time'],
                    'price' => $platform['price'] ?? null,
                ]);
            }

            return response()->json([
                'message' => 'Service created successfully!',
                'service' => $service,
                'uploadedFiles' => json_decode($service->uploaded_files)
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }


    public function myServices()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $services = Service::with('user')
            ->where('user_id', auth()->id())
            ->get();
        foreach ($services as $service) {
            $uploadedFiles = json_decode($service->uploaded_files, true);
            if ($uploadedFiles) {
                $service->uploaded_files = array_map(function ($file) {
                    $file = str_replace(['\\'], '', $file);
                    return $file;
                }, $uploadedFiles);
            }
        }

        return response()->json(['services' => $services]);
    }

    public function deleteService($id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $service = Service::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$service) {
            return response()->json(['error' => 'Service not found or unauthorized'], 404);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully!'], 200);
    }

    public function updateService(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $service = Service::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$service) {
            return response()->json(['error' => 'Service not found or unauthorized'], 404);
        }

        if ($request->has('platforms') && is_string($request->input('platforms'))) {
            $request->merge(['platforms' => json_decode($request->input('platforms'), true)]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'hashtags' => 'nullable|string',
            'uploadedFiles' => 'nullable|file|mimetypes:video/mp4,video/mpeg',
            'platforms' => 'required|array',
            'platforms.*.name' => 'required|string|in:facebook,instagram,tiktok,youtube',
            'platforms.*.videoType' => 'required|string',
            'platforms.*.time' => 'required|string',
            'platforms.*.price' => 'nullable|numeric|min:0',
        ]);

        $service->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'hashtags' => $validated['hashtags'],
        ]);

        if ($request->hasFile('uploadedFiles')) {
            $filePath = $request->file('uploadedFiles')->store('uploadedFiles');
            $uploadedFiles = [asset('storage/' . $filePath)];
            $service->uploaded_files = json_encode($uploadedFiles);
            $service->save();
        }

        foreach ($validated['platforms'] as $platform) {
            $service->platforms()->updateOrCreate(
                [
                    'platform' => $platform['name'],
                    'video_type' => $platform['videoType'],
                ],
                [
                    'duration' => $platform['time'],
                    'price' => $platform['price'] ?? null,
                ]
            );
        }


        return response()->json(['message' => 'Service updated successfully!', 'service' => $service], 200);
    }


    public function show($id)
    {
        $service = Service::with(['user.profile', 'platforms', 'user.orders.service'])->findOrFail($id);

        if (!$service->user) {
            return response()->json(['error' => 'User not found for this service'], 404);
        }

        $response = [
            'id' => $service->id,
            'userId' => $service->user_id,
            'title' => $service->title,
            'description' => $service->description,
            'category' => $service->category,
            'hashtags' => $service->hashtags,
            'paymentType' => $service->payment_type,
            'uploaded_files' => json_decode($service->uploaded_files),
            'platforms' => $service->platforms,
            'profile' => $service->user->profile ? [
                'name' => $service->user->profile->name,
                'fullname' => $service->user->first_name . ' ' . $service->user->last_name,
                'description' => $service->user->profile->service_description,
                'email' => $service->user->email,
                'phone' => $service->user->phone,
                'role' => $service->user->profile->role,
                'hashtags-2' => $service->user->profile->hashtags
                    ? explode(' ', $service->user->profile->hashtags)
                    : [],
                'select_price' => $service->user->profile->select_price,
                'stats' => [
                    'services' => $service->user->services()->count(),
                    'followers' => $service->user->profile->global_followers,
                    'orders' => $service->user->orders->count(),
                ],
                'profile_image' => $service->user->profile->profile_image ?? 'default-profile-image.jpg',
                'is_online' => $service->user->profile->is_online,
                'last_seen' => $service->user->profile->last_seen,
                'social_links' => array_filter([
                    'instagram' => $service->user->profile->instagram,
                    'facebook' => $service->user->profile->facebook,
                    'tiktok' => $service->user->profile->tiktok,
                    'youtube' => $service->user->profile->youtube,
                ])
            ] : null,
            'my_orders' => $service->user->orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'service_name' => $order->service->title ?? 'Service not found',
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('Y-m-d H:i'),
                    'total' => $order->price ?? 0,
                ];
            }),
        ];

        return response()->json($response);
    }






    public function shows($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        return response()->json($service);
    }







    public function getAllServices()
    {
        try {
            $services = Service::with(['user.profile'])->get();
            foreach ($services as $service) {
                $uploadedFiles = json_decode($service->uploaded_files, true);
                if ($uploadedFiles) {
                    $service->uploaded_files = array_map(function ($file) {
                        return str_replace(['\\'], '', $file);
                    }, $uploadedFiles);
                }
            }
            $servicesData = $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'status' => $service->status,
                    'uploaded_files' => $service->uploaded_files,
                    'creator' => [
                        'id' => $service->user->id,
                        'first_name' => $service->user->first_name,
                        'last_name' => $service->user->last_name,
                        'profile_image' => $service->user->profile->profile_image ?? 'default-profile-image.jpg',
                    ],
                ];
            });

            return response()->json(['services' => $servicesData], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching services.', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:services,id',
            'status' => 'required'
        ]);

        $service = Service::with(['user.profile'])->find($validated['id']);

        if (!$service) {
            return response()->json([
                'error' => 'Service not found'
            ], 404);
        }

        $service->status = $validated['status'];
        $service->save();

        $profileImage = $service->user->profile->profile_image
            ? asset('storage/' . $service->user->profile->profile_image)
            : 'https://via.placeholder.com/150';

        $response = [
            'id' => $service->id,
            'status' => $service->status,
            'category' => $service->category,
            'user' => [
                'first_name' => $service->user->first_name ?? 'Unknown',
                'last_name' => $service->user->last_name ?? '',
                'profile_image' => $profileImage,
            ],
        ];

        return response()->json([
            'message' => 'Status updated successfully',
            'service' => $response
        ]);
    }



    public function getPublishedServices()
    {
        $services = Service::with(['user.profile'])->where('status', 'published')->get();

        foreach ($services as $service) {
            $uploadedFiles = json_decode($service->uploaded_files, true);
            if ($uploadedFiles) {
                $service->uploaded_files = array_map(function ($file) {
                    return str_replace(['\\'], '', $file);
                }, $uploadedFiles);
            }
        }

        $response = $services->map(function ($service) {
            return [
                'id' => $service->id,
                'category' => $service->category,
                'uploaded_files' => $service->uploaded_files,
                'user' => [
                    'first_name' => $service->user->first_name ?? 'Unknown',
                    'last_name' => $service->user->last_name ?? '',
                    'profile_image' => $service->user->profile->profile_image
                        ? asset('storage/' . $service->user->profile->profile_image)
                        : 'default-profile.png',
                ],
            ];
        });

        return response()->json($response, 200);
    }


    public function getPlatformsByServiceId($service_id)
    {
        $platforms = DB::table('platforms')
            ->select('platform', 'video_type', 'duration', 'price')
            ->where('service_id', $service_id)
            ->distinct()
            ->get();

        return response()->json($platforms);
    }

    public function getAllServicesWithProfiles()
    {
        try {
            $services = Service::with(['user.profile'])->get();

            $servicesData = $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'status' => $service->status,
                    'uploaded_files' => json_decode($service->uploaded_files, true) ?? [],
                    'creator' => [
                        'id' => $service->user->id,
                        'full_name' => $service->user->first_name . ' ' . $service->user->last_name,
                        'profile_image' => $service->user->profile->profile_image ?? 'default-profile-image.jpg',
                        'description' => $service->user->profile->description ?? 'No description available.',
                    ],
                ];
            });

            return response()->json(['services' => $servicesData], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching services.', 'message' => $e->getMessage()], 500);
        }
    }

    public function getUserPublishedServices($userId)
    {
        $services = Service::with(['user.profile'])
            ->where('status', 'published')
            ->where('user_id', $userId)
            ->get();

        if ($services->isEmpty()) {
            return response()->json(['message' => 'No published services found for this user'], 404);
        }

        foreach ($services as $service) {
            $uploadedFiles = json_decode($service->uploaded_files, true);
            if ($uploadedFiles) {
                $service->uploaded_files = array_map(function ($file) {
                    return str_replace(['\\'], '', $file);
                }, $uploadedFiles);
            }
        }

        $response = $services->map(function ($service) {
            return [
                'id' => $service->id,
                'title' => $service->title,
                'category' => $service->category,
                'uploaded_files' => $service->uploaded_files,
                'user' => [
                    'first_name' => $service->user->first_name ?? 'Unknown',
                    'last_name' => $service->user->last_name ?? '',
                    'profile_image' => $service->user->profile->profile_image
                        ? asset('storage/' . $service->user->profile->profile_image)
                        : 'default-profile.png',
                    'user_id' => $service->user->id,
                ],
            ];
        });

        return response()->json($response, 200);
    }







    public function getUserServices($serviceId)
    {
        $service = Service::with(['user.profile'])->where('id', $serviceId)->where('status', 'published')->first();

        if (!$service) {
            return response()->json(['message' => 'لا توجد خدمة بهذا الـ ID أو الخدمة غير منشورة'], 404);
        }

        $uploadedFiles = json_decode($service->uploaded_files, true);
        if ($uploadedFiles) {
            $service->uploaded_files = array_map(function ($file) {
                return str_replace(['\\'], '', $file);
            }, $uploadedFiles);
        }

        $response = [
            'id' => $service->id,
            'category' => $service->category,
            'uploaded_files' => $service->uploaded_files,
            'user' => [
                'first_name' => $service->user->first_name ?? 'Unknown',
                'last_name' => $service->user->last_name ?? '',
                'profile_image' => $service->user->profile->profile_image
                    ? asset('storage/' . $service->user->profile->profile_image)
                    : 'default-profile.png',
            ],
        ];

        return response()->json($response, 200);
    }
}
