@extends('admin.layout')

@section('content')
<div class="min-vh-100 bg-light py-4">
    <div class="container-lg">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="display-4 fw-bold mb-2">⭐ Đánh Giá</h1>
            <p class="text-muted fs-5">Phân tích đánh giá và nhận xét sách</p>
        </div>

        <!-- Key Metrics -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3">
                <div class="card border-warning border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                    <div class="card-body text-dark">
                        <h5 class="card-title fw-semibold mb-3">⭐ Điểm TB</h5>
                        <p class="display-4 fw-bold">{{ number_format($average_rating, 1) }}/5</p>
                        <small class="text-dark">Từ tất cả đánh giá</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-info border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">✅ Phê Duyệt</h5>
                        <p class="display-4 fw-bold">{{ number_format($approval_rate, 1) }}%</p>
                        <small class="text-light">Tỉ lệ phê duyệt</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-danger border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">⏳ Chờ Phê Duyệt</h5>
                        <p class="display-4 fw-bold">{{ $pending_reviews }}</p>
                        <small class="text-light">Cần xem xét</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-success border-2 h-100 shadow-sm" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%);">
                    <div class="card-body text-white">
                        <h5 class="card-title fw-semibold mb-3">📝 Tổng Đánh Giá</h5>
                        <p class="display-4 fw-bold">{{ count($most_reviewed_books ?? []) }}</p>
                        <small class="text-light">Cuốn được đánh giá</small>
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

        <!-- Rating Distribution & Chart -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-warning fw-bold text-dark">
                        📊 Phân Bố Điểm Đánh Giá
                    </div>
                    <div class="card-body">
                        @forelse($rating_distribution as $rating => $count)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-semibold">⭐ {{ $rating }} sao</span>
                                    <span class="badge bg-warning text-dark">{{ $count }} đánh giá</span>
                                </div>
                                <div class="progress" style="height: 28px;">
                                    <div class="progress-bar bg-warning text-dark fw-bold" role="progressbar"
                                         style="width: {{ ($count / ($total_reviews ?: 1)) * 100 }}%">
                                        {{ round(($count / ($total_reviews ?: 1)) * 100, 1) }}%
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">Chưa có dữ liệu</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-info text-white fw-bold">
                        🎯 Biểu Đồ Đánh Giá
                    </div>
                    <div class="card-body">
                        <canvas id="ratingChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Most Reviewed Books -->
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">
                        🏆 Top Sách Được Đánh Giá Nhiều Nhất
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tên Sách</th>
                                        <th class="text-center">Số Đánh Giá</th>
                                        <th class="text-center">Điểm TB</th>
                                        <th>Mức Đánh Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($most_reviewed_books as $index => $book)
                                        <tr>
                                            <td class="fw-bold">{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $book->book_title }}</td>
                                            <td class="text-center"><span class="badge bg-info">{{ $book->review_count }}</span></td>
                                            <td class="text-center">
                                                <span class="fw-bold">⭐ {{ number_format($book->average_rating, 1) }}</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    @php
                                                        $rating = $book->average_rating ?? 0;
                                                        $bgColor = $rating >= 4 ? 'bg-success' : ($rating >= 3 ? 'bg-warning' : 'bg-danger');
                                                    @endphp
                                                    <div class="progress-bar {{ $bgColor }}" role="progressbar"
                                                         style="width: {{ ($rating / 5) * 100 }}%">
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch chart data
    fetch('{{ route("admin.statistics.chart-data") }}')
        .then(response => response.json())
        .then(data => {
            const ratingCtx = document.getElementById('ratingChart');
            if (ratingCtx) {
                new Chart(ratingCtx, {
                    type: 'doughnut',
                    data: {
                        labels: data.rating_distribution.map(item => item.rating + ' sao'),
                        datasets: [{
                            data: data.rating_distribution.map(item => item.count),
                            backgroundColor: ['#ffc107', '#17a2b8', '#198754', '#0d6efd', '#dc3545'],
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
