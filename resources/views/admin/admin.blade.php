@extends('admin.layout')
@section('title', 'Quản lý Sách')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark"><i class="fas fa-book-open text-primary me-2"></i>Quản lý sách</h3>
</div>

@if(session('success'))
    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger shadow-sm">
        <strong>Lỗi rồi, không thêm được vì:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card mb-4 border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-bottom-0">
        <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle text-success me-2"></i>Thêm sách mới</h5>
    </div>
    <div class="card-body bg-light rounded-bottom-4">
        <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" id="addBookForm">
            @csrf
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted">Tên sách <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        name="title" 
                        id="addBookTitle"
                        placeholder="Nhập tên sách..." 
                        class="form-control" 
                        required
                        autocomplete="off"
                    >
                    <small class="text-danger" id="addTitleError" style="display: none;"></small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted">Tác giả <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        name="author" 
                        id="addBookAuthor"
                        placeholder="Nhập tên tác giả..." 
                        class="form-control" 
                        required
                        autocomplete="off"
                    >
                    <small class="text-danger" id="addAuthorError" style="display: none;"></small>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="price" placeholder="VD: 50000" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Kho <span class="text-danger">*</span></label>
                    <input type="number" name="stock" placeholder="Số lượng" class="form-control" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Danh mục <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-muted">Ảnh bìa sách</label>
                    <input type="file" name="image" class="form-control">
                </div>
            </div>

            <div class="text-end mt-2">
                <button type="submit" class="btn btn-success px-5 fw-bold"><i class="fas fa-save me-1"></i> Lưu Sách Mới</button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4 shadow-sm border-0 rounded-4">
    <div class="card-body p-3 bg-white">
        <form action="{{ route('admin.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="🔍 Tìm theo tên sách hoặc tác giả...">
            </div>
            <div class="col-md-4">
                <select name="category_id" class="form-select">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Tìm kiếm</button>
                <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary text-nowrap">Làm mới</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light table-light">
                    <tr>
                        <th style="width: 5%;" class="text-center">ID</th>
                        <th style="width: 10%;" class="text-center">Ảnh</th>
                        <th style="width: 25%;">Tên sách</th>
                        <th style="width: 15%;">Tác giả</th>
                        <th style="width: 15%;">Danh mục</th>
                        <th style="width: 10%;">Giá</th>
                        <th style="width: 5%;" class="text-center">Kho</th>
                        <th style="width: 15%;" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                    <tr>
                        <td class="text-center text-muted">{{ $book->id }}</td>
                        <td class="text-center">
                            @if($book->image)
                                <img src="{{ asset($book->image) }}" class="rounded shadow-sm" style="width: 50px; height: 70px; object-fit: cover;">
                            @else
                                <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 70px; font-size: 10px;">No Image</div>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $book->title }}</td>
                        <td class="text-muted">{{ $book->author }}</td>
                        <td><span class="badge bg-secondary">{{ $book->category->name ?? 'N/A' }}</span></td>
                        <td class="text-danger fw-bold">{{ number_format($book->price) }}đ</td>
                        <td class="text-center">
                            <span class="badge {{ $book->stock <= 5 ? 'bg-danger' : 'bg-info text-dark' }} rounded-pill px-3 py-2">
                                {{ $book->stock }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('books.edit', $book->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                Sửa
                            </a>
                            <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa cuốn sách này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        
        @if(method_exists($books, 'links'))
            <div class="d-flex justify-content-center mt-4 mb-3">
                {{ $books->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
        </div>
    </div>
    @if($books->hasPages())
        <div class="card-footer bg-white border-top-0 d-flex justify-content-end py-3">
            {{ $books->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== VALIDATION FORM THÊM SÁCH =====
    const addBookForm = document.getElementById('addBookForm');
    const addBookTitle = document.getElementById('addBookTitle');
    const addBookAuthor = document.getElementById('addBookAuthor');
    const addTitleError = document.getElementById('addTitleError');
    const addAuthorError = document.getElementById('addAuthorError');
    
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
        addBookForm.addEventListener('submit', function(e) {
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
    if (addBookTitle) {
        validateInput(addBookTitle, addTitleError);
        addSubmitValidation(addBookTitle, addTitleError, 'Tên sách');
    }
    
    if (addBookAuthor) {
        validateInput(addBookAuthor, addAuthorError);
        addSubmitValidation(addBookAuthor, addAuthorError, 'Tên tác giả');
    }
});

function showErrorMessage(element, message) {
    element.textContent = message;
    element.style.display = 'block';
    element.style.marginTop = '5px';
}
</script>