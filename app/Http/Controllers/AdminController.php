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
        $query = Book::with('category')->latest();
        if ($request->filled('keyword')) {
            $query->where('title', 'LIKE', '%' . $request->keyword . '%')
                  ->orWhere('author', 'LIKE', '%' . $request->keyword . '%');
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $books = $query->paginate(10)->appends($request->all());
        $categories = Category::all();
        return view('admin.admin', compact('books', 'categories'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[@#$%^&*()+\=\[\]{};:"\'<>,?\/\\\\|`~!]/u', trim($value))) {
                        $fail('Tên sách không được chứa ký tự đặc biệt như: @, #, $, %, &, *, /, v.v.');
                    }
                    if (empty(trim($value))) {
                        $fail('Tên sách không được để trống.');
                    }
                }
            ],
            'author' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[@#$%^&*()+\=\[\]{};:"\'<>,?\/\\\\|`~!]/u', trim($value))) {
                        $fail('Tên tác giả không được chứa ký tự đặc biệt như: @, #, $, %, &, *, /, v.v.');
                    }
                    if (empty(trim($value))) {
                        $fail('Tên tác giả không được để trống.');
                    }
                }
            ],
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0', 
            'category_id' => 'required'
        ]);
        $data = $request->all();
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/books'), $imageName);
            $data['image'] = 'uploads/books/' . $imageName;
        }
        Book::create($data);
        return redirect()->back()->with('success', 'Thêm sách thành công!');
    }

    public function edit($id) {
        $book = Book::findOrFail($id);
        $categories = Category::all();
        return view('admin.edit', compact('book', 'categories'));
    }

    public function update(Request $request, $id) {
        // Validate input
        $request->validate([
            'title' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[@#$%^&*()+\=\[\]{};:"\'<>,?\/\\\\|`~!]/u', trim($value))) {
                        $fail('Tên sách không được chứa ký tự đặc biệt như: @, #, $, %, &, *, /, v.v.');
                    }
                    if (empty(trim($value))) {
                        $fail('Tên sách không được để trống.');
                    }
                }
            ],
            'author' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[@#$%^&*()+\=\[\]{};:"\'<>,?\/\\\\|`~!]/u', trim($value))) {
                        $fail('Tên tác giả không được chứa ký tự đặc biệt như: @, #, $, %, &, *, /, v.v.');
                    }
                    if (empty(trim($value))) {
                        $fail('Tên tác giả không được để trống.');
                    }
                }
            ],
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required'
        ]);
        
        $book = Book::findOrFail($id);
        $data = $request->all();
        if ($request->hasFile('image')) {
            if ($book->image && File::exists(public_path($book->image))) File::delete(public_path($book->image));
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/books'), $imageName);
            $data['image'] = 'uploads/books/' . $imageName;
        }
        $book->update($data);
        return redirect()->route('admin.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id) {
        $book = Book::findOrFail($id);
        if ($book->image && File::exists(public_path($book->image))) File::delete(public_path($book->image));
        $book->delete();
        return redirect()->back()->with('success', 'Đã xóa sách!');
    }

    // --- DANH MỤC ---
    public function categoryIndex() {
        $categories = Category::all();
        return view('admin.category_index', compact('categories'));
    }

    public function categoryStore(Request $request) {
        $request->validate(['name' => 'required|unique:categories,name']);
        Category::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Thêm danh mục thành công!');
    }

    public function categoryDestroy($id) {
        $category = Category::findOrFail($id);
        $category->delete();
        return redirect()->back()->with('success', 'Đã xóa danh mục!');
    }

    // --- ĐƠN HÀNG ---
    public function orderIndex() {
        $orders = DB::table('orders')->orderBy('created_at', 'desc')->get();
        foreach($orders as $order) {
            $order->items = DB::table('order_items')->where('order_id', $order->id)->get();
        }
        return view('admin.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, $id) {
        $order = DB::table('orders')->where('id', $id)->first();
        $newStatus = $request->status;

        // Chặn không cho đổi trạng thái nếu đơn đã bị hủy trước đó
        if ($order->status === 'canceled') {
            return redirect()->back()->with('error', 'Đơn hàng đã hủy, không thể thay đổi trạng thái khác!');
        }

        // Nếu admin đổi sang Hủy -> Hoàn kho + Trừ lượt bán
        if ($newStatus === 'canceled') {
            DB::transaction(function () use ($id) {
                $items = DB::table('order_items')->where('order_id', $id)->get();
                foreach ($items as $item) {
                    DB::table('books')->where('id', $item->book_id)->increment('stock', $item->quantity);
                    DB::table('books')->where('id', $item->book_id)->decrement('sold', $item->quantity);
                }
            });
        }

        DB::table('orders')->where('id', $id)->update(['status' => $newStatus, 'updated_at' => now()]);
        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }
    public function orderDestroy($id) {
        $order = DB::table('orders')->where('id', $id)->first();

        DB::transaction(function () use ($id, $order) {
            // Nếu đơn hàng CHƯA HỦY mà bị Admin XÓA -> Phải hoàn kho trước khi xóa
            if ($order && $order->status !== 'canceled') {
                $items = DB::table('order_items')->where('order_id', $id)->get();
                foreach ($items as $item) {
                    DB::table('books')->where('id', $item->book_id)->increment('stock', $item->quantity);
                    DB::table('books')->where('id', $item->book_id)->decrement('sold', $item->quantity);
                }
            }

            DB::table('order_items')->where('order_id', $id)->delete();
            DB::table('orders')->where('id', $id)->delete();
        });

        return redirect()->back()->with('success', 'Đã xóa đơn hàng!');
    }
}