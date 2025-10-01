<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Two\AbstractProvider;

class AuthController extends Controller
{
    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'message' => 'User retrieved successfully'
        ]);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
{
    try {
        Log::info('=== Google Callback Started ===');
        
        // Kiểm tra có code từ Google không
        if (!request()->has('code')) {
            Log::error('No authorization code received from Google');
            throw new \Exception('No authorization code received');
        }
        
        Log::info('Authorization code received, getting user data...');
        
        // Sử dụng stateless để tránh lỗi state với SPA/front-end khác domain
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver('google');
        $googleUser = $driver->stateless()->user();
        Log::info('Google user data received:', [
            'id' => $googleUser->getId(),
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail()
        ]);
        
        // Tạo mới nếu chưa có, còn nếu đã có thì chỉ cập nhật google_id và avatar
        $user = User::where('email', $googleUser->getEmail())->first();
        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(uniqid()),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            $user->google_id = $googleUser->getId();
            $user->avatar = $googleUser->getAvatar();
            $user->save();
        }
        
        Log::info('User saved to database:', ['user_id' => $user->id]);
        
        $token = $user->createToken('auth_token')->plainTextToken;
        Log::info('Token created successfully');
        
        $response = [
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
                'provider' => 'google'
            ],
            'message' => 'Google authentication successful'
        ];
        
        return redirect('http://localhost:5173/auth/callback?response=' . urlencode(json_encode($response)));
        
    } catch (\Exception $e) {
        Log::error('Google authentication failed:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        $error = [
            'success' => false,
            'message' => 'Authentication failed: ' . $e->getMessage()
        ];
        
        return redirect('http://localhost:5173/auth/callback?response=' . urlencode(json_encode($error)));
    }
}


}
