<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'parent_id', 'restaurant_id', 'name', 'slug', 'image'
    ];

    public function subCategory(){
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function parentCategory(){
        return $this->hasMany(self::class, 'id', 'parent_id');
    }

    public function products(){
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
