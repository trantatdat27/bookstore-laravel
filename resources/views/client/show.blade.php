@extends('client.layout')
@section('title', $book->title . ' - Chi tiết sách')

@section('custom_css')
<style>
    .book-description { line-height: 1.8; text-align: justify; color: #555; white-space: pre-line; font-size: 1.05rem; }
    .price-tag { font-size: 2.5rem; color: #dc3545; font-weight: 800; }
    .img-detail { border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); width: 100%; max-height: 550px; object-fit: contain; }
    .review-item { background: #f8f9fa; border-left: 4px solid #0d6efd; padding: 20px; margin-bottom: 15px; border-radius: 8px; }
    .review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    .rating-stars { color: #ffc107; font-size: 1.1rem; }
    .review-comment { color: #555; line-height: 1.6; margin-top: 10px; }
    .review-actions { margin-top: 10px; font-size: 0.9rem; }
    .reviews-container {
        max-height: 800px;
        overflow-y: auto;
        padding-right: 10px;
    }
    .reviews-container::-webkit-scrollbar {
        width: 6px;
    }
    .reviews-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .reviews-container::-webkit-scrollbar-thumb {
        background: #0d6efd;
        border-radius: 10px;
    }
    .reviews-container::-webkit-scrollbar-thumb:hover {
        background: #054bc0;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <a href="{{ route('client.home') }}" class="btn btn-outline-secondary mb-4 rounded-pill px-4 shadow-sm border-0 bg-white">
        <i class="fas fa-arrow-left me-2"></i>Quay lại cửa hàng
    </a>
    
    <!-- HÀNG 1: Ảnh + Thông tin cơ bản -->
    <div class="row bg-white p-4 p-md-5 shadow-sm rounded-4 border-0 mb-4">
        <div class="col-md-5 text-center mb-5 mb-md-0 position-relative">
            @if($book->image)
                <img src="{{ asset($book->image) }}" class="img-detail" alt="{{ $book->title }}">
            @else
                <div style="height: 500px; background: #f8f9fa;" class="d-flex align-items-center justify-content-center rounded-4 border shadow-sm">
                    <div class="text-muted text-center"><i class="fas fa-image display-1 mb-3 opacity-25 d-block"></i><span class="fs-5">Ảnh bìa đang cập nhật</span></div>
                </div>
            @endif
        </div>

        <div class="col-md-7 ps-md-5 d-flex flex-column">
            <div>
                <span class="badge bg-primary px-3 py-2 mb-3 rounded-pill shadow-sm fs-6"><i class="fas fa-tag me-1"></i> {{ $book->category->name ?? 'Sách' }}</span>
                <h1 class="display-5 fw-bold mb-3 text-dark">{{ $book->title }}</h1>
                <p class="fs-5 text-muted mb-4"><i class="fas fa-pen-nib me-2"></i>Tác giả: <span class="text-primary fw-bold">{{ $book->author }}</span></p>
                
                <div class="price-tag mb-3">{{ number_format($book->price) }}đ</div>
                
                <div class="mb-4 p-3 bg-light rounded-3 d-inline-block border">
                    <span class="fw-bold text-dark me-2"><i class="fas fa-warehouse me-1"></i> Tình trạng kho:</span>
                    @if($book->stock > 0)
                        <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>Còn {{ $book->stock }} cuốn</span>
                    @else
                        <span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i>Đã hết hàng</span>
                    @endif
                </div>
            </div>
            
            <hr class="my-4 border-secondary opacity-25">
            
            <div class="flex-grow-1">
                <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-book-open me-2 text-primary"></i>Giới thiệu nội dung:</h5>
                <div class="book-description bg-white p-3 rounded-3 border-start border-4 border-primary shadow-sm">{{ $book->description ?? 'Nội dung chi tiết cho cuốn sách này đang được cập nhật.' }}</div>
            </div>
            
            <div class="mt-5">
                @if($book->stock > 0)
                    <form action="{{ route('cart.add', $book->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill shadow fw-bold fs-5">
                            <i class="fas fa-cart-plus me-2"></i> THÊM VÀO GIỎ HÀNG
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary btn-lg w-100 py-3 rounded-pill shadow fw-bold fs-5" disabled>
                        <i class="fas fa-box-open me-2"></i> TẠM HẾT HÀNG
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- HÀNG 2: Mô tả + Đánh giá (2 cột trên desktop, full width trên mobile) -->
    <div class="row g-4">
        <!-- Cột trái: Mô tả sách (không cần vì đã ở hàng 1) -->
        
        <!-- Cột phải: PHẦN ĐÁNH GIÁ VÀ BÌNH LUẬN (Full width responsive) -->
        <div class="col-12">
            <div class="bg-white p-4 p-md-5 shadow-sm rounded-4 border-0">
                <h3 class="fw-bold mb-4 text-dark"><i class="fas fa-star me-2 text-warning"></i>Đánh giá từ khách hàng</h3>

                <!-- Nút tạo đánh giá -->
                @auth
                    <div class="mb-4">
                        @if(auth()->user()->hasPurchasedBook($book->id))
                            @if(!auth()->user()->hasReviewedBook($book->id))
                                <a href="{{ route('review.create', $book->id) }}" class="btn btn-success rounded-pill shadow">
                                    <i class="fas fa-star me-2"></i>Viết đánh giá của bạn
                                </a>
                            @else
                                <div class="alert alert-info rounded-3 mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Bạn đã đánh giá sách này rồi. <a href="{{ route('review.edit', auth()->user()->getReviewForBook($book->id)->id) }}" class="alert-link">Chỉnh sửa</a>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning rounded-3 mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>Bạn chỉ có thể đánh giá sách khi đơn hàng đã hoàn thành!
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mb-4">
                        <a href="{{ route('login') }}" class="btn btn-primary rounded-pill shadow">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập để đánh giá
                        </a>
                    </div>
                @endauth

                <hr class="my-4 border-secondary opacity-25">

                <!-- Danh sách đánh giá đã duyệt -->
                @php
                    $approvedReviews = $book->reviews()->where('status', 'approved')->latest()->get();
                @endphp

                @if($approvedReviews->isNotEmpty())
                    <div class="reviews-list reviews-container">
                        @foreach($approvedReviews as $review)
                            <div class="review-item">
                                <div class="review-header">
                                    <div>
                                        <strong class="text-dark">{{ $review->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="rating-stars">
                                        @for($i = 0; $i < $review->rating; $i++)
                                            <i class="fas fa-star"></i>
                                        @endfor
                                        @for($i = $review->rating; $i < 5; $i++)
                                            <i class="far fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if($review->comment)
                                    <div class="review-comment">{{ $review->comment }}</div>
                                @endif
                                @auth
                                    @if(auth()->id() === $review->user_id)
                                        <div class="review-actions">
                                            <a href="{{ route('review.edit', $review->id) }}" class="btn-link text-primary">Sửa</a>
                                            <form action="{{ route('review.destroy', $review->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-link text-danger border-0 bg-transparent" onclick="return confirm('Bạn chắc chắn muốn xóa?')">Xóa</button>
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-secondary rounded-3 text-center mb-0">
                        <i class="fas fa-comments me-2"></i>Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá!
                    </div>
                @endif
            </div>
        </div>
        </div><!-- end col-12 -->
    </div><!-- end row -->
</div><!-- end container -->
@endsection