<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

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
        'email',
        'password',
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
    ];

    // Quan hệ với Order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Quan hệ với Review
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Kiểm tra user đã mua sách này với đơn hàng đã hoàn thành
    public function hasPurchasedBook($bookId)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', $this->id)
            ->where('order_items.book_id', $bookId)
            ->where('orders.status', 'completed')
            ->exists();
    }

    // Kiểm tra user đã review sách này chưa
    public function hasReviewedBook($bookId)
    {
        return $this->reviews()->where('book_id', $bookId)->exists();
    }

    // Lấy review của user cho một sách
    public function getReviewForBook($bookId)
    {
        return $this->reviews()->where('book_id', $bookId)->first();
    }
}
