<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Latest extends Model
{
    use HasFactory;
    protected $table = 'latest';
    protected $fillable = ['title','description', 'image_links'];

    protected $casts = [
        'image_links' => 'json',
    ];
}
