@extends('client.layout')
@section('title', 'Thanh toán đơn hàng')

@section('custom_css')
<style>
    .checkout-container { max-width: 1000px; margin-top: 20px; }
    .card { border: none; border-radius: 16px; }
    .form-label { font-weight: 600; color: #495057; }
    .form-control { border-radius: 10px; padding: 12px 15px; }
    .order-summary { background-color: #ffffff; border-top: 5px solid #0d6efd; }
    .payment-method-box { border: 2px solid #e9ecef; border-radius: 12px; padding: 15px; cursor: pointer; transition: 0.3s; }
    .payment-method-box:hover, .payment-method-box.active { border-color: #0d6efd; background-color: #f0f7ff; }
</style>
@endsection

@section('content')
<div class="container checkout-container mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary"><i class="fas fa-check-circle me-2"></i>XÁC NHẬN THANH TOÁN</h2>
        <p class="text-muted">Vui lòng điền đầy đủ thông tin để chúng tôi giao hàng đến bạn sớm nhất</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm p-4 p-md-5">
                <h4 class="mb-4 fw-bold border-bottom pb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i>1. Thông tin giao hàng</h4>
                <form action="{{ route('cart.place_order') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Họ và tên người nhận</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="fullname" class="form-control border-start-0 ps-0" placeholder="Ví dụ: Nguyễn Văn A" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                            <input type="tel" name="phone" class="form-control border-start-0 ps-0" placeholder="Số điện thoại liên lạc" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Địa chỉ giao hàng chi tiết</label>
                        <textarea name="address" class="form-control shadow-sm" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện..." required></textarea>
                    </div>

                    <h4 class="mt-5 mb-4 fw-bold border-bottom pb-2"><i class="fas fa-credit-card text-primary me-2"></i>2. Phương thức thanh toán</h4>
                    <div class="payment-method-box d-flex align-items-center mb-4 active shadow-sm">
                        <input class="form-check-input me-3 fs-4" type="radio" name="payment_method" value="COD" id="codMethod" checked>
                        <label class="form-check-label w-100" for="codMethod" style="cursor: pointer;">
                            <span class="d-block fw-bold fs-5 text-dark">Thanh toán khi nhận hàng (COD)</span>
                            <small class="text-muted">Bạn sẽ thanh toán bằng tiền mặt cho shipper khi nhận được hàng.</small>
                        </label>
                        <i class="fas fa-hand-holding-usd text-primary fs-1 ms-auto opacity-25"></i>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 shadow rounded-pill py-3 fw-bold fs-5 mt-2">
                        XÁC NHẬN ĐẶT HÀNG <i class="fas fa-paper-plane ms-2"></i>
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('cart.index') }}" class="text-decoration-none text-muted">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại giỏ hàng
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm p-4 order-summary position-sticky" style="top: 100px;">
                <h4 class="mb-4 fw-bold"><i class="fas fa-receipt text-primary me-2"></i>Đơn hàng của bạn</h4>
                
                <div class="order-items mb-4">
                    @php $total = 0; @endphp
                    @foreach($cart as $id => $item)
                        @php $total += $item['price'] * $item['quantity']; @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-light">
                            <div style="max-width: 70%;">
                                <h6 class="mb-1 fw-bold text-dark">{{ $item['title'] }}</h6>
                                <span class="badge bg-light text-dark border px-2 py-1">SL: {{ $item['quantity'] }}</span>
                                <small class="text-muted ms-1">x {{ number_format($item['price']) }}đ</small>
                            </div>
                            <span class="fw-bold text-primary">{{ number_format($item['price'] * $item['quantity']) }}đ</span>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between mb-2 text-muted"><span>Tạm tính:</span><span>{{ number_format($total) }}đ</span></div>
                <div class="d-flex justify-content-between mb-4 text-muted"><span>Phí vận chuyển:</span><span class="text-success text-uppercase fw-bold"><i class="fas fa-shipping-fast me-1"></i>Miễn phí</span></div>

                <div class="d-flex justify-content-between align-items-center border-top border-2 pt-3">
                    <span class="fs-5 fw-bold text-dark">Tổng thanh toán:</span>
                    <span class="fs-3 fw-bold text-danger">{{ number_format($total) }}đ</span>
                </div>

                <div class="mt-4 p-3 bg-light rounded-3 border border-warning">
                    <div class="d-flex">
                        <i class="fas fa-info-circle text-warning fs-4 me-2 mt-1"></i>
                        <small class="text-dark"><strong>Lưu ý:</strong> Đơn hàng sẽ được xử lý và giao cho đơn vị vận chuyển trong vòng 24h.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection