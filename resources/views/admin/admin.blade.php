@extends('admin.layout')
@section('title', 'Quản lý Sách')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark"><i class="fas fa-book-open text-primary me-2"></i>Quản lý sách</h3>
</div>

<div class="card mb-5">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle text-success me-2"></i>Thêm sách mới</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="title" placeholder="Tên sách" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="author" placeholder="Tác giả" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="price" placeholder="Giá bán" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="stock" placeholder="Số lượng kho" class="form-control" required min="0">
                </div>
                
                <div class="col-md-4">
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="file" name="image" class="form-control" title="Ảnh sản phẩm">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="fas fa-save me-1"></i>Lưu Sách</button>
                </div>
                
                <div class="col-12">
                    <textarea name="description" placeholder="Mô tả sách (tùy chọn)..." class="form-control" rows="2"></textarea>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Ảnh</th>
                        <th>Tên sách</th>
                        <th>Tác giả</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th class="text-center">Kho</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                    <tr>
                        <td class="ps-3">
                            @if($book->image)
                                <img src="{{ asset($book->image) }}" style="width: 50px; height: 70px; object-fit: cover;" class="rounded border">
                            @else
                                <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 70px; font-size: 10px;">N/A</div>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">{{ $book->title }}</td>
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
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa cuốn sách này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection