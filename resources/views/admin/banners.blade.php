@extends('admin.layout') {{-- Kế hoạch layout admin hiện có --}}
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white">Thêm Banner Mới</div>
                <div class="card-body">
                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hình ảnh banner</label>
                            <input type="file" name="image" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề (nếu có)</label>
                            <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề banner">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">TẢI LÊN</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Tiêu đề</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banners as $banner)
                            <tr>
                                <td><img src="{{ asset($banner->image) }}" width="200" class="rounded shadow-sm"></td>
                                <td>{{ $banner->title }}</td>
                                <td>
                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST">
    @csrf 
    @method('DELETE')
    <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa banner này?')">Xóa</button>
</form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection