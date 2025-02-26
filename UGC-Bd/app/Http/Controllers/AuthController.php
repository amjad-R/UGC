<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:15',
                'password' => 'required|string|min:8|confirmed',
                'user_type' => 'required|in:business,creator',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ]);

            return response()->json(['message' => 'User registered successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to register user: ' . $e->getMessage()], 500);
        }
    }



    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                if ($user->profile) {
                    $user->profile->is_online = true;
                    $user->profile->last_seen = now();
                    $user->profile->save();
                }

                $tokenResult = $user->createToken('YourAppName', ['*']);

                if ($request->wantsJson()) {
                    return response()->json($tokenResult->plainTextToken, 200);
                } else {
                    return redirect()->route('profile.show');
                }
            }

            return response()->json(['message' => 'Invalid credentials!'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Login failed: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user) {
                if ($user->profile) {
                    $user->profile->is_online = false;
                    $user->profile->last_seen = now();
                    $user->profile->save();
                }

                $user->tokens()->delete();

                return response()->json(['message' => 'Successfully logged out'], 200);
            }

            return response()->json(['message' => 'User not authenticated'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Logout failed: ' . $e->getMessage()], 500);
        }
    }

    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $response = Password::sendResetLink($request->only('email'));

            return $response == Password::RESET_LINK_SENT
                ? response()->json(['message' => 'Reset link sent!'], 200)
                : response()->json(['message' => 'Failed to send reset link!'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error sending reset link: ' . $e->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
                'token' => 'required',
            ]);

            $response = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                    event(new PasswordReset($user));
                }
            );

            return $response == Password::PASSWORD_RESET
                ? response()->json(['message' => 'Password reset successfully!'], 200)
                : response()->json(['message' => 'Failed to reset password!'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error resetting password: ' . $e->getMessage()], 500);
        }
    }
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
            $user = $this->findOrCreateUser($facebookUser, 'facebook');
            Auth::login($user);
            return response()->json(['message' => 'Login successful!', 'user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Login failed: ' . $e->getMessage()], 500);
        }
    }

    private function findOrCreateUser($socialUser, $provider)
    {
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => bcrypt(uniqid()),
            ]);
        }

        return $user;
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = $this->findOrCreateUser($googleUser, 'google');
            Auth::login($user);
            return response()->json(['message' => 'Login successful!', 'user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Login failed: ' . $e->getMessage()], 500);
        }
    }

    public function redirectToInstagram()
    {
        return Socialite::driver('instagram')->redirect();
    }

    public function handleInstagramCallback()
    {
        try {
            $instagramUser = Socialite::driver('instagram')->stateless()->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'فشل تسجيل الدخول عبر Instagram: ' . $e->getMessage());
        }
    }

    public function showProfileForm()
    {
        return view('profile', [
            'user' => auth()->user()
        ]);
    }

    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:15',
                'instagram' => 'nullable|string|max:255',
                'facebook' => 'nullable|string|max:255',
                'tiktok' => 'nullable|string|max:255',
                'youtube' => 'nullable|string|max:255',
                'service_description' => 'nullable|string|max:1000',
                'hashtags' => 'nullable|string|max:255',
                'identity_verification' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $user = auth()->user();
            $user->update($request->only(['first_name', 'last_name', 'email', 'phone']));

            $profileData = $request->only([
                'instagram',
                'facebook',
                'tiktok',
                'youtube',
                'service_description',
                'hashtags'
            ]);

            if ($request->hasFile('identity_verification')) {
                $path = $request->file('identity_verification')->store('identity_verifications');
                $profileData['identity_verification'] = $path;
            }

            $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

            return response()->json(['message' => 'Profile updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating profile: ' . $e->getMessage()], 500);
        }
    }
}
