<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
}
