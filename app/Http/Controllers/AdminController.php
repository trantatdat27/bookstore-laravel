<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller 
{
    public function index(Request $request) {
    // Bắt đầu với một query cơ bản (lấy sách kèm danh mục, xếp mới nhất lên đầu)
    $query = Book::with('category')->latest();

    // 1. Lọc theo Tên sách hoặc Tác giả (dùng keyword)
    if ($request->has('keyword') && $request->keyword != '') {
        $keyword = $request->keyword;
        $query->where(function($q) use ($keyword) {
            $q->where('title', 'LIKE', '%' . $keyword . '%')
              ->orWhere('author', 'LIKE', '%' . $keyword . '%');
        });
    }

    // 2. Lọc theo Danh mục
    if ($request->has('category_id') && $request->category_id != '') {
        $query->where('category_id', $request->category_id);
    }

    // Lấy dữ liệu và phân trang (appends để giữ lại từ khóa trên URL khi bấm sang Trang 2, Trang 3)
    $books = $query->paginate(10)->appends($request->all());
    $categories = Category::all();

    return view('admin.admin', compact('books', 'categories'));
}

    public function store(Request $request) {
        // Cập nhật validate thêm trường stock (số lượng)
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0', // Số lượng phải là số nguyên và >= 0
            'category_id' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/books'), $imageName);
            $data['image'] = 'uploads/books/' . $imageName;
        }
        
        Book::create($data);
        return redirect()->back()->with('success', 'Thêm sách và cập nhật số lượng thành công!');
    }

    public function edit($id) {
        $book = Book::findOrFail($id);
        $categories = Category::all();
        return view('admin.edit', compact('book', 'categories'));
    }

    public function update(Request $request, $id) {
        $book = Book::findOrFail($id);
        
        // Cập nhật validate cho cả trường stock khi sửa
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required'
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            if ($book->image && File::exists(public_path($book->image))) {
                File::delete(public_path($book->image));
            }
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/books'), $imageName);
            $data['image'] = 'uploads/books/' . $imageName;
        }
        
        $book->update($data);
        return redirect()->route('admin.index')->with('success', 'Cập nhật thông tin và số lượng thành công!');
    }

    public function destroy($id) {
        $book = Book::findOrFail($id);
        if ($book->image && File::exists(public_path($book->image))) {
            File::delete(public_path($book->image));
        }
        $book->delete();
        return redirect()->back()->with('success', 'Đã xóa sách!');
    }

    // Xem danh sách đơn hàng
    public function orderIndex() {
        $orders = DB::table('orders')->orderBy('created_at', 'desc')->get();
        foreach($orders as $order) {
            $order->items = DB::table('order_items')->where('order_id', $order->id)->get();
        }
        return view('admin.orders', compact('orders'));
    }

    // Cập nhật trạng thái đơn hàng
    public function updateOrderStatus(Request $request, $id) {
        DB::table('orders')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);
        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}