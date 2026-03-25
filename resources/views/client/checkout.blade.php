<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .checkout-container { max-width: 1000px; margin-top: 50px; }
        .card { border: none; border-radius: 12px; }
        .form-label { fw-bold; color: #495057; }
        .order-summary { background-color: #ffffff; border-left: 4px solid #0d6efd; }
        .btn-checkout { padding: 12px; font-weight: bold; font-size: 1.1rem; border-radius: 8px; }
        .payment-method-box { border: 2px solid #e9ecef; border-radius: 8px; padding: 15px; cursor: pointer; transition: 0.3s; }
        .payment-method-box:hover { border-color: #0d6efd; background-color: #f0f7ff; }
    </style>
</head>
<body>

<div class="container checkout-container mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">XÁC NHẬN THANH TOÁN</h2>
        <p class="text-muted">Vui lòng điền đầy đủ thông tin để chúng tôi giao hàng đến bạn sớm nhất</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm p-4">
                <h4 class="mb-4 fw-bold">1. Thông tin giao hàng</h4>
                <form action="{{ route('cart.place_order') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Họ và tên người nhận</label>
                        <input type="text" name="fullname" class="form-control form-control-lg" placeholder="Ví dụ: Nguyễn Văn A" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="tel" name="phone" class="form-control form-control-lg" placeholder="Số điện thoại liên lạc" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ giao hàng</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện..." required></textarea>
                    </div>

                    <h4 class="mt-5 mb-4 fw-bold">2. Phương thức thanh toán</h4>
                    <div class="payment-method-box d-flex align-items-center mb-4">
                        <input class="form-check-input me-3" type="radio" name="payment_method" value="COD" id="codMethod" checked>
                        <label class="form-check-label w-100" for="codMethod">
                            <span class="d-block fw-bold">Thanh toán khi nhận hàng (COD)</span>
                            <small class="text-muted">Bạn sẽ thanh toán tiền mặt cho shipper khi nhận được hàng</small>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-checkout w-100 shadow">
                        XÁC NHẬN ĐẶT HÀNG
                    </button>
                    
                    <a href="{{ route('cart.index') }}" class="btn btn-link w-100 mt-2 text-decoration-none text-muted">
                        Quay lại giỏ hàng
                    </a>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm p-4 order-summary">
                <h4 class="mb-4 fw-bold">Đơn hàng của bạn</h4>
                
                <div class="order-items mb-4">
                    @php $total = 0; @endphp
                    @foreach($cart as $id => $item)
                        @php $total += $item['price'] * $item['quantity']; @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div style="max-width: 70%;">
                                <h6 class="mb-0 fw-bold">{{ $item['title'] }}</h6>
                                <small class="text-muted">SL: {{ $item['quantity'] }} x {{ number_format($item['price']) }}đ</small>
                            </div>
                            <span class="fw-bold text-dark">{{ number_format($item['price'] * $item['quantity']) }}đ</span>
                        </div>
                    @endforeach
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span>Tạm tính:</span>
                    <span>{{ number_format($total) }}đ</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span>Phí vận chuyển:</span>
                    <span class="text-success text-uppercase fw-bold">Miễn phí</span>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                    <span class="fs-5 fw-bold">Tổng thanh toán:</span>
                    <span class="fs-3 fw-bold text-danger">{{ number_format($total) }} VNĐ</span>
                </div>

                <div class="mt-4 p-3 bg-light rounded border border-warning">
                    <small class="text-dark d-block">
                        <strong>Lưu ý:</strong> Đơn hàng sẽ được xử lý trong vòng 24h. Cảm ơn bạn đã tin tưởng Bookstore!
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>