<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\JsonResponse;

class LevelController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'levels' => Level::all()->map(fn ($level) => [
                'id'   => $level->id,
                'code' => $level->code,
                'name' => $level->description ?? $level->code,
            ]),
        ]);
    }
}
