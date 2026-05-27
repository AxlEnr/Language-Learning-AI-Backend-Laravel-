<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\UserSkill;
use App\Models\UserStats;
use App\Services\Adaptive\AdaptiveService;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected AdaptiveService $adaptiveService
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'native_language_id' => $request->native_language_id,
                'target_language_id' => $request->target_language_id,
                'level_id' => $request->level_id,
            ]);

            $this->initializeUserSkills($user);
            $this->initializeUserStats($user);

            DB::commit();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user->load(['nativeLanguage', 'targetLanguage', 'level']),
                'token' => $token,
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ]);
        }

    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load(['nativeLanguage', 'targetLanguage', 'level', 'stats']),
            'token' => $token,
        ]);
    }

    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function user(): JsonResponse
    {
        return response()->json([
            'user' => Auth::user()->load([
                'nativeLanguage',
                'targetLanguage',
                'level',
                'stats',
                'skills',
            ]),
        ]);
    }

    protected function initializeUserSkills(User $user): void
    {
        foreach (\App\Enums\SkillType::cases() as $skill) {
            UserSkill::create([
                'user_id' => $user->id,
                'skill' => $skill,
                'level' => 0,
                'last_updated' => now(),
            ]);
        }
    }

    protected function initializeUserStats(User $user): void
    {
        UserStats::firstOrCreate(
        ['user_id' => $user->id],
        [
            'xp' => 0,
            'streak_days' => 0,
            'last_activity_date' => now(),
        ]);
    }
}
