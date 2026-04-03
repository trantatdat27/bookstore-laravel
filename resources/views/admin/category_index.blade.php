@extends('admin.layout')
@section('title', 'Quản lý Danh mục')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark"><i class="fas fa-tags text-primary me-2"></i>Quản lý Danh mục</h3>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-primary" style="border-top: 4px solid #0d6efd;">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-plus me-2 text-primary"></i>Thêm danh mục mới</h5>
            </div>
            <div class="card-body pt-0">
                <form action="{{ route('categories.store') }}" method="POST" id="addCategoryForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Tên danh mục</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="categoryNameInput"
                            class="form-control" 
                            placeholder="VD: Sách giáo khoa..." 
                            required
                            autocomplete="off"
                        >
                        <small class="text-danger" id="categoryError" style="display: none;"></small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="fas fa-check me-2"></i>Thêm Ngay</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-dark"></i>Danh sách hiện có</h5>
        </div>
        <div class="card-body p-3">
            <div class="mb-3">
                <form action="{{ route('categories.index') }}" method="GET" class="input-group">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="Tìm kiếm danh mục..." 
                        id="searchInput"
                        value="{{ request('search') }}"
                    >
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Tìm
                    </button>
                    @if(request('search'))
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Tên danh mục</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-4">Thao tác</th> </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td class="ps-4 text-muted">#{{ $cat->id }}</td>
                        <td><strong class="text-dark">{{ $cat->name }}</strong></td>
                        <td class="text-muted">{{ $cat->created_at->format('d/m/Y') }}</td>
                        <td class="text-end pe-4">
                            <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm border-0">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open display-4 opacity-25 mb-3 d-block"></i>
                                Chưa có danh mục nào. Hãy thêm ở bên trái!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== VALIDATION FORM THÊM DANH MỤC =====
    const addCategoryForm = document.getElementById('addCategoryForm');
    const categoryNameInput = document.getElementById('categoryNameInput');
    const categoryError = document.getElementById('categoryError');
    
    if (categoryNameInput) {
        // Real-time: loại bỏ ký tự đặc biệt khi nhập
        categoryNameInput.addEventListener('input', function(e) {
            const value = e.target.value;
            // Chỉ cho phép: chữ cái, số, dấu cách, dấu gạch ngang, dấu chấm
            const cleanValue = value.replace(/[@#$%^&*()+\=\[\]{};:"'<>,?\/\\|`~!]/g, '');
            
            if (value !== cleanValue) {
                e.target.value = cleanValue;
                showErrorMessage(categoryError, 'Ký tự đặc biệt không được phép!');
                setTimeout(() => categoryError.style.display = 'none', 3000);
            }
        });
    }
    
    // Validate trước khi submit
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(e) {
            const value = categoryNameInput.value.trim();
            
            // Kiểm tra chứa ký tự đặc biệt
            if (/@|#|\$|%|\^|&|\*|\(|\)|\+|=|\[|\]|{|}|;|:|"|'|<|>|,|\?|\/|\\|\||`|~|!/.test(value)) {
                e.preventDefault();
                showErrorMessage(categoryError, 'Tên danh mục không được chứa ký tự đặc biệt như: @, #, $, %, &, *, /, v.v.');
                return false;
            }
            
            // Kiểm tra không để trống
            if (value === '') {
                e.preventDefault();
                showErrorMessage(categoryError, 'Tên danh mục không được để trống!');
                return false;
            }
        });
    }
    
    // ===== VALIDATION FORM TÌM KIẾM =====
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        // Real-time: loại bỏ ký tự đặc biệt khi nhập
        searchInput.addEventListener('input', function(e) {
            let value = e.target.value;
            const cleanValue = value.replace(/[@#$%^&*()+\=\[\]{};:"'<>,?\/\\|`~!]/g, '');
            
            if (value !== cleanValue) {
                e.target.value = cleanValue;
                showNotification('Ký tự đặc biệt đã bị loại bỏ', 'warning');
            }
        });
        
        // Xác thực trước khi submit form
        const searchForm = searchInput.closest('form');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                const value = searchInput.value.trim();
                
                // Kiểm tra nếu vẫn còn ký tự đặc biệt
                if (/@|#|\$|%|\^|&|\*|\(|\)|\+|=|\[|\]|{|}|;|:|"|'|<|>|,|\?|\/|\\|\||`|~|!/.test(value)) {
                    e.preventDefault();
                    showNotification('Vui lòng chỉ nhập chữ, số, dấu cách hoặc dấu gạch ngang', 'danger');
                    return false;
                }
            });
        }
    }
});

function showErrorMessage(element, message) {
    element.textContent = message;
    element.style.display = 'block';
    element.style.marginTop = '5px';
}

function showNotification(message, type = 'info') {
    const alertClass = type === 'danger' ? 'alert-danger' : (type === 'warning' ? 'alert-warning' : 'alert-info');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Tự động xóa thông báo sau 3 giây
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}
</script>