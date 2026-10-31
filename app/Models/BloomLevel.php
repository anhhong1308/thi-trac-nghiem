<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloomLevel extends Model
{
    protected $fillable = [
        'name',
        'code',
        'level',
        'description',
    ];
}