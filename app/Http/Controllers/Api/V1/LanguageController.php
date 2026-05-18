<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    public function index(): JsonResponse
    {
        $languages = Language::withCount('modules')->get();

        return response()->json([
            'languages' => $languages->map(fn ($lang) => [
                'id' => $lang->id,
                'code' => $lang->code,
                'name' => $lang->name,
                'modules_count' => $lang->modules_count,
            ]),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $language = Language::with(['modules.level'])->findOrFail($id);

        return response()->json([
            'language' => [
                'id' => $language->id,
                'code' => $language->code,
                'name' => $language->name,
                'modules' => $language->modules->map(fn ($module) => [
                    'id' => $module->id,
                    'title' => $module->title,
                    'level' => $module->level->code,
                ]),
            ],
        ]);
    }
}
