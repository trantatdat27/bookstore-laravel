@extends('client.layout')
@section('title', 'Đăng nhập')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle text-primary display-4 mb-3"></i>
                        <h3 class="fw-bold">Đăng Nhập</h3>
                        <p class="text-muted">Chào mừng bạn quay lại với Bookstore</p>
                    </div>

                    <x-auth-session-status class="mb-4 text-success fw-bold" :status="session('status')" />
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 small">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input 
                                type="email" 
                                id="email"
                                name="email" 
                                class="form-control px-3 py-2 rounded-3 @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}" 
                                placeholder="Nhập email của bạn"
                                required 
                                autofocus>
                            @error('email')
                                <div class="invalid-feedback d-block text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                            <input 
                                type="password" 
                                id="password"
                                name="password" 
                                class="form-control px-3 py-2 rounded-3 @error('password') is-invalid @enderror" 
                                placeholder="Nhập mật khẩu"
                                required 
                                autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback d-block text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    name="remember" 
                                    id="remember_me"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted" for="remember_me">Nhớ mật khẩu</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-decoration-none small">Quên mật khẩu?</a>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold fs-5 shadow-sm">Đăng Nhập</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">Chưa có tài khoản? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Đăng ký ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection