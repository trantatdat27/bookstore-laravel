@extends('admin.layout')
@section('title', 'Sửa sách: ' . $book->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i>Chỉnh sửa sách</h3>
    <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary fw-bold">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">Tên sách</label>
                    <input 
                        type="text" 
                        name="title" 
                        id="bookTitle"
                        value="{{ $book->title }}" 
                        class="form-control" 
                        required
                        autocomplete="off"
                    >
                    <small class="text-danger" id="titleError" style="display: none;"></small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">Tác giả</label>
                    <input 
                        type="text" 
                        name="author" 
                        id="bookAuthor"
                        value="{{ $book->author }}" 
                        class="form-control" 
                        required
                        autocomplete="off"
                    >
                    <small class="text-danger" id="authorError" style="display: none;"></small>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted">Giá bán (VNĐ)</label>
                    <div class="input-group">
                        <input type="number" name="price" value="{{ $book->price }}" class="form-control" required>
                        <span class="input-group-text fw-bold">đ</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted">Số lượng kho</label>
                    <input type="number" name="stock" value="{{ $book->stock }}" class="form-control" required min="0">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted">Danh mục</label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $book->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-muted d-block">Ảnh sản phẩm</label>
                @if($book->image)
                    <div class="mb-3">
                        <img src="{{ asset($book->image) }}" class="img-thumbnail rounded shadow-sm" style="height: 150px; object-fit: cover;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control">
                <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle me-1"></i>Để trống nếu không muốn thay đổi ảnh.</small>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-muted">Mô tả nội dung</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Nhập nội dung mô tả sách...">{{ $book->description }}</textarea>
            </div>

            <hr class="my-4">
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold shadow-sm" id="submitBtn">
                    <i class="fas fa-save me-2"></i> Cập nhật Sách
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookTitleInput = document.getElementById('bookTitle');
    const bookAuthorInput = document.getElementById('bookAuthor');
    const titleError = document.getElementById('titleError');
    const authorError = document.getElementById('authorError');
    const form = document.querySelector('form');
    
    // Hàm validation cho nhập liệu
    function validateInput(input, errorElement) {
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            // Loại bỏ ký tự đặc biệt: @, #, $, %, ^, &, *, (, ), +, =, [, ], {, }, ;, :, ", ', <, >, ,, ?, /, \, |, `, ~, !
            const cleanValue = value.replace(/[@#$%^&*()+\=\[\]{};:"'<>,?\/\\|`~!]/g, '');
            
            if (value !== cleanValue) {
                e.target.value = cleanValue;
                showErrorMessage(errorElement, 'Ký tự đặc biệt không được phép!');
                setTimeout(() => errorElement.style.display = 'none', 3000);
            }
        });
    }
    
    // Hàm xác thực khi submit
    function addSubmitValidation(input, errorElement, fieldName) {
        form.addEventListener('submit', function(e) {
            const value = input.value.trim();
            
            // Kiểm tra chứa ký tự đặc biệt
            if (/@|#|\$|%|\^|&|\*|\(|\)|\+|=|\[|\]|{|}|;|:|"|'|<|>|,|\?|\/|\\|\||`|~|!/.test(value)) {
                e.preventDefault();
                showErrorMessage(errorElement, fieldName + ' không được chứa ký tự đặc biệt như: @, #, $, %, &, *, /, v.v.');
                return false;
            }
            
            // Kiểm tra không để trống
            if (value === '') {
                e.preventDefault();
                showErrorMessage(errorElement, fieldName + ' không được để trống!');
                return false;
            }
        });
    }
    
    // Áp dụng validation cho cả hai input
    if (bookTitleInput) {
        validateInput(bookTitleInput, titleError);
        addSubmitValidation(bookTitleInput, titleError, 'Tên sách');
    }
    
    if (bookAuthorInput) {
        validateInput(bookAuthorInput, authorError);
        addSubmitValidation(bookAuthorInput, authorError, 'Tên tác giả');
    }
});

function showErrorMessage(element, message) {
    element.textContent = message;
    element.style.display = 'block';
    element.style.marginTop = '5px';
}
</script>