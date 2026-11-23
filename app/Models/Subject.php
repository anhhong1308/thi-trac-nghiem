<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Một môn học có nhiều chương
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    // Một môn học có thể thuộc nhiều chuyên ngành
    public function majors(): BelongsToMany
    {
        return $this->belongsToMany(Major::class);
    }
}
