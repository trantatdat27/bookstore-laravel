<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', 'min:6', 'max:255', Rules\Password::defaults()],
                'password_confirmation' => ['required'],
            ],
            [
                'name.required' => 'Họ và tên là bắt buộc.',
                'name.string' => 'Họ và tên phải là một chuỗi ký tự.',
                'name.min' => 'Họ và tên phải có ít nhất 3 ký tự.',
                'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
                'email.required' => 'Email là bắt buộc.',
                'email.email' => 'Email không hợp lệ.',
                'email.max' => 'Email không được vượt quá 255 ký tự.',
                'email.unique' => 'Email này đã tồn tại trong hệ thống.',
                'password.required' => 'Mật khẩu là bắt buộc.',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
                'password.max' => 'Mật khẩu không được vượt quá 255 ký tự.',
                'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
                'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu.',
            ]
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
