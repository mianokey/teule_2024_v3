<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildDetail extends Model
{
     protected $table = 'children_details';
     protected $fillable = ['child_id','key', 'value'];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
}
