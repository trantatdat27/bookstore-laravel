@extends('client.layout')
@section('title', 'Tra cứu đơn hàng')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('client.home') }}" class="btn btn-outline-secondary rounded-pill px-4 bg-white shadow-sm border-0">
            <i class="fas fa-arrow-left me-2"></i>Quay lại cửa hàng
        </a>
    </div>
    
    <div class="text-center mb-5">
        <i class="fas fa-search-location display-4 text-primary mb-3"></i>
        <h2 class="fw-bold text-dark">Tra cứu lịch sử đơn hàng</h2>
        <p class="text-muted">Nhập số điện thoại bạn đã dùng để đặt hàng để kiểm tra trạng thái</p>
    </div>
    
    <form action="{{ route('cart.track') }}" method="GET" class="mx-auto mb-5" style="max-width: 600px;">
        <div class="input-group shadow-sm border border-primary rounded-pill bg-white p-1">
            <span class="input-group-text bg-transparent border-0 ms-2"><i class="fas fa-phone-alt text-muted"></i></span>
            <input type="text" name="phone" class="form-control border-0 shadow-none bg-transparent" placeholder="Nhập số điện thoại..." value="{{ request('phone') }}" required>
            <button class="btn btn-primary rounded-pill px-4 fw-bold" type="submit">Tìm kiếm <i class="fas fa-search ms-1"></i></button>
        </div>
    </form>

    @if(isset($orders) && $orders->count() > 0)
        <div class="mx-auto" style="max-width: 800px;">
            <div class="alert alert-success shadow-sm rounded-3 mb-4 border-0">
                <i class="fas fa-check-circle me-2"></i>Tìm thấy <strong>{{ $orders->count() }}</strong> đơn hàng cho số điện thoại: <span class="fw-bold">{{ request('phone') }}</span>
            </div>
            
            @foreach($orders as $order)
                <div class="card shadow-sm mb-4 border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <span class="fs-5 text-dark"><i class="fas fa-hashtag text-muted me-1"></i>Mã đơn: <strong>{{ $order->id }}</strong></span>
                        @php
                            $badgeClass = 'bg-info text-dark'; $icon = 'fa-spinner';
                            if($order->status == 'pending') { $badgeClass = 'bg-warning text-dark'; $icon = 'fa-clock'; }
                            elseif($order->status == 'completed') { $badgeClass = 'bg-success'; $icon = 'fa-check-double'; }
                            elseif($order->status == 'cancelled') { $badgeClass = 'bg-danger'; $icon = 'fa-times-circle'; }
                        @endphp
                        <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill fs-6 shadow-sm"><i class="fas {{ $icon }} me-1"></i> {{ strtoupper($order->status) }}</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-4 bg-light p-3 rounded-3 mx-0">
                            <div class="col-sm-6 mb-2 mb-sm-0">
                                <small class="text-muted d-block"><i class="fas fa-user me-1"></i> Khách hàng:</small>
                                <strong class="fs-5">{{ $order->customer_name }}</strong>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <small class="text-muted d-block"><i class="fas fa-calendar-alt me-1"></i> Ngày đặt:</small>
                                <span class="fw-bold">{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm border-bottom">
                                <thead class="text-muted border-bottom">
                                    <tr><th>Sản phẩm</th><th class="text-center">Số lượng</th><th class="text-end">Giá</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td class="py-2 fw-bold text-dark">{{ $item->book_title }}</td>
                                            <td class="text-center py-2"><span class="badge bg-secondary rounded-pill">x{{ $item->quantity }}</span></td>
                                            <td class="text-end py-2 text-primary fw-bold">{{ number_format($item->price) }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mt-3">
                            <div class="mb-3 mb-md-0 text-muted small" style="max-width: 60%;"><i class="fas fa-map-marker-alt text-danger me-1"></i> Giao đến: {{ $order->address }}</div>
                            <div class="text-md-end">
                                <span class="text-muted d-block mb-1">Tổng cộng</span>
                                <h4 class="text-danger mb-0 fw-bold">{{ number_format($order->total_amount) }}đ</h4>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif(request()->has('phone'))
        <div class="alert alert-danger text-center mx-auto shadow-sm border-0 rounded-4 py-4" style="max-width: 600px;">
            <i class="fas fa-exclamation-circle display-4 mb-3 opacity-50"></i>
            <h5 class="fw-bold mb-0">Không tìm thấy đơn hàng nào!</h5>
            <p class="mb-0 mt-2">Vui lòng kiểm tra lại số điện thoại hoặc đặt mua sách mới.</p>
        </div>
    @endif
</div>
@endsection