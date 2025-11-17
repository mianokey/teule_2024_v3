<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name','description','base_price','allow_preorder','allow_design_upload'];

    public function variants() {
        return $this->hasMany(ProductVariant::class);
    }
}

