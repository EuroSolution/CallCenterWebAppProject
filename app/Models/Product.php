<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable= [
        'restaurant_id', 'category_id', 'name', 'description', 'price', 'discounted_price',
        'type', 'status', 'image', 'slug'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function productSizes(){
        return $this->hasMany(ProductSize::class, 'product_id', 'id');
    }
}
