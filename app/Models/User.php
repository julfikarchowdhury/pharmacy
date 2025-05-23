<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'lat',
        'long',
        'status',
        'password',
        'points',
        'image',
        'role',
        'email_verified_at',
        'device_token',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function pharmacy()
    {
        return $this->hasOne(Pharmacy::class, 'user_id');  // Ensure the foreign key is specified if it's different from the default `user_id`.
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'user_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
