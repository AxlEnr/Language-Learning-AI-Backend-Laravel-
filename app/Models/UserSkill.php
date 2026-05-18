<?php

namespace App\Models;

use App\Enums\SkillType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'skill',
        'level',
        'last_updated',
    ];

    protected function casts(): array
    {
        return [
            'skill' => SkillType::class,
            'level' => 'integer',
            'last_updated' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
