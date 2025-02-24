<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tip extends Model
{
    use HasFactory;
    protected $fillable = [
        'title_en',
        'title_bn',
        'type',
        'instruction_en',
        'instruction_bn',
        'status',
        'image',
        'video'
    ];
}
