@extends('client.layout')
@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus text-primary display-4 mb-3"></i>
                        <h3 class="fw-bold">Đăng Ký Tài Khoản</h3>
                        <p class="text-muted">Tham gia cùng chúng tôi để mua sách dễ dàng hơn</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 small">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ và tên</label>
                            <input type="text" name="name" class="form-control px-3 py-2 rounded-3" value="{{ old('name') }}" minlength="2" maxlength="255" required autofocus autocomplete="name">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control px-3 py-2 rounded-3" value="{{ old('email') }}" maxlength="255" required autocomplete="email">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mật khẩu</label>
                                <input type="password" name="password" class="form-control px-3 py-2 rounded-3" required autocomplete="new-password">
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <label class="form-label fw-bold">Xác nhận mật khẩu</label>
                                <input type="password" name="password_confirmation" class="form-control px-3 py-2 rounded-3" required autocomplete="new-password">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold fs-5 shadow-sm">Tạo Tài Khoản</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">Đã có tài khoản? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Đăng nhập ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection