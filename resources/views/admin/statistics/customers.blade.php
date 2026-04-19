@extends('admin.layout')

@section('content')
<div class="min-vh-100 bg-light py-4">
    <div class="container-lg">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="display-4 fw-bold mb-2">👥 Khách Hàng</h1>
            <p class="text-muted fs-5">Phân tích thông tin và hành vi khách hàng</p>
        </div>

        <!-- Key Metrics -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3">
                <div class="card border-danger border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">👥 Tổng Khách</h5>
                        <p class="display-4 fw-bold">{{ $total_customers }}</p>
                        <small class="text-light">Khách hàng đã đăng ký</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-success border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">🆕 Khách Mới</h5>
                        <p class="display-4 fw-bold">{{ $new_customers }}</p>
                        <small class="text-light">Tháng này</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-info border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">🔄 Tỷ Lệ Tái Mua</h5>
                        <p class="display-4 fw-bold">{{ number_format($repeat_purchase_rate, 1) }}%</p>
                        <small class="text-light">Khách quay lại</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-warning border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #fd7e14 0%, #d57305 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">💰 Giá Trị TB</h5>
                        <p class="display-4 fw-bold">{{ number_format($avg_clv, 0, ',', '.') }}</p>
                        <small class="text-light">Tổng chi bình quân</small>
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

        <!-- Top 10 Customers -->
        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-danger text-white fw-bold">
                        👑 Top 10 Khách Hàng
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tên Khách Hàng</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-end">Tổng Chi</th>
                                        <th class="text-center">Đơn Hàng</th>
                                        <th class="text-center">Đánh Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($top_customers as $index => $customer)
                                        <tr>
                                            <td class="fw-bold">
                                                <span class="badge bg-danger rounded-circle" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td class="fw-semibold">{{ $customer->customer_name }}</td>
                                            <td class="text-muted">{{ $customer->email ?? 'N/A' }}</td>
                                            <td class="text-end fw-bold text-danger">{{ number_format($customer->total_spent, 0, ',', '.') }} ₫</td>
                                            <td class="text-center"><span class="badge bg-info">{{ $customer->order_count }}</span></td>
                                            <td class="text-center"><span class="badge bg-warning">{{ $customer->review_count ?? 0 }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Insights -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white fw-bold">
                        📊 Thống Kê Khách Hàng
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Khách mua 1 lần</span>
                                @php
                                    $one_time = $total_customers - count($top_customers);
                                @endphp
                                <span class="badge bg-secondary">{{ $one_time }}</span>
                            </div>
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar bg-secondary" role="progressbar"
                                     style="width: {{ ($one_time / ($total_customers ?: 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Khách tái mua</span>
                                <span class="badge bg-success">{{ count($top_customers) }}</span>
                            </div>
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ (count($top_customers) / ($total_customers ?: 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-info text-white fw-bold">
                        💡 Insights
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-2">
                            <strong>📈 Tăng trưởng</strong><br>
                            <small>{{ $new_customers_this_month }} khách hàng mới tháng này</small>
                        </div>
                        <div class="alert alert-warning mb-2">
                            <strong>🎯 Tái mua</strong><br>
                            <small>{{ number_format($repeat_purchase_rate, 1) }}% khách hàng quay lại mua</small>
                        </div>
                        <div class="alert alert-success mb-0">
                            <strong>💰 Giá trị</strong><br>
                            <small>Giá trị bình quân khách hàng là {{ number_format($average_customer_ltv, 0, ',', '.') }} ₫</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
