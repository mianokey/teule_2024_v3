<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
     use HasFactory;

    // Table name (optional if it follows Laravel convention)
    protected $table = 'posts';

    // Fillable fields for mass assignment
    protected $fillable = [
        'title',
        'slug',
        'image',
        'category',
        'author',
        'excerpt',
        'content',
    ];

    // Optional: automatically generate slug from title (if you want)
    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = \Str::slug($post->title);
            }
        });
    }
}
