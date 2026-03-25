<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Book extends Model {
    protected $fillable = ['title', 'author', 'price', 'description', 'image', 'category_id','stock'];
    public function category() { return $this->belongsTo(Category::class); }
}