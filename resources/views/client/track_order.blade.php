<!DOCTYPE html>
<html>
<head>
    <title>Tra cứu đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="mb-4">
            <a href="{{ route('client.home') }}" class="btn btn-outline-secondary">← Quay lại cửa hàng</a>
        </div>
        
        <h2 class="text-center mb-4">Lịch sử đơn hàng</h2>
        
        <form action="{{ route('cart.track') }}" method="GET" class="mx-auto mb-5" style="max-width: 500px;">
            <div class="input-group shadow-sm border rounded">
                <input type="text" name="phone" class="form-control border-0" placeholder="Nhập số điện thoại đã đặt hàng..." value="{{ request('phone') }}" required>
                <button class="btn btn-primary px-4" type="submit">Tìm kiếm</button>
            </div>
        </form>

        {{-- Kiểm tra nếu có danh sách đơn hàng --}}
        @if(isset($orders) && $orders->count() > 0)
            <div class="mx-auto" style="max-width: 700px;">
                <p class="text-muted mb-3">Tìm thấy <strong>{{ $orders->count() }}</strong> đơn hàng cho số điện thoại: {{ request('phone') }}</p>
                
                @foreach($orders as $order)
                    <div class="card shadow-sm mb-4 border-0">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <span>Mã đơn: <strong>#{{ $order->id }}</strong></span>
                            <span class="badge @if($order->status == 'pending') bg-warning text-dark @elseif($order->status == 'completed') bg-success @else bg-info @endif">
                                {{ strtoupper($order->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Khách hàng:</small>
                                    <strong>{{ $order->customer_name }}</strong>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <small class="text-muted d-block">Ngày đặt:</small>
                                    <span>{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</span>
                                </div>
                            </div>
                            
                            <table class="table table-sm border-top">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end">Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>{{ $item->book_title }}</td>
                                            <td class="text-center">x{{ $item->quantity }}</td>
                                            <td class="text-end">{{ number_format($item->price) }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="text-muted small">Địa chỉ: {{ $order->address }}</span>
                                <h5 class="text-danger mb-0">Tổng: {{ number_format($order->total_amount) }} VNĐ</h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @elseif(request()->has('phone'))
            <div class="alert alert-danger text-center mx-auto shadow-sm" style="max-width: 500px;">
                Không tìm thấy lịch sử đơn hàng nào với số điện thoại này!
            </div>
        @endif
    </div>
</body>
</html>