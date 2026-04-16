<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Book extends Model {
    protected $fillable = ['title', 'author', 'price', 'description', 'image', 'category_id','stock', 'sold'];
    
    public function category() { 
        return $this->belongsTo(Category::class); 
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }
}