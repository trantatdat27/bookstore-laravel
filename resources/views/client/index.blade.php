<!DOCTYPE html>
<html>
<head>
    <title>Bookstore - Mua sắm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .book-card img { height: 250px; object-fit: cover; }
        .book-card { transition: transform 0.2s; height: 100%; }
        .book-card:hover { transform: translateY(-5px); }
        .w-fit { width: fit-content; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="text-end mb-3">
        <a href="{{ route('cart.track') }}" class="btn btn-outline-primary me-2">🔍 Tra cứu đơn hàng</a>
        <a href="{{ route('cart.index') }}" class="btn btn-warning position-relative">
            🛒 Giỏ hàng
        </a>
    </div>   

    <h1 class="text-center mb-5 fw-bold text-primary">CỬA HÀNG SÁCH</h1>

    <form action="{{ route('client.home') }}" method="GET" class="row g-3 mb-5 p-4 bg-white shadow-sm rounded border">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Tìm tên sách hoặc tác giả..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Lọc kết quả</button>
        </div>
    </form>

    <div class="row g-4">
        @forelse($books as $book)
        <div class="col-md-3">
            <div class="card book-card shadow-sm border-0">
                @if($book->image)
                    <img src="{{ asset($book->image) }}" class="card-img-top" alt="{{ $book->title }}">
                @else
                    <div style="height: 250px; background: #eee; display: flex; align-items: center; justify-content: center;" class="card-img-top text-muted">
                        Không có ảnh
                    </div>
                @endif

                <div class="card-body d-flex flex-column">
                    <span class="badge bg-info text-dark mb-2 w-fit">{{ $book->category->name ?? 'Sách' }}</span>
                    <h5 class="card-title fw-bold text-truncate">{{ $book->title }}</h5>
                    <p class="text-muted small mb-1">Tác giả: {{ $book->author }}</p>
                    
                    <p class="small mb-2">
                        @if($book->stock > 0)
                            <span class="text-success fw-bold">● Còn hàng ({{ $book->stock }})</span>
                        @else
                            <span class="text-danger fw-bold">○ Hết hàng</span>
                        @endif
                    </p>

                    <h4 class="text-danger fw-bold mt-auto">{{ number_format($book->price) }}đ</h4>
                    
                    <div class="mt-3">
                        @if($book->stock > 0)
                            <form action="{{ route('cart.add', $book->id) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">Thêm vào giỏ</button>
                            </form>
                        @else
                            <button class="btn btn-secondary w-100 mb-2" disabled>Hết hàng</button>
                        @endif
                        <a href="{{ route('client.show', $book->id) }}" class="btn btn-outline-dark w-100">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted fs-4">Không tìm thấy cuốn sách nào.</p>
        </div>
        @endforelse
    </div>
</div>

</body>
</html>