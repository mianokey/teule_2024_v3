<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sustainability_project extends Model
{
    use HasFactory;
    protected $table = 'sustainability_project';

    protected $fillable = ['title','brief', 'description', 'image_links'];

    protected $casts = [
        'image_links' => 'json',
    ];

    public function reports()
    {
        return $this->hasMany(Sustainability_report::class, 'sustainability_id');
    }
}
