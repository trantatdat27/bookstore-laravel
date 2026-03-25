<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Book::query();

        // 1. Lọc theo từ khóa tìm kiếm (Tên sách hoặc Tác giả)
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
        }

        // 2. Lọc theo danh mục
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $books = $query->with('category')->get();

        return view('client.index', compact('books', 'categories'));
    }

    // Xem chi tiết 1 cuốn sách
    public function show($id)
    {
        $book = Book::with('category')->findOrFail($id);
        return view('client.show', compact('book'));
    }
}