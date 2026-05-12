<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // This tells Laravel it's okay to write to the 'name' column
    protected $fillable = ['name'];

    // Also, define the relationship to Designs
    public function designs()
    {
        return $this->belongsToMany(Design::class);
    }
}
