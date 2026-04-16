<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'phone',
        'address',
        'payment_method',
        'total_amount',
        'status'
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với Order Items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Quan hệ với Reviews
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
