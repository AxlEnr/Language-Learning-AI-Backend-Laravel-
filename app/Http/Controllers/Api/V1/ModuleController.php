<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Module\ListModulesRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    public function index(ListModulesRequest $request): JsonResponse
    {
        $query = Module::with(['language', 'level', 'lessons'])->orderBy('order_index');

        if ($request->filled('language_id')) {
            $query->where('language_id', $request->language_id);
        }

        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        $modules = $query->get();

        return response()->json([
            'modules' => ModuleResource::collection($modules),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $module = Module::with(['language', 'level', 'lessons.exercises'])->findOrFail($id);

        return response()->json([
            'module' => new ModuleResource($module),
        ]);
    }
}
