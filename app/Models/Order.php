<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'tracking_id',
        'order_type',
        'customer_id',
        'pharmacy_id',
        'total',
        'sub_total',
        'delivery_address',
        'delivery_lat',
        'delivery_long',
        'delivery_charge',
        "discount_by_points",
        "pharmacy_discount",
        'tax',
        'status',
        'date',
        'payment_type',
        'payment_status',
        'note',
        'prescription'
    ];
    /**
     * Relationship: An Order belongs to a Customer (User)
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relationship: An Order has many OrderDetails
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }


    /**
     * Relationship: An OrderDetail belongs to a Pharmacy
     */
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class, 'pharmacy_id');
    }
}
