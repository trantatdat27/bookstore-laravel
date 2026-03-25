<!DOCTYPE html>
<html>
<head>
    <title>Sửa sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chỉnh sửa sách: {{ $book->title }}</h2>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="border p-4 bg-white shadow-sm rounded">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="fw-bold">Tên sách</label>
                <input type="text" name="title" value="{{ $book->title }}" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold">Tác giả</label>
                <input type="text" name="author" value="{{ $book->author }}" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="fw-bold">Giá (VNĐ)</label>
                <input type="number" name="price" value="{{ $book->price }}" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-bold">Số lượng kho</label>
                <input type="number" name="stock" value="{{ $book->stock }}" class="form-control" required min="0">
            </div>

            <div class="col-md-4 mb-3">
                <label class="fw-bold">Danh mục</label>
                <select name="category_id" class="form-control" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $book->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="fw-bold">Ảnh sản phẩm</label><br>
            @if($book->image)
                <img src="{{ asset($book->image) }}" width="120" class="mb-2 border p-1">
            @endif
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label class="fw-bold">Mô tả</label>
            <textarea name="description" class="form-control" rows="5">{{ $book->description }}</textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">Cập nhật thay đổi</button>
    </form>
</body>
</html>