<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'order_id',
        'rating',
        'comment',
        'status'
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với Book
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Quan hệ với Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
