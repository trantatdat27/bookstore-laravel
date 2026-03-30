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
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Tên danh mục</label>
                        <input type="text" name="name" class="form-control" placeholder="VD: Sách giáo khoa..." required>
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