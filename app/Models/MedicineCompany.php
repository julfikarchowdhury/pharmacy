<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineCompany extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_en',
        'name_bn'
    ];
}
