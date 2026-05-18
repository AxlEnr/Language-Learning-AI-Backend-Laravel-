<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'order_index' => $this->order_index,
            'language' => [
                'id' => $this->language->id,
                'name' => $this->language->name,
                'code' => $this->language->code,
            ],
            'level' => [
                'id' => $this->level->id,
                'code' => $this->level->code,
                'description' => $this->level->description,
            ],
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
