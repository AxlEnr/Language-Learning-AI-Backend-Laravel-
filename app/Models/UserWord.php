<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'word_id',
        'familiarity',
        'next_review_at',
        'last_reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'familiarity' => 'integer',
            'next_review_at' => 'datetime',
            'last_reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class);
    }
}
