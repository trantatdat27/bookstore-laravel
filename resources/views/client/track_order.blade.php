@extends('client.layout')
@section('title', 'Lịch sử đơn hàng')

@section('custom_css')
<style>
    .card-order { transition: all 0.3s ease; border: 1px solid #edf2f7; }
    .card-order:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .status-badge { font-size: 0.85rem; padding: 6px 16px; font-weight: 600; }
</style>
@endsection

@section('content')
<div class="container py-5" style="min-height: 70vh;">
    <div class="d-flex align-items-center mb-4">
        <div class="bg-primary text-white rounded-3 p-3 me-3">
            <i class="fas fa-history fs-4"></i>
        </div>
        <div>
            <h2 class="fw-bold mb-0">Lịch sử đơn hàng</h2>
            <p class="text-muted mb-0">Theo dõi trạng thái các đơn hàng bạn đã đặt</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($orders->isEmpty())
        <div class="text-center py-5 bg-white shadow-sm rounded-4 border">
            <i class="fas fa-box-open display-1 text-muted mb-3 opacity-25"></i>
            <h4 class="text-dark fw-bold">Chưa có đơn hàng nào</h4>
            <p class="text-muted mb-4">Hãy khám phá kho sách và chọn cho mình những cuốn sách yêu thích nhé!</p>
            <a href="{{ route('client.home') }}" class="btn btn-primary rounded-pill px-5 shadow-sm">Mua sắm ngay</a>
        </div>
    @else
        <div class="row">
            <div class="col-lg-10 mx-auto">
                @foreach($orders as $order)
                    @php
    $statusData = [
        'pending'   => ['class' => 'bg-warning text-dark', 'label' => 'Chờ xử lý', 'icon' => 'fa-clock'],
        'confirmed' => ['class' => 'bg-primary text-white', 'label' => 'Đã xác nhận', 'icon' => 'fa-check-double'],
        'shipping'  => ['class' => 'bg-info text-white', 'label' => 'Đang giao hàng', 'icon' => 'fa-truck'],
        'completed' => ['class' => 'bg-success text-white', 'label' => 'Đã hoàn thành', 'icon' => 'fa-check-circle'],
        'canceled'  => ['class' => 'bg-danger text-white', 'label' => 'Đã hủy', 'icon' => 'fa-times-circle'],
    ];
    // Lấy status từ DB, nếu không khớp thì mặc định pending
    $currentStatus = $statusData[$order->status] ?? $statusData['pending'];
@endphp

                    <div class="card card-order shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small text-uppercase fw-bold">Mã đơn hàng:</span>
                                <span class="fw-bold text-primary ms-1">#{{ $order->id }}</span>
                                <span class="mx-2 text-silver">|</span>
                                <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i>{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</span>
                            </div>
                            <span class="badge rounded-pill status-badge {{ $currentStatus['class'] }}">
                                <i class="fas {{ $currentStatus['icon'] }} me-1"></i>
                                {{ $currentStatus['label'] }}
                            </span>
                        </div>
                        
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead>
                                        <tr class="text-muted small border-bottom">
                                            <th class="pb-2">Sản phẩm</th>
                                            <th class="pb-2 text-center">Số lượng</th>
                                            <th class="pb-2 text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td class="py-3">
                                                <div class="fw-bold text-dark">{{ $item->book_title }}</div>
                                                <small class="text-muted">{{ number_format($item->price) }}đ</small>
                                            </td>
                                            <td class="py-3 text-center">x{{ $item->quantity }}</td>
                                            <td class="py-3 text-end fw-bold">{{ number_format($item->price * $item->quantity) }}đ</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-light border-0 py-3 px-4">
    <div class="row align-items-center">
        <div class="col-md-7">
            <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> Giao đến: <strong>{{ $order->address }}</strong></small>
        </div>
        <div class="col-md-5 text-md-end">
    {{-- CHỈ hiện nút Hủy nếu trạng thái đơn hàng là 'pending' (Chờ xử lý) --}}
    @if($order->status == 'pending')
        <form action="{{ route('cart.cancel', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 me-2">
                <i class="fas fa-times me-1"></i> Hủy đơn
            </button>
        </form>
    @endif
    
    <span class="text-muted me-2">Tổng thanh toán:</span>
    <span class="fs-4 fw-bold text-danger">{{ number_format($order->total_amount) }}đ</span>
</div>
    </div>
</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection