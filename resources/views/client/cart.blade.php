@extends('client.layout')
@section('title', 'Giỏ hàng của bạn')

@section('custom_css')
<style> 
    .table-cart td { vertical-align: middle; } 
    .btn-checkout {
        transition: all 0.3s ease;
    }
    .btn-checkout:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3) !important;
    }
    .empty-cart-icon {
        font-size: 100px;
        color: #dee2e6;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="fas fa-shopping-cart me-2"></i>Giỏ hàng của bạn</h2>
        <a href="{{ route('client.home') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-1"></i> Tiếp tục mua sắm
        </a>
    </div>

    {{-- Thông báo --}}
    @if(session('success')) 
        <div class="alert alert-success rounded-3 shadow-sm border-0 mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div> 
    @endif
    
    @if(session('error')) 
        <div class="alert alert-danger rounded-3 shadow-sm border-0 mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        </div> 
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @if(session('cart') && count(session('cart')) > 0)
                @php $total = 0; @endphp
                <div class="table-responsive">
                    <table class="table table-hover table-cart mb-0 text-center">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="py-3 px-4 text-start">Sản phẩm</th>
                                <th class="py-3">Giá</th>
                                <th class="py-3" style="width: 150px;">Số lượng</th>
                                <th class="py-3">Thành tiền</th>
                                <th class="py-3">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('cart') as $id => $item)
                                @php $total += $item['price'] * $item['quantity']; @endphp
                                <tr class="border-bottom">
                                    <td class="px-4 py-3 text-start">
                                        <div class="d-flex align-items-center">
                                            @if(isset($item['image']))
                                                <img src="{{ asset($item['image']) }}" class="rounded shadow-sm me-3" style="width: 60px; height: 80px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 80px;">
                                                    <i class="fas fa-book text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark">{{ $item['title'] }}</h6>
                                                <small class="text-muted">{{ $item['author'] ?? 'Tác giả' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-medium">{{ number_format($item['price']) }}đ</td>
                                    <td>
                                        <div class="badge bg-light text-dark border px-3 py-2 fs-6 fw-normal">
                                            {{ $item['quantity'] }}
                                        </div>
                                    </td>
                                    <td class="fw-bold text-primary">{{ number_format($item['price'] * $item['quantity']) }}đ</td>
                                    <td>
                                        <form action="{{ route('cart.remove', $id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-2" onclick="return confirm('Bạn có chắc muốn xóa cuốn sách này khỏi giỏ hàng?')">
                                                <i class="fas fa-trash-alt fs-5"></i>
                                            </button>
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
                                <button type="submit" class="btn btn-light text-danger rounded-pill px-4 border" onclick="return confirm('Toàn bộ giỏ hàng sẽ bị xóa sạch, bạn chắc chứ?')">
                                    <i class="fas fa-times-circle me-1"></i> Xóa sạch giỏ hàng
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h4 class="mb-3 text-dark">Tổng cộng: 
                                <span class="text-danger fw-bold fs-2 ms-2">{{ number_format($total) }}đ</span>
                            </h4>
                            
                            {{-- Nút bấm đã sửa thành thẻ <a> để tránh lỗi POST không mong muốn --}}
                            <a href="{{ route('cart.checkout') }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm fw-bold btn-checkout">
                                TIẾP TỤC THANH TOÁN <i class="fas fa-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-shopping-basket empty-cart-icon"></i>
                    </div>
                    <p class="text-muted fs-4 fw-bold">Ối! Giỏ hàng đang trống.</p>
                    <p class="text-muted mb-4">Có vẻ như bạn chưa chọn được cuốn sách nào ưng ý.</p>
                    <a href="{{ route('client.home') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                        Khám phá kho sách ngay
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection