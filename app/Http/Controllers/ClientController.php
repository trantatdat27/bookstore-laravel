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
        $query = Book::with('category'); 

        // 1. NẾU NGƯỜI DÙNG CÓ TÌM KIẾM HOẶC LỌC DANH MỤC
        if ($request->filled('keyword') || $request->filled('category_id')) {
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                      ->orWhere('author', 'like', '%' . $keyword . '%');
                });
            }
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Chỉ trả về kết quả lọc (không hiện mục bán chạy nữa cho đỡ rối)
            $books = $query->latest()->paginate(10);
            return view('client.index', compact('books', 'categories'));
        }

        // 2. NẾU LÀ TRANG CHỦ MẶC ĐỊNH (Không lọc)
        // Lấy Top 10 cuốn bán chạy nhất (sắp xếp theo cột sold)
        $bestsellers = Book::with('category')->orderBy('sold', 'desc')->take(10)->get();

        // Lấy sách mới nhất cho phần còn lại (phân trang)
        $books = Book::with('category')->latest()->paginate(10);

        return view('client.index', compact('books', 'bestsellers', 'categories'));
    }

    public function show($id)
    {
        $book = Book::with('category')->findOrFail($id);
        return view('client.show', compact('book'));
    }
}