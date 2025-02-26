<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProfile;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;



class UserProfileController extends Controller
{

    public function show()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $profile = $user->profile;

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }



        return response()->json($profile);
    }



    public function update(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'instagram' => 'nullable|url',
            'facebook' => 'nullable|url',
            'tiktok' => 'nullable|url',
            'youtube' => 'nullable|url',
            'service_description' => 'nullable|string',
            'hashtags' => 'nullable|string',
            'identity_verification' => 'nullable|file|mimes:pdf|max:2048',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'global_followers' => 'nullable|integer|min:0',
            'category' => 'nullable|string|max:255',
            'select_price' => 'nullable|string',
            'video_type' => 'nullable|string',
            'role' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('identity_verification')) {
            $file = $request->file('identity_verification')->store('identity_verifications');
            $data['identity_verification'] = $file;
        }

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image')->store('profile_images');
            $data['profile_image'] = $file;
        }

        $profile = UserProfile::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile,
        ]);
    }
    public function getAllUniqueProfiles()
    {
        try {
            $users = User::with('profile')
                ->where('user_type', 'creator')
                ->has('profile')
                ->get();

            $profilesData = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'profile_image' => $user->profile->profile_image ?? 'default-profile-image.jpg',
                    'description' => $user->profile->service_description ?? 'No description available.',
                    'instagram' => $user->profile->instagram ?? null,
                    'facebook' => $user->profile->facebook ?? null,
                    'tiktok' => $user->profile->tiktok ?? null,
                    'youtube' => $user->profile->youtube ?? null,
                    'followers' => $user->profile->global_followers ?? 0,
                    'category' => $user->profile->category ?? 'General',
                    'price_range' => $user->profile->select_price ?? 'N/A',
                    'video_type' => $user->profile->video_type ?? 'N/A',
                    'role' => $user->profile->role ?? null,
                ];
            });

            return response()->json(['profiles' => $profilesData], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching profiles.', 'message' => $e->getMessage()], 500);
        }
    }
    public function getBusinessUsers()
    {
        try {
            $users = User::with('profile')
                ->where('user_type', 'business')
                ->has('profile')
                ->get();

            $usersData = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'phone' => $user->phone,
                    'profile_image' => $user->profile->profile_image ?? 'default-profile-image.jpg',
                    'join_date' => $user->created_at->format('Y-m-d'),
                    'services' => $user->profile->service_description ?? 'No services available.',
                ];
            });

            return response()->json(['users' => $usersData], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching users.', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->profile) {
                $user->profile()->delete();
            }

            $user->delete();

            return response()->json(['message' => 'User deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting user.', 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleUserStatus($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->is_active = !$user->is_active;
            $user->save();

            if ($user->is_active) {
                $services = Service::where('user_id', $user->id)
                    ->where('status', '!=', 'pending')
                    ->get();

                foreach ($services as $service) {
                    $service->status = 'pending';
                    $service->save();
                }
            } else {
                $services = Service::where('user_id', $user->id)->get();
                foreach ($services as $service) {
                    if ($service->status == 'pending') {
                        $service->status = 'published';
                        $service->save();
                    }
                }
            }

            return response()->json([
                'message' => 'User status updated successfully.',
                'new_status' => $user->is_active
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating user status.', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCreators()
    {
        try {
            $users = User::with('profile')
                ->where('user_type', 'creator')
                ->has('profile')
                ->get();

            $usersData = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->first_name . ' ' . $user->last_name,
                    'phone' => $user->phone ?? 'No phone number',
                    'profile_image' => optional($user->profile)->profile_image
                        ? url('storage/' . $user->profile->profile_image)
                        : url('storage/default-profile-image.jpg'),
                    'join_date' => $user->created_at->format('Y-m-d'),
                    'services' => optional($user->profile)->service_description ?? 'No services available.',
                    'is_active' => $user->is_active,
                ];
            });

            return response()->json(['users' => $usersData], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching creators.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeUserType($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->user_type = 'support';
            $user->save();

            return response()->json(['message' => 'User type changed to support.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error changing user type.', 'message' => $e->getMessage()], 500);
        }
    }

    public function getUserDetailsWithServices($id)
    {
        try {
            $user = User::with(['profile', 'services'])->findOrFail($id);

            $data = [
                'id' => $user->id,
                'full_name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone ?? 'No phone number',
                'profile_image' => optional($user->profile)->profile_image
                    ? url('storage/' . $user->profile->profile_image)
                    : url('storage/default-profile-image.jpg'),
                'instagram' => $user->profile->instagram ?? null,
                'facebook' => $user->profile->facebook ?? null,
                'tiktok' => $user->profile->tiktok ?? null,
                'youtube' => $user->profile->youtube ?? null,
                'service_description' => $user->profile->service_description ?? 'No description available.',
                'hashtags' => $user->profile->hashtags ?? null,
                'global_followers' => $user->profile->global_followers ?? 0,
                'category' => $user->profile->category ?? 'General',
                'role' => $user->profile->role ?? 'User',
                'join_date' => $user->created_at->format('Y-m-d'),
                'is_active' => $user->is_active,
                'services' => $user->services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'title' => $service->title,
                        'description' => $service->description,
                        'platforms' => $service->platforms,
                        'price' => $service->price,
                        'negotiable' => $service->negotiable,
                        'status' => $service->status,
                    ];
                }),
            ];

            return response()->json(['user_details' => $data], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching user details.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function getProfileWithDetails($id)
    {
        try {
            $service = Service::with('profile')->findOrFail($id);

            $service->my_orders = $service->orders;

            return response()->json([
                'success' => true,
                'data' => $service
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found or an error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function getProfileByUserId($userId): JsonResponse
    {
        try {
            $user = User::with('profile', 'services', 'orders')->findOrFail($userId);

            if (!$user->profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile not found for this user.',
                ], 404);
            }

            $profile = $user->profile;
            return response()->json([
                'success' => true,
                'profile' => [
                    'fullname' => trim($user->first_name . ' ' . $user->last_name),
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $profile->role,
                    'service_description' => $profile->service_description,
                    'profile_image' => $profile->profile_image ?? 'default-profile-image.jpg',
                    'hashtags-2' => $profile->hashtags ? explode(' ', $profile->hashtags) : [],
                    'select_price' => $profile->select_price,
                    'stats' => [
                        'services' => $user->services()->count(),
                        'followers' => $profile->global_followers ?? 0,
                        'orders' => $user->orders->count(),
                    ],
                    'is_online' => $profile->is_online,
                    'last_seen' => $profile->last_seen,
                    'social_links' => array_filter([
                        'instagram' => $profile->instagram,
                        'facebook' => $profile->facebook,
                        'tiktok' => $profile->tiktok,
                        'youtube' => $profile->youtube,
                    ]),
                    'services' => $user->services->map(function ($service) {
                        $uploadedFiles = json_decode($service->uploaded_files, true);
                        if ($uploadedFiles) {
                            $service->uploaded_files = array_map(function ($file) {
                                return str_replace(['\\'], '', $file);
                            }, $uploadedFiles);
                        }

                        return [
                            'id' => $service->id,
                            'category' => $service->category,
                            'uploaded_files' => $service->uploaded_files,
                            'user' => [
                                'first_name' => $service->user->first_name ?? 'Unknown',
                                'last_name' => $service->user->last_name ?? '',
                                'profile_image' => $service->user->profile->profile_image
                                    ? asset('storage/' . $service->user->profile->profile_image)
                                    : asset('storage/default-profile.png'),
                            ],
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
