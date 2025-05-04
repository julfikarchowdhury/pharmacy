<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'medicine_id',
        'unit_id',
        'qty',
        'price',
        'discounted_price',
        'status',
    ];
    /**
     * Relationship: An OrderDetail belongs to an Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Relationship: An OrderDetail belongs to a Medicine
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }


    /**
     * Relationship: An OrderDetail belongs to a Unit (if needed for measurement)
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
