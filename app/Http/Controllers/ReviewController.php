<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Kiểm tra xem user có mua sách này với đơn hàng đã hoàn thành không
     */
    public function hasPurchased($userId, $bookId)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', $userId)
            ->where('order_items.book_id', $bookId)
            ->where('orders.status', 'completed')
            ->exists();
    }

    /**
     * Hiển thị form tạo review
     */
    public function create($bookId)
    {
        // Kiểm tra sách có tồn tại không
        $book = Book::findOrFail($bookId);

        // Kiểm tra nếu chưa đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đánh giá sách!');
        }

        // Kiểm tra user đã mua sách này chưa
        if (!$this->hasPurchased(Auth::id(), $bookId)) {
            return redirect()->back()->with('error', 'Bạn phải mua sách này trước khi có thể đánh giá!');
        }

        // Kiểm tra user đã review chưa
        $existingReview = Review::where('user_id', Auth::id())
            ->where('book_id', $bookId)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'Bạn đã đánh giá sách này rồi!');
        }

        return view('client.review.create', compact('book'));
    }

    /**
     * Lưu review từ khách hàng
     */
    public function store(Request $request, $bookId)
    {
        // Kiểm tra nếu chưa đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }

        // Kiểm tra user đã mua sách này và đơn hàng đã hoàn thành
        if (!$this->hasPurchased(Auth::id(), $bookId)) {
            return redirect()->back()->with('error', 'Bạn chỉ có thể đánh giá sách khi đơn hàng đã hoàn thành!');
        }

        // Validate input
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá',
            'rating.min' => 'Đánh giá phải từ 1 sao trở lên',
            'rating.max' => 'Đánh giá không được vượt quá 5 sao',
            'comment.max' => 'Bình luận không được vượt quá 1000 ký tự'
        ]);

        // Lấy đơn hàng gần nhất của user cho sách này
        $order = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', Auth::id())
            ->where('order_items.book_id', $bookId)
            ->where('orders.status', '!=', 'canceled')
            ->latest('orders.created_at')
            ->select('orders.id')
            ->first();

        // Tạo review
        Review::create([
            'user_id' => Auth::id(),
            'book_id' => $bookId,
            'order_id' => $order?->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending' // Admin phải duyệt trước
        ]);

        return redirect()->route('client.show', $bookId)->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt!');
    }

    /**
     * Hiển thị form sửa review
     */
    public function edit($id)
    {
        $review = Review::findOrFail($id);

        // Kiểm tra quyền
        if ($review->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền sửa đánh giá này!');
        }

        $book = $review->book;
        return view('client.review.edit', compact('review', 'book'));
    }

    /**
     * Cập nhật review
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // Kiểm tra quyền
        if ($review->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền sửa đánh giá này!');
        }

        // Validate input
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending' // Reset status để admin duyệt lại
        ]);

        return redirect()->route('client.show', $review->book_id)->with('success', 'Cập nhật đánh giá thành công!');
    }

    /**
     * Xóa review (khách hàng xóa của mình)
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // Kiểm tra quyền
        if ($review->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa đánh giá này!');
        }

        $bookId = $review->book_id;
        $review->delete();

        return redirect()->route('client.show', $bookId)->with('success', 'Đánh giá đã bị xóa!');
    }

    // ========== ADMIN FUNCTIONS ==========

    /**
     * Hiển thị danh sách reviews (admin)
     */
    public function adminIndex(Request $request)
    {
        $query = Review::with(['user', 'book']);

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo sách
        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        $reviews = $query->latest()->paginate(15);
        $books = Book::all();

        return view('admin.review.index', compact('reviews', 'books'));
    }

    /**
     * Duyệt/Phê duyệt review (admin)
     */
    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Đã phê duyệt đánh giá!');
    }

    /**
     * Từ chối review (admin)
     */
    public function reject($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Đã từ chối đánh giá!');
    }

    /**
     * Xóa review (admin)
     */
    public function adminDestroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Đánh giá đã bị xóa!');
    }
}
