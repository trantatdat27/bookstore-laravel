@extends('admin.layout')

@section('content')
<div class="min-vh-100 bg-light py-4">
    <div class="container-lg">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="display-4 fw-bold mb-2">📦 Kho Hàng</h1>
            <p class="text-muted fs-5">Quản lý tồn kho và phân tích cảnh báo</p>
        </div>

        <!-- Key Metrics -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3">
                <div class="card border-primary border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">📚 Tổng Trong Kho</h5>
                        <p class="display-4 fw-bold">{{ $total_stock }}</p>
                        <small class="text-light">Quyển sách</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-danger border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">🚨 Hết Hàng</h5>
                        <p class="display-4 fw-bold">{{ $out_of_stock_count }}</p>
                        <small class="text-light">Loại sách</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-warning border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                    <div class="card-body text-dark">
                        <h5 class="card-title fw-semibold mb-3">⚠️ Sắp Hết</h5>
                        <p class="display-4 fw-bold">{{ $low_stock_books->count() }}</p>
                        <small class="text-dark">Loại sách (< 5)</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-success border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">🔄 Không Bán</h5>
                        <p class="display-4 fw-bold">{{ $dead_stock->count() }}</p>
                        <small class="text-light">Loại sách không bán</small>
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

        <!-- Low Stock Alerts -->
        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning fw-bold text-dark">
                        ⚠️ Cảnh Báo - Sách Sắp Hết (Tồn < 5)
                    </div>
                    <div class="card-body">
                        @if($low_stock_count > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tên Sách</th>
                                            <th class="text-center">Danh Mục</th>
                                            <th class="text-end">Tồn Kho</th>
                                            <th class="text-center">Mức Cảnh Báo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($low_stock as $book)
                                            <tr class="table-warning">
                                                <td class="fw-semibold">{{ $book->title }}</td>
                                                <td class="text-center"><span class="badge bg-info">{{ $book->category_name ?? 'N/A' }}</span></td>
                                                <td class="text-end fw-bold">{{ $book->stock }}</td>
                                                <td class="text-center">
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-warning" role="progressbar"
                                                             style="width: {{ ($book->stock / 5) * 100 }}%">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Không có cảnh báo</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success mb-0">
                                ✅ Tất cả sách đều có tồn kho đủ
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Out of Stock Alerts -->
        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-danger text-white fw-bold">
                        🚨 Cảnh Báo - Hết Hàng
                    </div>
                    <div class="card-body">
                        @if($out_of_stock_count > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tên Sách</th>
                                            <th class="text-center">Danh Mục</th>
                                            <th class="text-end">Tồn Kho</th>
                                            <th class="text-center">Trạng Thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($out_of_stock as $book)
                                            <tr class="table-danger">
                                                <td class="fw-semibold">{{ $book->title }}</td>
                                                <td class="text-center"><span class="badge bg-secondary">{{ $book->category_name ?? 'N/A' }}</span></td>
                                                <td class="text-end fw-bold">0</td>
                                                <td class="text-center">
                                                    <span class="badge bg-danger">Hết hàng</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Không có sách hết hàng</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success mb-0">
                                ✅ Không có sách hết hàng
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Dead Stock Analysis -->
        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white fw-bold">
                        🔄 Sách Không Bán (Hàng Chết)
                    </div>
                    <div class="card-body">
                        @if($dead_stock_count > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tên Sách</th>
                                            <th class="text-center">Danh Mục</th>
                                            <th class="text-end">Tồn Kho</th>
                                            <th class="text-center">Đánh Giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($dead_stock as $book)
                                            <tr class="table-secondary">
                                                <td class="fw-semibold">{{ $book->title }}</td>
                                                <td class="text-center"><span class="badge bg-secondary">{{ $book->category_name ?? 'N/A' }}</span></td>
                                                <td class="text-end fw-bold">{{ $book->stock }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning">⭐ {{ number_format($book->average_rating ?? 0, 1) }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Không có hàng chết</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-success mb-0">
                                ✅ Không có hàng chết (tất cả sách đều có lượt bán)
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Turnover by Category -->
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white fw-bold">
                        📊 Vòng Quay Hàng Theo Danh Mục
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Danh Mục</th>
                                        <th class="text-center">Số Loại Sách</th>
                                        <th class="text-end">Tồn Kho</th>
                                        <th class="text-end">Đã Bán</th>
                                        <th class="text-center">Vòng Quay</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stock_turnover as $category)
                                        <tr>
                                            <td class="fw-semibold">{{ $category['name'] }}</td>
                                            <td class="text-center"><span class="badge bg-primary">{{ $category['total_books'] }}</span></td>
                                            <td class="text-end fw-bold">{{ $category['total_stock'] }}</td>
                                            <td class="text-end fw-bold">{{ $category['total_sold'] }}</td>
                                            <td class="text-center">
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                         style="width: {{ min(($category['total_sold'] / ($category['total_sold'] + $category['total_stock'] ?: 1)) * 100, 100) }}%">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Chưa có dữ liệu</td>
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
@endsection
