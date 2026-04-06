<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bookstore - Mua sắm sách')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, sans-serif; }
        
        /* Header CSS */
        .search-group .form-control { border-top-left-radius: 50px; border-bottom-left-radius: 50px; padding-left: 20px; }
        .search-group .btn { border-top-right-radius: 50px; border-bottom-right-radius: 50px; }
        .search-group select { max-width: 180px; cursor: pointer; }
        
        /* Footer CSS */
        footer { background-color: #212529; color: #adb5bd; }
        footer a { color: #adb5bd; text-decoration: none; transition: 0.2s; }
        footer a:hover { color: #fff; padding-left: 5px; }
        .social-icons a { font-size: 1.2rem; margin-right: 15px; color: #adb5bd; }
        .social-icons a:hover { color: #0d6efd; padding-left: 0; }
        
        @yield('custom_css')
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3">
    <div class="container-fluid px-lg-5">
        <a class="navbar-brand fw-bold text-primary fs-4" href="{{ route('client.home') }}">
            <i class="fas fa-book-open me-2"></i>BOOKSTORE
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            
            <form action="{{ route('client.home') }}" method="GET" class="d-flex mx-lg-auto my-3 my-lg-0 col-12 col-lg-5">
    <div class="input-group search-group shadow-sm border border-primary rounded-pill bg-white">
        <input type="text" name="keyword" class="form-control border-0 shadow-none bg-transparent" placeholder="Tìm tên sách, tác giả..." value="{{ request('keyword') }}">
        
        <button class="btn btn-primary px-4 fw-bold" type="submit"><i class="fas fa-search"></i></button>
    </div>
</form>

            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-2 d-none d-xl-block">
                    <a href="{{ route('cart.track') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                        <i class="fas fa-truck me-1"></i> Tra cứu đơn
                    </a>
                </li>
                <li class="nav-item me-3">
                    <a href="{{ route('cart.index') }}" class="btn btn-warning btn-sm px-3 position-relative rounded-pill fw-bold text-dark">
                        <i class="fas fa-shopping-cart me-1"></i> Giỏ hàng
                    </a>
                </li>
                <div class="vr mx-2 d-none d-lg-block"></div>

                @guest
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a href="{{ route('login') }}" class="btn btn-light btn-sm text-dark me-2 border rounded-pill px-3">Đăng nhập</a>
                    </li>
                    <li class="nav-item mt-2 mt-lg-0">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm rounded-pill px-3">Đăng ký</a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item dropdown ms-lg-2 mt-2 mt-lg-0">
                        <a class="nav-link dropdown-toggle fw-bold text-dark py-0" href="#" id="userMenu" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle text-primary fs-5 align-middle me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 rounded-3">
                            @if(Auth::user()->role === 'admin')
                                <li><a class="dropdown-item py-2" href="{{ route('admin.index') }}"><i class="fas fa-cog me-2"></i>Trang Quản trị</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger fw-bold"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="flex-grow-1">
    @yield('content')
</main>

<footer class="pt-5 pb-4 mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-white fw-bold mb-3"><i class="fas fa-book-open text-primary me-2"></i> BOOKSTORE</h5>
                <p class="small">Chúng tôi cung cấp hàng ngàn tựa sách đa dạng thể loại. Cam kết sách thật, chất lượng cao và giao hàng nhanh chóng.</p>
                <div class="social-icons mt-3">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="text-white fw-bold mb-3">Về chúng tôi</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#">Giới thiệu</a></li>
                    <li class="mb-2"><a href="#">Tuyển dụng</a></li>
                    <li class="mb-2"><a href="#">Bảo mật</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="text-white fw-bold mb-3">Hỗ trợ</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#">Hướng dẫn mua hàng</a></li>
                    <li class="mb-2"><a href="#">Đổi trả</a></li>
                    <li class="mb-2"><a href="{{ route('cart.track') }}">Tra cứu đơn</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-white fw-bold mb-3">Liên hệ</h5>
                <ul class="list-unstyled small">
                    <li class="mb-3"><i class="fas fa-map-marker-alt text-primary me-2"></i> 123 Đường Sách, TP. HCM</li>
                    <li class="mb-3"><i class="fas fa-phone-alt text-primary me-2"></i> 1900 1234</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start small">&copy; {{ date('Y') }} Bookstore. All rights reserved.</div>
            <div class="col-md-6 text-center text-md-end">
                <i class="fab fa-cc-visa fs-3 me-2 text-muted"></i>
                <i class="fab fa-cc-mastercard fs-3 me-2 text-muted"></i>
            </div>
        </div>
    </div>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var myCarousel = document.querySelector('#heroCarousel');
        var carousel = new bootstrap.Carousel(myCarousel, {
            interval: 3000, // Thời gian chuyển ảnh: 3000ms = 3 giây
            ride: 'carousel',
            pause: 'hover' // Sẽ tạm dừng khi người dùng di chuột vào banner
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>