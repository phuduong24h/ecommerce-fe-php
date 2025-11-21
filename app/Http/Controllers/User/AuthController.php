<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Văn Hải
class AuthController extends Controller
{
    /**
     * Hàm tạo URL API chuẩn
     * - config('services.api.url') là "http://localhost:3000"
     * - Hàm này sẽ nối thêm "/api/v1" và endpoint
     */
    private function apiUrl($path)
    {
        // 1. Lấy URL gốc (http://localhost:3000)
        $base = rtrim(config('services.api.url'), '/');
        
        // 2. Đảm bảo path bắt đầu bằng /
        $path = '/' . ltrim($path, '/');

        // 3. Nối chuỗi: http://localhost:3000 + /api/v1 + /path
        return $base . '/api/v1' . $path;
    }

    /**
     * Hàm gửi request có token (Dùng cho các logic nội bộ nếu cần)
     */
    protected function apiWithToken($method, $path, $data = [])
    {
        $token = session('admin_token');

        return Http::withToken($token)
            ->$method($this->apiUrl($path), $data);
    }

    // ============================================
    // USER LOGIN (KHÁCH HÀNG)
    // ============================================
    public function showUserLogin()
    {
        return view('user.auth.login');
    }

    public function userLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string'
        ]);

        // Gọi API: http://localhost:3000/api/v1/auth/login
        $res = Http::post($this->apiUrl("/auth/login"), [
            "email"    => $request->email,
            "password" => $request->password
        ]);

        $json = $res->json();

        // Kiểm tra lỗi từ Backend
        if ($res->failed() || !($json['success'] ?? false)) {
            return back()->withErrors(['msg' => $json['message'] ?? 'Sai email hoặc mật khẩu!']);
        }

        // SỬA QUAN TRỌNG: Lấy data từ key ['data']
        $data = $json['data'];

        // Lưu Session cho User (Dùng key user_token để đồng bộ với CartController/WarrantyController)
        session([
            'user_token' => $data['token'], // Đồng bộ tên key với các Controller khác
            'user'       => $data['user']   // Đồng bộ tên key với các Controller khác
        ]);

        return redirect()->route('home'); // Thường user login xong về trang chủ hoặc trang trước đó
    }

    public function userLogout()
    {
        // Xóa session user
        session()->forget(['user_token', 'user', 'user.cart']);
        return redirect()->route('login');
    }

    // ============================================
    // ADMIN LOGIN
    // ============================================
    public function showLogin()
    {
        return view('user.admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string'
        ]);

        // Gọi API: http://localhost:3000/api/v1/admin/auth/login
        $response = Http::asJson()->post($this->apiUrl("/admin/auth/login"), [
            'email'    => $request->email,
            'password' => $request->password
        ]);

        $json = $response->json();

        if ($response->failed() || !($json['success'] ?? false)) {
            return back()->withErrors(['error' => $json['message'] ?? 'Đăng nhập thất bại!']);
        }

        // Kiểm tra và lưu token Admin
        if (isset($json['data']['token'])) {
            session(['admin_token' => $json['data']['token']]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['error' => 'Lỗi hệ thống: Không nhận được Token']);
    }

    // ============================================
    // GỬI OTP (ADMIN)
    // ============================================
    public function showForgot()
    {
        return view('user.admin.forgot-password');
    }

    public function submitForgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = Http::post($this->apiUrl("/admin/auth/send-otp"), [
            'email' => $request->email
        ]);

        if ($response->failed()) {
            return back()->withErrors(['error' => 'Không thể gửi OTP!']);
        }

        session(['reset_email' => $request->email]);

        return redirect()->route('admin.verify')
            ->with('success', 'Đã gửi mã OTP!');
    }

    // ============================================
    // XÁC MINH OTP (ADMIN)
    // ============================================
    public function showVerify()
    {
        if (!session('reset_email')) {
            return redirect()->route('admin.forgot');
        }
        return view('user.admin.verify-otp');
    }

    public function submitVerify(Request $request)
    {
        $request->validate(['otp' => 'required']);

        $response = Http::post($this->apiUrl("/admin/auth/verify-otp"), [
            'email' => session('reset_email'),
            'otp'   => $request->otp
        ]);

        if ($response->failed()) {
            return back()->withErrors(['error' => 'Mã OTP không đúng hoặc đã hết hạn!']);
        }

        return redirect()->route('admin.reset')
            ->with('success', 'OTP hợp lệ. Hãy đặt mật khẩu mới.');
    }

    // ============================================
    // ĐẶT LẠI MẬT KHẨU (ADMIN)
    // ============================================
    public function showReset()
    {
        if (!session('reset_email')) {
            return redirect()->route('admin.forgot');
        }
        return view('user.admin.reset-password');
    }

    public function submitReset(Request $request)
    {
        $request->validate(['password' => 'required|min:6']);

        $response = Http::post($this->apiUrl("/admin/auth/reset-password"), [
            'email'    => session('reset_email'),
            'password' => $request->password
        ]);

        if ($response->failed()) {
            return back()->withErrors(['error' => 'Đặt lại mật khẩu thất bại!']);
        }

        session()->forget('reset_email');

        return redirect()->route('admin.login')
            ->with('success', 'Đặt lại mật khẩu thành công!');
    }

    // ============================================
    // ADMIN LOGOUT
    // ============================================
    public function logout()
    {
        session()->forget('admin_token');
        return redirect()->route('admin.login');
    }
}