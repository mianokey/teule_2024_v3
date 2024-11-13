<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'date_from',
        'date_to',
        'title',
        'time_from',
        'time_to',
        'location',
        'url',
        // Add any other fields that are fillable.
    ];
}
