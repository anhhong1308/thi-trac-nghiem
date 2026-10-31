<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'subject_id',
        'chapter_id',
        'bloom_level_id',
        'created_by',
        'content',
        'type',
        'explanation',
        'status',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function bloomLevel(): BelongsTo
    {
        return $this->belongsTo(BloomLevel::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)
            ->orderBy('position')
            ->orderBy('id');
    }
}