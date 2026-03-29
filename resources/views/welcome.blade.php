<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">NHÀ SÁCH ONLINE</a>
            <div class="ms-auto">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary">Bảng điều khiển</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary me-2">Đăng nhập</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">Đăng ký</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <div class="container mt-5 text-center">
        <h1>Chào mừng bạn đến với Bookstore!</h1>
        <p>Hệ thống quản lý và mua sắm sách trực tuyến hiện đại.</p>
    </div>
</body>
</html>