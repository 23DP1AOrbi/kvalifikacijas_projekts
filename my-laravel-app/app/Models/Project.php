<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'design_id',
        'name',
        'color_data'
    ];

    protected $casts = [
        'color_data' => 'array',
    ];
}
