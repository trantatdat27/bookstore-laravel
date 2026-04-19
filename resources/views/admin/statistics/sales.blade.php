@extends('admin.layout')

@section('content')
<div class="min-vh-100 bg-light py-4">
    <div class="container-lg">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="display-4 fw-bold mb-2">📈 Bán Hàng</h1>
            <p class="text-muted fs-5">Phân tích doanh số và trend bán hàng</p>
        </div>

        <!-- Key Metrics -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-success border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">💰 Đơn Hàng Gần Đây</h5>
                        <p class="display-4 fw-bold">{{ count($revenue_data ?? []) }}</p>
                        <small class="text-light">bản ghi</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-info border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #fd7e14 0%, #d57305 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">📊 Danh Mục</h5>
                        <p class="display-4 fw-bold">{{ count($sales_by_category ?? []) }}</p>
                        <small class="text-light">danh mục bán hàng</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs nav-fill bg-white shadow-sm rounded-3 mb-4" style="border: none;">
            <li class="nav-item">
                <a class="nav-link fw-bold" href="{{ route('admin.statistics') }}">
                    📊 Tổng Quan
                </a>
            </li>
        </ul>

        <!-- Revenue Chart -->
        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white fw-bold">
                        📈 Trendline Doanh Thu
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status & Top Books -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-info text-white fw-bold">
                        📊 Trạng Thái Đơn Hàng
                    </div>
                    <div class="card-body">
                        <canvas id="orderStatusChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-warning text-dark fw-bold">
                        🏆 Top 10 Sách Bán Chạy
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <div class="list-group list-group-flush">
                            @forelse($top_books as $index => $item)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-warning text-dark rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="fw-semibold">{{ $item->book_title ?? 'N/A' }}</span>
                                    </div>
                                    <span class="badge bg-success">{{ $item->total_sold }}</span>
                                </div>
                            @empty
                                <p class="text-muted text-center py-4">Chưa có dữ liệu</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales by Category -->
        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">
                        📂 Doanh Thu Theo Danh Mục
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Danh Mục</th>
                                        <th class="text-end">Số Sách Bán</th>
                                        <th>Biểu Đồ</th>
                                        <th class="text-end">Phần Trăm</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sales_by_category as $category)
                                        <tr>
                                            <td class="fw-semibold">{{ $category['name'] }}</td>
                                            <td class="text-end fw-bold">{{ $category['total_sold'] }}</td>
                                            <td>
                                                <div class="progress" style="height: 24px; min-width: 150px;">
                                                    <div class="progress-bar bg-primary" role="progressbar"
                                                         style="width: {{ ($category['total_sold'] / ($sales_by_category->max('total_sold') ?: 1)) * 100 }}%">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold">{{ round(($category['total_sold'] / $total_books_sold) * 100, 1) }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch chart data
    fetch('{{ route("admin.statistics.chart-data") }}')
        .then(response => response.json())
        .then(data => {
            // Revenue Trend Chart
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: data.revenue_trend.map(item => item.date),
                        datasets: [{
                            label: 'Doanh Thu',
                            data: data.revenue_trend.map(item => item.revenue),
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // Order Status Chart
            const statusCtx = document.getElementById('orderStatusChart');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: data.order_status.map(item => item.status),
                        datasets: [{
                            data: data.order_status.map(item => item.count),
                            backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545'],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        })
        .catch(error => console.error('Error loading chart data:', error));
});
</script>
@endsection
