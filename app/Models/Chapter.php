<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends Model
{
    protected $fillable = [
        'subject_id',
        'name',
        'description',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Một chương thuộc về một môn học
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}