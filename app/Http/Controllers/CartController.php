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
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('client.cart', compact('cart', 'total'));
    }

    // Thêm sản phẩm
    public function add($id)
    {
        $book = Book::findOrFail($id);
        
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
                "author" => $book->author,
                "image" => $book->image
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
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('client.checkout', compact('cart', 'total'));
    }

    // Lưu đơn hàng, TRỪ KHO và TĂNG LƯỢT BÁN
    public function placeOrder(Request $request)
    {
        $request->validate([
            'fullname' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ], [
            'fullname.required' => 'Vui lòng nhập họ tên',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'address.required' => 'Vui lòng nhập địa chỉ',
        ]);

        $cart = session()->get('cart');
        if (!$cart) return redirect()->route('client.home');

        try {
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

                // 2. Lưu chi tiết và xử lý kho/lượt bán
                foreach ($cart as $id => $details) {
                    DB::table('order_items')->insert([
                        'order_id' => $orderId,
                        'book_title' => $details['title'],
                        'quantity' => $details['quantity'],
                        'price' => $details['price'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $book = Book::find($id);
                    if ($book) {
                        // TRỪ số lượng tồn kho
                        $book->decrement('stock', $details['quantity']);
                        // TĂNG số lượng đã bán (Để hiện thị mục Sách Bán Chạy)
                        $book->increment('sold', $details['quantity']); 
                    }
                }
            });

            session()->forget('cart');
            return redirect()->route('client.home')->with('success', 'Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage());
        }
    }

    // Tra cứu đơn hàng
    public function trackOrder(Request $request) 
    {
        $orders = collect();
        if ($request->filled('phone')) {
            $orders = DB::table('orders')
                ->where('phone', $request->phone)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($orders as $order) {
                $order->items = DB::table('order_items')->where('order_id', $order->id)->get();
            }
        }
        return view('client.track_order', compact('orders'));
    }
}