<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng của bạn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Giỏ hàng của bạn</h2>
        <a href="{{ route('client.home') }}" class="btn btn-outline-secondary">Tiếp tục mua sắm</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if(count($cart) > 0)
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th style="width: 100px;">Số lượng</th>
                            <th>Thành tiền</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0 @endphp
                        @foreach($cart as $id => $details)
                            @php $total += $details['price'] * $details['quantity'] @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $details['title'] }}</div>
                                    <small class="text-muted">Tác giả: {{ $details['author'] }}</small>
                                </td>
                                <td>{{ number_format($details['price']) }}đ</td>
                                <td>{{ $details['quantity'] }}</td>
                                <td class="text-danger fw-bold">{{ number_format($details['price'] * $details['quantity']) }}đ</td>
                                <td class="text-end">
                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="row mt-4 align-items-center">
                    <div class="col-md-6">
                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">Xóa sạch giỏ hàng</button>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <h4 class="mb-3">Tổng tiền: <span class="text-danger fw-bold">{{ number_format($total) }} VNĐ</span></h4>
                        
                        <form action="{{ route('cart.checkout') }}" method="POST" onsubmit="return confirm('Xác nhận đặt hàng?')">
                            @csrf
                            <a href="{{ route('cart.checkout') }}" class="btn btn-success btn-lg px-5 shadow">THANH TOÁN NGAY</a>
                        </form>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="fs-1 mb-3">🛒</div>
                    <p class="text-muted fs-5">Giỏ hàng của bạn đang trống!</p>
                    <a href="{{ route('client.home') }}" class="btn btn-primary">Mua sắm ngay thôi</a>
                </div>
            @endif
        </div>
    </div>
</div>

</body>
</html>