<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_url', 
        'is_color',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}