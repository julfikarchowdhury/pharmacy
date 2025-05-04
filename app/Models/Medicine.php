<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'category_id',
        'concentration_id',
        'medicine_company_id',
        'medicine_generic_id',
        'name_en',
        'name_bn',
        'description_en',
        'description_bn',
        'status',
        'unit_price',
        'strip_price',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function company()
    {
        return $this->belongsTo(MedicineCompany::class, 'medicine_company_id');
    }

    public function generic()
    {
        return $this->belongsTo(MedicineGeneric::class, 'medicine_generic_id');
    }

    public function concentration()
    {
        return $this->belongsTo(Concentration::class, 'concentration_id');
    }
    // One-to-many relationship for images
    public function images()
    {
        return $this->hasMany(MedicineImage::class);
    }

    // Many-to-many relationship for units// Many-to-many relationship for units
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'medicine_units');
    }



    public function pharmacies()
    {
        return $this->belongsToMany(Pharmacy::class, 'pharmacy_medicines')
            ->withPivot('discount_percentage', 'status')
            ->wherePivot('status', 'active')
            ->withTimestamps();
    }


    // Function to get the first image
    public function medicineThumb()
    {
        return $this->images()->limit(1);
    }


}
