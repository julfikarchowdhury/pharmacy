<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    use HasFactory;
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'pharmacy_medicines')
            ->withPivot('discount_percentage')
            ->withTimestamps();
    }
}
