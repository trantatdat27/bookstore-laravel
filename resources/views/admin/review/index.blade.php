@extends('admin.layout')
@section('title', 'Quản lý đánh giá/bình luận')

@section('custom_css')
<style>
    .review-card { background: #f8f9fa; border-left: 4px solid #0d6efd; padding: 15px; margin-bottom: 12px; border-radius: 6px; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d4edda; color: #155724; }
    .status-rejected { background: #f8d7da; color: #721c24; }
    .rating-stars { color: #ffc107; font-size: 0.95rem; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fas fa-star me-2"></i>Quản lý đánh giá/bình luận</h2>
        <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white p-3 shadow-sm rounded-3">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Lọc theo trạng thái</label>
                        <select name="status" class="form-select rounded-2">
                            <option value="">-- Tất cả --</option>
                            <option value="pending" @selected(request('status') === 'pending')>Chờ duyệt</option>
                            <option value="approved" @selected(request('status') === 'approved')>Đã duyệt</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>Bị từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Lọc theo sách</label>
                        <select name="book_id" class="form-select rounded-2">
                            <option value="">-- Tất cả sách --</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}" @selected(request('book_id') == $book->id)>{{ $book->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary rounded-pill">
                            <i class="fas fa-filter me-2"></i>Lọc
                        </button>
                        <a href="{{ route('admin.reviews') }}" class="btn btn-secondary rounded-pill">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="row">
        <div class="col-12">
            @if($reviews->isNotEmpty())
                <div class="bg-white shadow-sm rounded-3 overflow-hidden">
                    @foreach($reviews as $review)
                        <div class="review-card">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div>
                                        <strong class="text-dark fs-5">{{ $review->book->title }}</strong>
                                        <br>
                                        <small class="text-muted">Người đánh giá: <strong>{{ $review->user->name }}</strong> ({{ $review->user->email }})</small>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="rating-stars mb-2">
                                        @for($i = 0; $i < $review->rating; $i++)
                                            <i class="fas fa-star"></i>
                                        @endfor
                                        @for($i = $review->rating; $i < 5; $i++)
                                            <i class="far fa-star"></i>
                                        @endfor
                                        <span class="ms-2 text-dark fw-bold">({{ $review->rating }}/5)</span>
                                    </div>
                                </div>
                            </div>

                            @if($review->comment)
                                <div class="mb-2 p-2 bg-white border border-secondary rounded bg-opacity-50">
                                    <p class="mb-0 text-dark">{{ $review->comment }}</p>
                                </div>
                            @endif

                            <div class="row mt-3 align-items-center">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-calendar-alt me-2"></i>{{ $review->created_at->format('d/m/Y H:i') }}
                                    </small>
                                    <span class="badge rounded-pill status-{{ $review->status }} mt-2">
                                        @if($review->status === 'pending')
                                            <i class="fas fa-hourglass-half me-1"></i>Chờ duyệt
                                        @elseif($review->status === 'approved')
                                            <i class="fas fa-check-circle me-1"></i>Đã duyệt
                                        @else
                                            <i class="fas fa-times-circle me-1"></i>Bị từ chối
                                        @endif
                                    </span>
                                </div>
                                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                    @if($review->status !== 'approved')
                                        <form action="{{ route('review.approve', $review->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill" title="Duyệt">
                                                <i class="fas fa-check me-1"></i>Duyệt
                                            </button>
                                        </form>
                                    @endif

                                    @if($review->status !== 'rejected')
                                        <form action="{{ route('review.reject', $review->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-warning rounded-pill" title="Từ chối">
                                                <i class="fas fa-ban me-1"></i>Từ chối
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('review.admin.destroy', $review->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill" title="Xóa" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                            <i class="fas fa-trash me-1"></i>Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="alert alert-info rounded-3 text-center p-5 shadow-sm">
                    <i class="fas fa-info-circle display-4 mb-3 d-block opacity-50"></i>
                    <h5>Không có đánh giá nào</h5>
                    <p>Hiện tại chưa có đánh giá/bình luận nào từ khách hàng.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
