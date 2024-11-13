<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sustainability_report extends Model
{
    use HasFactory;

    protected $fillable = ['title','description','report_url'];

    
    
    public function sustainability()
    {
        return $this->belongsTo(Sustainability_project::class, 'sustainability_id');
    }
}
