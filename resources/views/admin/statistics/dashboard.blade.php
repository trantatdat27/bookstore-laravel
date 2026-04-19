@extends('admin.layout')

@section('content')
<div class="min-vh-100 bg-light py-4">
    <div class="container-lg">
        <!-- Header -->
        <div class="mb-5 pb-3 border-bottom">
            <div class="d-flex justify-content-between align-items-start gap-4 mb-0">
                <div>
                    <h1 class="display-6 fw-bold mb-2">📊 Bảng Thống Kê</h1>
                    <p class="text-dark fs-6 mb-0">Tổng quan chi tiết về kinh doanh của bạn</p>
                </div>
                
                <!-- Period Filter -->
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.statistics', ['period' => 'today']) }}" 
                       class="btn btn-sm {{ $period === 'today' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">
                        Hôm nay
                    </a>
                    <a href="{{ route('admin.statistics', ['period' => 'week']) }}" 
                       class="btn btn-sm {{ $period === 'week' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">
                        Tuần
                    </a>
                    <a href="{{ route('admin.statistics', ['period' => 'month']) }}" 
                       class="btn btn-sm {{ $period === 'month' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">
                        Tháng
                    </a>
                    <a href="{{ route('admin.statistics', ['period' => 'year']) }}" 
                       class="btn btn-sm {{ $period === 'year' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">
                        Năm
                    </a>
                    <a href="{{ route('admin.statistics', ['period' => 'all']) }}" 
                       class="btn btn-sm {{ $period === 'all' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">
                        Tất cả
                    </a>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Revenue -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-primary border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">💰 Tổng Doanh Thu</h5>
                        <p class="h4 fw-bold mb-2">{{ number_format($overview['total_revenue'], 0, ',', '.') }}</p>
                        <small class="text-light">đồng</small>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-success border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">📦 Tổng Đơn Hàng</h5>
                        <p class="h4 fw-bold mb-2">{{ $overview['total_orders'] }}</p>
                        <small class="text-warning fw-semibold">⚠️ {{ $overview['pending_orders'] }} đang chờ</small>
                    </div>
                </div>
            </div>

            <!-- Average Order Value -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-info border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">💳 Giá Trị TB/Đơn</h5>
                        <p class="h4 fw-bold mb-2">{{ number_format($overview['average_order_value'], 0, ',', '.') }}</p>
                        <small class="text-light">đồng</small>
                    </div>
                </div>
            </div>

            <!-- Book Stock -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-secondary border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">📚 Kho Sách</h5>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light bg-opacity-10 rounded mb-2">
                                <span class="fw-medium">Trong kho</span>
                                <span class="fs-5 fw-bold">{{ $overview['total_books_in_stock'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light bg-opacity-10 rounded">
                                <span class="fw-medium">Đã bán</span>
                                <span class="fs-5 fw-bold">{{ $overview['total_books_sold'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Books & Sales by Category -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white fw-bold">
                        🏆 Top 10 Sách Bán Chạy
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($top_books as $index => $item)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-primary rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="fw-semibold">{{ $item->book_title ?? 'N/A' }}</span>
                                    </div>
                                    <span class="badge bg-info">{{ $item->total_sold }}</span>
                                </div>
                            @empty
                                <p class="text-muted text-center py-4">Chưa có dữ liệu</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-success text-white fw-bold">
                        📂 Doanh Thu Theo Danh Mục
                    </div>
                    <div class="card-body">
                        @forelse($sales_by_category as $category)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-semibold">{{ $category['name'] }}</span>
                                    <span class="badge bg-success">{{ $category['total_sold'] }} sách</span>
                                </div>
                                <div class="progress" style="height: 24px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ ($category['total_sold'] / ($sales_by_category->max('total_sold') ?: 1)) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">Chưa có dữ liệu</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews & Inventory Summary -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-warning fw-bold">
                        ⭐ Đánh Giá
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Điểm trung bình</span>
                                <span class="fs-3 fw-bold">{{ number_format($average_rating, 1) }}/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-danger text-white fw-bold">
                        ⚠️ Cảnh Báo Kho
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger mb-2">
                            <strong>{{ $out_of_stock_count }}</strong> sách hết hàng
                        </div>
                        <div class="alert alert-warning mb-0">
                            <strong>{{ $low_stock->count() }}</strong> sách sắp hết (< 5)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-danger text-white fw-bold">
                👑 Top 5 Khách Hàng
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên Khách Hàng</th>
                                <th class="text-end">Tổng Chi</th>
                                <th class="text-end">Đơn Hàng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($top_customers as $index => $customer)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge bg-danger rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="fw-semibold">{{ $customer->customer_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        {{ number_format($customer->total_spent, 0, ',', '.') }} ₫
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-info">{{ $customer->order_count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Chưa có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
