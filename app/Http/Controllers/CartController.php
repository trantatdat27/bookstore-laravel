<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Xem giỏ hàng
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('client.cart', compact('cart'));
    }

    // Thêm sản phẩm
    public function add($id)
    {
        $book = Book::findOrFail($id);
        
        // Kiểm tra xem sách còn hàng không trước khi thêm
        if ($book->stock <= 0) {
            return redirect()->back()->with('error', 'Sách này hiện đã hết hàng!');
        }

        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "title" => $book->title,
                "quantity" => 1,
                "price" => $book->price,
                "author" => $book->author
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    // Xóa sản phẩm
    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Đã xóa sản phẩm!');
    }

    // Xóa sạch giỏ
    public function clear()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Giỏ hàng đã trống!');
    }

    // Hiển thị trang nhập thông tin (Checkout)
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('client.home')->with('error', 'Giỏ hàng trống!');
        }
        return view('client.checkout', compact('cart'));
    }

    // Lưu đơn hàng và TRỪ KHO
    public function placeOrder(Request $request)
    {
        $request->validate([
            'fullname' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        $cart = session()->get('cart');
        if (!$cart) return redirect()->route('client.home');

        DB::transaction(function () use ($cart, $request) {
            $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

            // 1. Lưu đơn hàng chính
            $orderId = DB::table('orders')->insertGetId([
                'customer_name' => $request->fullname,
                'phone' => $request->phone,
                'address' => $request->address,
                'payment_method' => $request->payment_method ?? 'COD',
                'total_amount' => $total,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. Lưu chi tiết và TRỪ SỐ LƯỢNG KHO
            foreach ($cart as $id => $details) {
                // Lưu vào bảng order_items
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'book_title' => $details['title'],
                    'quantity' => $details['quantity'],
                    'price' => $details['price'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // PHẦN QUAN TRỌNG: Trừ số lượng sách trong bảng books
                $book = Book::find($id);
                if ($book) {
                    $book->decrement('stock', $details['quantity']);
                }
            }
        });

        session()->forget('cart');
        return redirect()->route('client.home')->with('success', 'Đặt hàng thành công! Số lượng kho đã được cập nhật.');
    }

    // Tra cứu đơn hàng
    public function trackOrder(Request $request) {
    $orders = collect(); // Tạo một bộ sưu tập trống mặc định
    
    if ($request->filled('phone')) {
        // Đổi ->first() thành ->get() để lấy DANH SÁCH đơn hàng
        $orders = DB::table('orders')
            ->where('phone', $request->phone)
            ->orderBy('created_at', 'desc')
            ->get();

        // Lấy chi tiết sản phẩm cho từng đơn hàng trong danh sách
        foreach ($orders as $order) {
            $order->items = DB::table('order_items')->where('order_id', $order->id)->get();
        }
    }
    
    // Đổi biến truyền sang view từ 'order' thành 'orders'
    return view('client.track_order', compact('orders'));
}
}