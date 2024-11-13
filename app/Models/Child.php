<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'dob', 'img_url','status'];

    public function details()
    {
        return $this->hasMany(ChildDetail::class,'child_id');
    }

    public function sponsorDetailsCount()
    {
        return $this->details()->where('key', 'sponsors')->selectRaw('child_id, COUNT(*) as count')->groupBy('child_id');
    }

    public function otherDetails()
    {
        return $this->details()->where('key', '!=', 'sponsors');
    }

}
