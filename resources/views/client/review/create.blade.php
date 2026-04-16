@extends('client.layout')
@section('title', 'Đánh giá sách - ' . $book->title)

@section('custom_css')
<style>
    .rating-input { font-size: 2.5rem; cursor: pointer; color: #ddd; }
    .rating-input.active { color: #ffc107; }
    .star { display: inline-block; margin: 0 5px; cursor: pointer; font-size: 2.5rem; color: #ddd; transition: 0.2s; }
    .star:hover, .star.active { color: #ffc107; }
</style>
@endsection

@section('content')
<div class="container py-5">
    <a href="{{ route('client.show', $book->id) }}" class="btn btn-outline-secondary mb-4 rounded-pill px-4">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="bg-white p-4 p-md-5 shadow-sm rounded-4">
                <h2 class="fw-bold mb-4 text-center">
                    <i class="fas fa-star me-2 text-warning"></i>Đánh giá sách
                </h2>

                <div class="alert alert-info rounded-3 mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Sách:</strong> <span class="text-primary fw-bold">{{ $book->title }}</span> - {{ $book->author }}
                </div>

                <form action="{{ route('review.store', $book->id) }}" method="POST">
                    @csrf

                    <!-- Rating -->
                    <div class="mb-4">
                        <label class="form-label fw-bold fs-5">Đánh giá của bạn <span class="text-danger">*</span></label>
                        <div class="mb-3">
                            <div id="rating-stars" class="mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star" data-value="{{ $i }}" onclick="setRating({{ $i }})">
                                        <i class="fas fa-star"></i>
                                    </span>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="rating-value" value="0">
                            <div>
                                <small id="rating-text" class="text-muted">Chọn số sao để đánh giá</small>
                            </div>
                            @error('rating')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Comment -->
                    <div class="mb-4">
                        <label class="form-label fw-bold fs-5">Bình luận của bạn</label>
                        <textarea name="comment" class="form-control rounded-3 @error('comment') is-invalid @enderror" 
                                  rows="5" placeholder="Chia sẻ cảm nghĩ của bạn về sách này...">{{ old('comment') }}</textarea>
                        @error('comment')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2">Tối đa 1000 ký tự</small>
                    </div>

                    <!-- Submit -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg rounded-pill shadow">
                            <i class="fas fa-paper-plane me-2"></i>Gửi đánh giá
                        </button>
                        <a href="{{ route('client.show', $book->id) }}" class="btn btn-secondary btn-lg rounded-pill">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function setRating(value) {
    document.getElementById('rating-value').value = value;
    const stars = document.querySelectorAll('#rating-stars .star');
    const ratingTexts = ['', 'Không tốt', 'Bình thường', 'Tốt', 'Rất tốt', 'Tuyệt vời'];
    
    stars.forEach((star, index) => {
        if (index < value) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
    
    document.getElementById('rating-text').textContent = ratingTexts[value];
}

// Hover effect
document.querySelectorAll('#rating-stars .star').forEach(star => {
    star.addEventListener('mouseover', function() {
        const value = this.getAttribute('data-value');
        document.querySelectorAll('#rating-stars .star').forEach((s, index) => {
            if (index < value) {
                s.style.color = '#ffc107';
            } else {
                s.style.color = '#ddd';
            }
        });
    });
});

document.getElementById('rating-stars').addEventListener('mouseleave', function() {
    const currentValue = document.getElementById('rating-value').value;
    document.querySelectorAll('#rating-stars .star').forEach((star, index) => {
        if (index < currentValue) {
            star.style.color = '#ffc107';
        } else {
            star.style.color = '#ddd';
        }
    });
});
</script>
@endsection
