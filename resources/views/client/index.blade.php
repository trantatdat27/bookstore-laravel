@extends('client.layout')
@section('title', 'Bookstore - Mua sắm sách trực tuyến')

@section('custom_css')
<style>
    /* Hiệu ứng và định dạng cho thẻ Sách */
    .book-card { transition: all 0.3s ease; border-radius: 10px; overflow: hidden; height: 100%; border: 1px solid #f0f0f0; }
    .book-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; border-color: #dee2e6; }
    .book-card img { height: 200px; object-fit: cover; width: 100%; border-bottom: 1px solid #eee; }
    .book-badge { position: absolute; top: 8px; left: 8px; z-index: 10; font-size: 0.75rem; }
    
    /* Style cho thanh trượt danh mục ngang */
    .category-scroll { 
        display: flex; 
        overflow-x: auto; 
        white-space: nowrap; 
        padding-bottom: 8px; 
        gap: 10px;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .category-scroll::-webkit-scrollbar { display: none; /* Chrome, Safari and Opera */ }

    /* CSS FIX LỖI MẤT CHỮ KHI HOVER (CHUẨN 100%) */
    .btn-cat-custom {
        background-color: #ffffff;
        border: 1px solid #ced4da;
        color: #212529 !important; /* Chữ mặc định màu đen đậm */
        transition: all 0.2s ease;
    }
    .btn-cat-custom:hover {
        background-color: #f8f9fa; /* Nền xám rất nhạt */
        color: #0d6efd !important; /* Chữ đổi sang XANH DƯƠNG khi rê chuột */
        border-color: #0d6efd;
    }
    .btn-cat-active {
        background-color: #0d6efd !important;
        color: #ffffff !important;
        border-color: #0d6efd !important;
    }
</style>
@endsection

@section('content')
<div class="container py-4">

    <div class="category-scroll mb-4">
        <a href="{{ route('client.home') }}" 
           class="btn btn-sm rounded-pill px-4 fw-medium shadow-sm {{ !request('category_id') ? 'btn-cat-active' : 'btn-cat-custom' }}">
            Tất cả
        </a>
        
        @if(isset($categories))
            @foreach($categories as $cat)
                <a href="{{ route('client.home', ['category_id' => $cat->id]) }}" 
                   class="btn btn-sm rounded-pill px-4 fw-medium shadow-sm {{ request('category_id') == $cat->id ? 'btn-cat-active' : 'btn-cat-custom' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        @endif
    </div>

    @if(isset($bestsellers) && count($bestsellers) > 0)
        <div class="d-flex justify-content-between align-items-end mb-3 mt-2 border-bottom pb-2">
            <h4 class="fw-bold text-uppercase text-danger mb-0 border-danger border-bottom border-3 pb-2 d-inline-block">
                <i class="fas fa-fire me-1"></i> Sách Bán Chạy
            </h4>
        </div>
        
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3 mb-5">
            @foreach($bestsellers as $book)
            <div class="col">
                <div class="card book-card bg-white">
                    <span class="badge bg-danger book-badge shadow-sm">Hot</span>
                    
                    @if($book->image)
                        <img src="{{ asset($book->image) }}" alt="{{ $book->title }}">
                    @else
                        <div style="height: 200px; background: #f8f9fa;" class="d-flex align-items-center justify-content-center border-bottom text-muted">
                            <i class="fas fa-image fs-1 opacity-25"></i>
                        </div>
                    @endif

                    <div class="card-body d-flex flex-column p-2">
                        <h6 class="card-title fw-bold text-truncate mb-1" title="{{ $book->title }}" style="font-size: 0.95rem;">
                            {{ $book->title }}
                        </h6>
                        <p class="text-muted mb-2" style="font-size: 0.8rem;"><i class="fas fa-pen-nib me-1"></i> {{ $book->author }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-auto">
                            <span class="text-danger fw-bold" style="font-size: 1rem;">{{ number_format($book->price) }}đ</span>
                            <span class="text-muted" style="font-size: 0.75rem;">Đã bán {{ $book->sold ?? 0 }}</span>
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-7">
                                @if($book->stock > 0)
                                    <form action="{{ route('cart.add', $book->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm" style="border-radius: 6px;">
                                            <i class="fas fa-cart-plus"></i> Thêm
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary btn-sm w-100 fw-bold" style="border-radius: 6px;" disabled>Hết</button>
                                @endif
                            </div>
                            <div class="col-5">
                                <a href="{{ route('client.show', $book->id) }}" class="btn btn-outline-dark btn-sm w-100" style="border-radius: 6px;">
                                    Xem
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-end mb-3 mt-2 border-bottom pb-2">
        <h4 class="fw-bold text-uppercase text-dark mb-0 border-primary border-bottom border-3 pb-2 d-inline-block">
            {{ request('keyword') || request('category_id') ? 'Kết quả tìm kiếm' : 'Sách Mới Cập Nhật' }}
        </h4>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3 mb-5">
        @forelse($books as $book)
        <div class="col">
            <div class="card book-card bg-white">
                
                <span class="badge bg-primary book-badge shadow-sm">{{ $book->category->name ?? 'Sách' }}</span>
                
                @if($book->image)
                    <img src="{{ asset($book->image) }}" alt="{{ $book->title }}">
                @else
                    <div style="height: 200px; background: #f8f9fa;" class="d-flex align-items-center justify-content-center border-bottom text-muted">
                        <i class="fas fa-image fs-1 opacity-25"></i>
                    </div>
                @endif

                <div class="card-body d-flex flex-column p-2">
                    <h6 class="card-title fw-bold text-truncate mb-1" title="{{ $book->title }}" style="font-size: 0.95rem;">
                        {{ $book->title }}
                    </h6>
                    <p class="text-muted mb-2" style="font-size: 0.8rem;"><i class="fas fa-pen-nib me-1"></i> {{ $book->author }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-auto">
                        <span class="text-danger fw-bold" style="font-size: 1rem;">{{ number_format($book->price) }}đ</span>
                        <span class="{{ $book->stock > 0 ? 'text-success' : 'text-danger' }} fw-bold bg-light px-2 py-1 rounded" style="font-size: 0.75rem;">
                            {{ $book->stock > 0 ? 'Còn '.$book->stock : 'Hết' }}
                        </span>
                    </div>
                    
                    <div class="row g-1">
                        <div class="col-7">
                            @if($book->stock > 0)
                                <form action="{{ route('cart.add', $book->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm" style="border-radius: 6px;">
                                        <i class="fas fa-cart-plus"></i> Thêm
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary btn-sm w-100 fw-bold" style="border-radius: 6px;" disabled>Hết</button>
                            @endif
                        </div>
                        <div class="col-5">
                            <a href="{{ route('client.show', $book->id) }}" class="btn btn-outline-dark btn-sm w-100" style="border-radius: 6px;">
                                Xem
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 w-100">
            <div class="p-5 bg-white shadow-sm rounded-4 border">
                <i class="fas fa-box-open display-1 text-muted mb-3 opacity-50"></i>
                <p class="text-muted fs-5 fw-bold">Không tìm thấy cuốn sách nào.</p>
                <a href="{{ route('client.home') }}" class="btn btn-primary rounded-pill px-4 mt-2">Xem tất cả sách</a>
            </div>
        </div>
        @endforelse
    </div>
    
    @if(method_exists($books, 'links'))
        <div class="d-flex justify-content-center mt-4 mb-5">
            {{ $books->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection