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
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}