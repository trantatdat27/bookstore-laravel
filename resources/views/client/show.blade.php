<!DOCTYPE html>
<html>
<head>
    <title>{{ $book->title }} - Chi tiết</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .book-description { line-height: 1.8; text-align: justify; color: #444; white-space: pre-line; }
        .price-tag { font-size: 2rem; color: #dc3545; font-weight: bold; }
        .img-detail { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-height: 600px; object-fit: contain; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <a href="{{ route('client.home') }}" class="btn btn-outline-secondary mb-4 border-0">← Quay lại cửa hàng</a>
    
    <div class="row bg-white p-5 shadow-sm rounded border">
        <div class="col-md-5 text-center mb-4 mb-md-0">
            @if($book->image)
                <img src="{{ asset($book->image) }}" class="img-detail" alt="{{ $book->title }}">
            @else
                <div style="height: 500px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 10px;" class="border">
                    <span class="text-muted fs-5">Ảnh bìa đang cập nhật</span>
                </div>
            @endif
        </div>

        <div class="col-md-7 ps-md-5">
            <span class="badge bg-primary px-3 py-2 mb-3">{{ $book->category->name ?? 'Sách' }}</span>
            <h1 class="display-5 fw-bold mb-2">{{ $book->title }}</h1>
            <p class="lead text-muted mb-4">Tác giả: <span class="text-dark fw-bold">{{ $book->author }}</span></p>
            
            <div class="price-tag mb-2">{{ number_format($book->price) }} VNĐ</div>
            
            <div class="mb-4">
                <strong>Tình trạng:</strong>
                @if($book->stock > 0)
                    <span class="badge bg-success">Còn {{ $book->stock }} cuốn trong kho</span>
                @else
                    <span class="badge bg-danger">Hiện đã hết hàng</span>
                @endif
            </div>
            
            <hr class="my-4">
            
            <h5 class="fw-bold mb-3">Giới thiệu nội dung:</h5>
            <div class="book-description">
                {{ $book->description ?? 'Nội dung chi tiết cho cuốn sách này đang được cập nhật.' }}
            </div>
            
            <div class="mt-5">
                @if($book->stock > 0)
                    <form action="{{ route('cart.add', $book->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg px-5 py-3 shadow">
                            🛒 THÊM VÀO GIỎ HÀNG
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary btn-lg px-5 py-3 shadow" disabled>
                        TẠM HẾT HÀNG
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

</body>
</html>