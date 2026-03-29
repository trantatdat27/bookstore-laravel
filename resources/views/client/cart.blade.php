@extends('client.layout')
@section('title', 'Giỏ hàng của bạn')

@section('custom_css')
<style> .table-cart td { vertical-align: middle; } </style>
@endsection

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="fas fa-shopping-cart me-2"></i>Giỏ hàng của bạn</h2>
        <a href="{{ route('client.home') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-arrow-left me-1"></i> Tiếp tục mua sắm</a>
    </div>

    @if(session('success')) <div class="alert alert-success rounded-3 shadow-sm"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger rounded-3 shadow-sm"><i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}</div> @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @if(count($cart) > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-cart mb-0 text-center">
                        <thead class="table-light text-muted">
                            <tr><th class="text-start ps-4 py-3">Sản phẩm</th><th class="py-3">Giá</th><th class="py-3">Số lượng</th><th class="py-3">Thành tiền</th><th class="py-3">Xóa</th></tr>
                        </thead>
                        <tbody>
                            @php $total = 0 @endphp
                            @foreach($cart as $id => $details)
                                @php $total += $details['price'] * $details['quantity'] @endphp
                                <tr>
                                    <td class="text-start ps-4 py-3">
                                        <div class="fw-bold text-dark fs-6">{{ $details['title'] }}</div>
                                        <small class="text-muted"><i class="fas fa-pen-nib me-1"></i>{{ $details['author'] }}</small>
                                    </td>
                                    <td class="py-3">{{ number_format($details['price']) }}đ</td>
                                    <td class="py-3"><span class="badge bg-light text-dark border px-3 py-2 fs-6 rounded-pill">{{ $details['quantity'] }}</span></td>
                                    <td class="text-danger fw-bold py-3">{{ number_format($details['price'] * $details['quantity']) }}đ</td>
                                    <td class="py-3">
                                        <form action="{{ route('cart.remove', $id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" style="width: 32px; height: 32px; padding: 0;"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-white border-top">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-light text-danger rounded-pill px-4 border"><i class="fas fa-times-circle me-1"></i> Xóa sạch giỏ hàng</button>
                            </form>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h4 class="mb-3 text-dark">Tổng tiền: <span class="text-danger fw-bold fs-3">{{ number_format($total) }}đ</span></h4>
                            <form action="{{ route('cart.checkout') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn đặt hàng không?')">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm fw-bold">THANH TOÁN NGAY <i class="fas fa-arrow-right ms-2"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-basket display-1 text-muted mb-4 opacity-25"></i>
                    <p class="text-muted fs-4 fw-bold">Giỏ hàng của bạn đang trống!</p>
                    <a href="{{ route('client.home') }}" class="btn btn-primary rounded-pill px-5 mt-3 shadow-sm">Mua sắm ngay thôi</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection