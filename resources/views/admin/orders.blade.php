@extends('admin.layout')
@section('title', 'Quản lý Đơn hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark"><i class="fas fa-shopping-cart text-primary me-2"></i>Danh sách Đơn hàng</h3>
</div>

@foreach($orders as $order)
<div class="card mb-4 shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0 text-primary fw-bold">
        <i class="fas fa-hashtag me-1"></i> Đơn hàng #{{ $order->id }} - {{ $order->customer_name }}
    </h5>
    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Xóa vĩnh viễn đơn hàng này?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger border-0">
            <i class="fas fa-trash"></i> Xóa đơn
        </button>
    </form>
</div>
    <div class="card-body p-4">
        <div class="row mb-4 bg-light p-3 rounded-3 mx-0">
            <div class="col-md-6 border-end">
                <p class="mb-2"><strong class="text-dark"><i class="fas fa-phone-alt text-muted me-2"></i>SĐT:</strong> {{ $order->phone }}</p>
            </div>
            <div class="col-md-6 ps-md-4">
                <p class="mb-2"><strong class="text-dark"><i class="fas fa-map-marker-alt text-muted me-2"></i>Địa chỉ:</strong> {{ $order->address }}</p>
            </div>
        </div>

        <h6 class="fw-bold mb-3"><i class="fas fa-list text-muted me-2"></i>Chi tiết sản phẩm:</h6>
        <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tên sách</th>
                        <th class="text-center" style="width: 100px;">Số lượng</th>
                        <th class="text-end" style="width: 150px;">Đơn giá</th>
                        <th class="text-end" style="width: 150px;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td class="fw-bold text-dark">{{ $item->book_title }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end text-muted">{{ number_format($item->price) }}đ</td>
                        <td class="text-end fw-bold text-danger">{{ number_format($item->price * $item->quantity) }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-end align-items-md-center mt-4 border-top pt-3">
            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="d-flex align-items-center gap-2 mb-3 mb-md-0 w-100 w-md-auto">
                @csrf
                <label class="fw-bold text-muted text-nowrap">Trạng thái:</label>
                <select name="status" class="form-select fw-bold 
    {{ $order->status == 'pending' ? 'text-warning' : '' }}
    {{ $order->status == 'confirmed' ? 'text-primary' : '' }} 
    {{ $order->status == 'shipping' ? 'text-info' : '' }}
    {{ $order->status == 'completed' ? 'text-success' : '' }}
    {{ $order->status == 'canceled' ? 'text-danger' : '' }}
" style="min-width: 160px;">
    {{-- 1. Trạng thái Chờ xử lý --}}
    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ Chờ xử lý</option>
    
    {{-- 2. Trạng thái Đã xác nhận  --}}
    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>🔵 Đã xác nhận</option>
    
    {{-- 3. Trạng thái Đang giao --}}
    <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>🚚 Đang giao</option>
    
    {{-- 4. Trạng thái Hoàn thành --}}
    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>✅ Hoàn thành</option>
    
    {{-- 5. Trạng thái Hủy đơn --}}
    <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>❌ Hủy đơn</option>
</select>
                <button type="submit" class="btn btn-dark fw-bold text-nowrap"><i class="fas fa-sync-alt me-1"></i> Cập nhật</button>
            </form>
            
            <div class="text-end w-100 w-md-auto">
                <span class="text-muted d-block mb-1">Tổng cộng:</span>
                <span class="text-danger fw-bold fs-4">{{ number_format($order->total_amount) }} VNĐ</span>
            </div>
        </div>
    </div>
</div>
@endforeach

@if(count($orders) == 0)
    <div class="alert alert-info text-center py-5 border-0 shadow-sm rounded-4 bg-white">
        <i class="fas fa-box-open display-1 text-muted mb-3 opacity-25 d-block"></i>
        <h4 class="text-muted">Chưa có đơn hàng nào</h4>
    </div>
@endif
@endsection