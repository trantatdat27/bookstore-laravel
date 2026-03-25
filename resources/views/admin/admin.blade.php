<!DOCTYPE html>
<html>
<head>
    <title>Admin - Quản lý sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý sách</h2>
        <div>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Quản lý danh mục</a>
            <a href="{{ route('admin.orders') }}" class="btn btn-warning fw-bold text-dark">Quản lý Đơn hàng</a>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" class="mb-5 border p-4 bg-white rounded shadow-sm">
        @csrf
        <h4 class="mb-3">Thêm sách mới</h4>
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="title" placeholder="Tên sách" class="form-control mb-2" required>
                <input type="text" name="author" placeholder="Tác giả" class="form-control mb-2" required>
                <input type="number" name="price" placeholder="Giá (VNĐ)" class="form-control mb-2" required>
                <input type="number" name="stock" placeholder="Số lượng kho" class="form-control mb-2" required min="0">
                
                <select name="category_id" class="form-control mb-2" required>
                    <option value="">Chọn danh mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <textarea name="description" placeholder="Mô tả" class="form-control mb-2" rows="4"></textarea>
                <input type="file" name="image" class="form-control mb-2">
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-2">Thêm sách</button>
    </form>

    <table class="table table-bordered shadow-sm bg-white">
        <thead class="table-dark">
            <tr>
                <th>Ảnh</th>
                <th>Tên</th>
                <th>Tác giả</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Kho</th> <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
            <tr>
                <td>
                    @if($book->image)
                        <img src="{{ asset($book->image) }}" width="60" class="img-thumbnail">
                    @else
                        <span class="text-muted small">N/A</span>
                    @endif
                </td>
                <td class="fw-bold">{{ $book->title }}</td>
                <td>{{ $book->author }}</td>
                <td>{{ $book->category->name ?? 'N/A' }}</td>
                <td>{{ number_format($book->price) }}đ</td>
                <td class="text-center">
                    <span class="badge {{ $book->stock <= 5 ? 'bg-danger' : 'bg-info' }}">
                        {{ $book->stock }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('books.edit', $book->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa sách?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>