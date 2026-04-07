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

                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                id="name"
                                name="name" 
                                class="form-control px-3 py-2 rounded-3 @error('name') is-invalid @enderror" 
                                value="{{ old('name') }}" 
                                placeholder="Nhập họ và tên của bạn"
                                required 
                                autofocus>
                            @error('name')
                                <div class="invalid-feedback d-block text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input 
                                type="email" 
                                id="email"
                                name="email" 
                                class="form-control px-3 py-2 rounded-3 @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}" 
                                placeholder="Nhập email của bạn"
                                required>
                            @error('email')
                                <div class="invalid-feedback d-block text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                                <input 
                                    type="password" 
                                    id="password"
                                    name="password" 
                                    class="form-control px-3 py-2 rounded-3 @error('password') is-invalid @enderror" 
                                    placeholder="Nhập mật khẩu"
                                    required 
                                    autocomplete="new-password">
                                @error('password')
                                    <div class="invalid-feedback d-block text-danger small mt-2">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle"></i> Ít nhất 6 ký tự, bao gồm chữ hoa, chữ thường, số
                                </small>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <label for="password_confirmation" class="form-label fw-bold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <input 
                                    type="password" 
                                    id="password_confirmation"
                                    name="password_confirmation" 
                                    class="form-control px-3 py-2 rounded-3 @error('password_confirmation') is-invalid @enderror" 
                                    placeholder="Xác nhận mật khẩu"
                                    required 
                                    autocomplete="new-password">
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block text-danger small mt-2">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
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