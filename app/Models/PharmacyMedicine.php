<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyMedicine extends Model
{
    use HasFactory;
    protected $fillable = [
        'pharmacy_id',
        'medicine_id',
        'discount_percentage',
    ];

}
