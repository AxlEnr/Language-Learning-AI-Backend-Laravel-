<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\UserSkill;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update($request->only([
            'name',
            'native_language_id',
            'target_language_id',
            'level_id',
        ]));

        return response()->json([
            'message' => 'Profile updated',
            'user' => $user->load(['nativeLanguage', 'targetLanguage', 'level', 'stats']),
        ]);
    }

    public function skills(): JsonResponse
    {
        $skills = UserSkill::where('user_id', request()->user()->id)
            ->orderBy('level', 'desc')
            ->get();

        return response()->json([
            'skills' => $skills->map(fn ($skill) => [
                'skill' => $skill->skill->value,
                'level' => $skill->level,
                'last_updated' => $skill->last_updated,
            ]),
        ]);
    }

    public function stats(): JsonResponse
    {
        $user = request()->user();
        $stats = $user->ensureStatsExist();

        return response()->json([
            'stats' => [
                'xp' => $stats->xp,
                'streak_days' => $stats->streak_days,
                'last_activity_date' => $stats->last_activity_date,
            ],
        ]);
    }
}
