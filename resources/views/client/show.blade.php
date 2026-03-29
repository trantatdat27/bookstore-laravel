@extends('client.layout')
@section('title', $book->title . ' - Chi tiết sách')

@section('custom_css')
<style>
    .book-description { line-height: 1.8; text-align: justify; color: #555; white-space: pre-line; font-size: 1.05rem; }
    .price-tag { font-size: 2.5rem; color: #dc3545; font-weight: 800; }
    .img-detail { border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); width: 100%; max-height: 550px; object-fit: contain; }
</style>
@endsection

@section('content')
<div class="container py-5">
    <a href="{{ route('client.home') }}" class="btn btn-outline-secondary mb-4 rounded-pill px-4 shadow-sm border-0 bg-white">
        <i class="fas fa-arrow-left me-2"></i>Quay lại cửa hàng
    </a>
    
    <div class="row bg-white p-4 p-md-5 shadow-sm rounded-4 border-0">
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
</div>
@endsection