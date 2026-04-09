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

    // Thêm dòng tính tổng tiền này
    $total = 0;
    foreach($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Truyền cả $cart và $total sang view
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

                // 1. KIỂM TRA KHO TRƯỚC (Chống lỗi click không chạy)
                foreach ($cart as $id => $details) {
                    $book = Book::find($id);
                    if (!$book || $book->stock < $details['quantity']) {
                        throw new \Exception("Sách '{$details['title']}' không đủ số lượng trong kho!");
                    }
                }

                // 2. Lưu đơn hàng chính
                $orderId = DB::table('orders')->insertGetId([
                    'user_id' => auth()->id(),
                    'customer_name' => $request->fullname,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'payment_method' => $request->payment_method ?? 'COD',
                    'total_amount' => $total,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // 3. Lưu chi tiết và xử lý kho/lượt bán
                foreach ($cart as $id => $details) {
                    DB::table('order_items')->insert([
                        'order_id' => $orderId,
                        'book_id'  => $id,
                        'book_title' => $details['title'],
                        'quantity' => $details['quantity'],
                        'price' => $details['price'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $book = Book::find($id);
                    $book->decrement('stock', $details['quantity']); // Trừ kho
                    $book->increment('sold', $details['quantity']);  // Tăng bán chạy
                }
            });

            session()->forget('cart');
            return redirect()->route('client.home')->with('success', 'Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.');

        } catch (\Exception $e) {
            // withInput() giúp giữ lại chữ khách đã nhập khi load lại trang
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Tra cứu đơn hàng
    public function trackOrder() 
{
    // 1. Kiểm tra nếu chưa đăng nhập thì bắt đăng nhập
    if (!auth()->check()) {
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem lịch sử đơn hàng.');
    }

    // 2. Lấy danh sách đơn hàng của User đang đăng nhập
    $orders = DB::table('orders')
        ->where('user_id', auth()->id()) // Lọc theo ID tài khoản
        ->orderBy('created_at', 'desc')
        ->get();

    // 3. Lấy chi tiết từng sản phẩm trong đơn hàng
    foreach ($orders as $order) {
        $order->items = DB::table('order_items')->where('order_id', $order->id)->get();
    }

    return view('client.track_order', compact('orders'));
}

// Hàm xử lý hủy đơn hàng từ phía khách hàng (Client)
    public function cancelOrder($id)
{
    $order = DB::table('orders')->where('id', $id)->where('user_id', auth()->id())->first();

    if (!$order || $order->status !== 'pending') {
        return redirect()->back()->with('error', 'Không thể hủy đơn hàng này.');
    }

    try {
        DB::transaction(function () use ($id) {
            $items = DB::table('order_items')->where('order_id', $id)->get();

            foreach ($items as $item) {
                // 1. Cộng lại số lượng vào kho (Ép kiểu int để an toàn)
                DB::table('books')->where('id', $item->book_id)->increment('stock', (int)$item->quantity);
                
                // 2. Xử lý trừ lượt bán an toàn (Chống lỗi âm UNSIGNED)
                $book = DB::table('books')->where('id', $item->book_id)->first();
                if ($book) {
                    if ($book->sold >= $item->quantity) {
                        DB::table('books')->where('id', $item->book_id)->decrement('sold', (int)$item->quantity);
                    } else {
                        // Nếu số đã bán hiện tại nhỏ hơn số lượng hủy, trả về 0 luôn để tránh lỗi DB
                        DB::table('books')->where('id', $item->book_id)->update(['sold' => 0]);
                    }
                }
            }

            // 3. Cập nhật trạng thái đơn
            DB::table('orders')->where('id', $id)->update([
                'status' => 'canceled',
                'updated_at' => now()
            ]);
        });

        return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công.');

    } catch (\Exception $e) {
        // Ném lỗi ra màn hình để bạn dễ dàng bắt bệnh nếu có lỗi khác
        return redirect()->back()->with('error', 'Lỗi hệ thống khi hủy đơn: ' . $e->getMessage());
    }
}
// Cập nhật số lượng sản phẩm trong giỏ hàng
public function update(Request $request)
{
    if($request->id && $request->quantity){
        $cart = session()->get('cart');
        
        // Kiểm tra tồn kho trước khi cập nhật
        $book = DB::table('books')->where('id', $request->id)->first();
        if($request->quantity > $book->stock) {
            return response()->json(['message' => 'Số lượng vượt quá kho hàng!'], 400);
        }

        $cart[$request->id]["quantity"] = $request->quantity;
        session()->put('cart', $cart);
        session()->flash('success', 'Giỏ hàng đã được cập nhật!');
    }
}
}