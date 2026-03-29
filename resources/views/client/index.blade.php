@extends('client.layout')
@section('title', 'Bookstore - Mua sắm sách trực tuyến')

@section('custom_css')
<style>
    /* Hiệu ứng và định dạng cho thẻ Sách */
    .book-card { transition: all 0.3s ease; border-radius: 12px; overflow: hidden; height: 100%; }
    .book-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
    .book-card img { height: 260px; object-fit: cover; width: 100%; border-bottom: 1px solid #eee; }
    .book-badge { position: absolute; top: 10px; left: 10px; z-index: 10; }
</style>
@endsection

@section('content')
<div class="container py-4">
    
    <div id="book-list" class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-2 mt-3">
        <h3 class="fw-bold text-uppercase text-dark mb-0 border-primary border-bottom border-3 pb-2 d-inline-block">
            {{ request('search') || request('category') ? 'Kết quả tìm kiếm' : 'Sách Mới Cập Nhật' }}
        </h3>
    </div>

    <div class="row g-4 mb-5">
        @forelse($books as $book)
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="card book-card shadow-sm border-0 position-relative bg-white">
                
                <span class="badge bg-primary book-badge shadow-sm">{{ $book->category->name ?? 'Sách' }}</span>
                
                @if($book->image)
                    <img src="{{ asset($book->image) }}" alt="{{ $book->title }}">
                @else
                    <div style="height: 260px; background: #f8f9fa;" class="d-flex align-items-center justify-content-center border-bottom text-muted">
                        <i class="fas fa-image fs-1 opacity-25"></i>
                    </div>
                @endif

                <div class="card-body d-flex flex-column p-3">
                    <h5 class="card-title fw-bold text-truncate mb-1" title="{{ $book->title }}">{{ $book->title }}</h5>
                    <p class="text-muted small mb-3"><i class="fas fa-pen-nib me-1"></i> {{ $book->author }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-auto">
                        <h5 class="text-danger fw-bold mb-0">{{ number_format($book->price) }}đ</h5>
                        <small class="{{ $book->stock > 0 ? 'text-success' : 'text-danger' }} fw-bold bg-light px-2 py-1 rounded">
                            {{ $book->stock > 0 ? 'Còn '.$book->stock : 'Hết hàng' }}
                        </small>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-7">
                            @if($book->stock > 0)
                                <form action="{{ route('cart.add', $book->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" style="border-radius: 8px;">
                                        <i class="fas fa-cart-plus"></i> Thêm
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary w-100 fw-bold" style="border-radius: 8px;" disabled>Hết hàng</button>
                            @endif
                        </div>
                        <div class="col-5">
                            <a href="{{ route('client.show', $book->id) }}" class="btn btn-outline-dark w-100" style="border-radius: 8px;">
                                Chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="p-5 bg-white shadow-sm rounded-4 border">
                <i class="fas fa-box-open display-1 text-muted mb-3 opacity-50"></i>
                <p class="text-muted fs-4 fw-bold">Không tìm thấy cuốn sách nào phù hợp.</p>
                <a href="{{ route('client.home') }}" class="btn btn-primary rounded-pill px-4 mt-2">Quay lại trang chủ</a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection