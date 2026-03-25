<!DOCTYPE html>
<html>
<head>
    <title>Admin - Quản lý đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Bookstore Admin</a>
        <div class="navbar-nav">
            <a class="nav-link" href="{{ route('admin.index') }}">Sách</a>
            <a class="nav-link active" href="{{ route('admin.orders') }}">Đơn hàng</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h2 class="mb-4">Danh sách Đơn hàng</h2>
    
    @foreach($orders as $order)
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="text-primary mb-3">Đơn hàng #{{ $order->id }} - Khách hàng: {{ $order->customer_name }}</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>📞 SĐT:</strong> {{ $order->phone }}</p>
                    <p class="mb-1"><strong>🏠 Địa chỉ:</strong> {{ $order->address }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>💳 Thanh toán:</strong> {{ $order->payment_method }}</p>
                    <p class="mb-1"><strong>🔔 Trạng thái:</strong> 
                        <span class="badge @if($order->status == 'pending') bg-warning @elseif($order->status == 'shipping') bg-primary @else bg-success @endif">
                            {{ $order->status }}
                        </span>
                    </p>
                </div>
            </div>

            <table class="table table-bordered bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->book_title }}</td>
                        <td>{{ number_format($item->price) }}đ</td>
                        <td>x{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price * $item->quantity) }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <select name="status" class="form-select form-select-sm" style="width: 150px;">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>Hủy đơn</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-dark">Cập nhật trạng thái</button>
                </form>
                <div class="text-danger fw-bold fs-5">
                    Tổng cộng: {{ number_format($order->total_amount) }} VNĐ
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if(count($orders) == 0)
        <div class="alert alert-info text-center">Chưa có đơn hàng nào được đặt.</div>
    @endif
</div>
</body>
</html>