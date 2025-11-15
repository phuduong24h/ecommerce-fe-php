<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\LoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * Hiển thị view đăng nhập/đăng ký
     */
    public function showLoginForm()
    {
        // Nếu đã đăng nhập thì về trang chủ
        if (session('user')) {
            return redirect()->route('home');
        }
        return view('user.auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $result = $this->loginService->login($request->email, $request->password);

        if (!$result['success']) {
            return back()->withErrors(['email' => $result['message'] ?? 'Sai email hoặc mật khẩu.'])->withInput();
        }

        // Lưu thông tin user và token vào session
        session([
            'user' => $result['data']['user'],
            'user_token' => $result['data']['token']
        ]);

        return redirect()->route('home');
    }

    /**
     * Xử lý đăng ký
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed', // 'password_confirmation'
        ]);

        if ($validator->fails()) {
            return back()->with('tab', 'register')->withErrors($validator)->withInput();
        }

        $details = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
        ];

        $result = $this->loginService->register($details);

        if (!$result['success']) {
            // Kiểm tra lỗi từ backend (ví dụ: EMAIL_TAKEN)
            $message = $result['message'] ?? 'Đăng ký thất bại. Vui lòng thử lại.';
            if (str_contains($message, 'EMAIL_TAKEN')) {
                $message = 'Email này đã được sử dụng.';
            }

            return back()->with('tab', 'register')->withErrors(['email' => $message])->withInput();
        }

        // Đăng ký thành công, chuyển về tab login với thông báo
        return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout()
    {
        session()->forget(['user', 'user_token']);
        return redirect()->route('home');
    }
}
