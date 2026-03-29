<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, $next)
{
    // Nếu đã đăng nhập VÀ có quyền admin thì cho đi tiếp
    if (auth()->check() && auth()->user()->role === 'admin') {
        return $next($request);
    }

    // Nếu không phải admin, đá về trang chủ với thông báo lỗi
    return redirect('/')->with('error', 'Bạn không có quyền truy cập trang quản trị!');
}
}
