<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Hàm tạo URL API (không phải viết lại nhiều lần)
     */
    private function apiUrl($path)
    {
        return rtrim(config('services.api.url'), '/') . $path;
    }

    /**
     * Hàm gửi request có token (DÙNG CHO CÁC CONTROLLER KHÁC)
     */
    protected function apiWithToken($method, $path, $data = [])
    {
        $token = session('admin_token');  // lấy token từ session

        return Http::withToken($token)
                   ->$method($this->apiUrl($path), $data);
    }

    // ============================================
    // USER LOGIN (BẢO HÀNH)
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

        $res = Http::post($this->apiUrl("/api/v1/auth/login"), [
            "email"    => $request->email,
            "password" => $request->password
        ]);

        if ($res->failed()) {
            return back()->withErrors(['msg' => 'Sai email hoặc mật khẩu!']);
        }

        $data = $res->json();

        session([
            'node_token' => $data['token'],
            'node_user'  => $data['user']
        ]);

        return redirect()->route('warranty.index');
    }

    public function userLogout()
    {
        session()->forget(['node_token', 'node_user']);
        return redirect()->route('user.login');
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

        $response = Http::post($this->apiUrl("/api/v1/admin/auth/login"), [
            'email'    => $request->email,
            'password' => $request->password
        ]);

        if ($response->failed()) {
            return back()->withErrors(['error' => 'Sai email hoặc mật khẩu!']);
        }

        $data  = $response->json();

        // ✔ LƯU TOKEN ĐỘNG
        session(['admin_token' => $data['data']['token']]);

        return redirect()->route('admin.dashboard');
    }

    // ============================================
    // GỬI OTP
    // ============================================
    public function showForgot()
    {
        return view('user.admin.forgot-password');
    }

    public function submitForgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $response = Http::post($this->apiUrl("/api/v1/admin/auth/send-otp"), [
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
    // XÁC MINH OTP
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
        $request->validate([
            'otp' => 'required'
        ]);

        $response = Http::post($this->apiUrl("/api/v1/admin/auth/verify-otp"), [
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
    // HIỂN THỊ FORM ĐẶT LẠI MẬT KHẨU
    // ============================================
    public function showReset()
    {
        if (!session('reset_email')) {
            return redirect()->route('admin.forgot');
        }

        return view('user.admin.reset-password');
    }

    // ============================================
    // SUBMIT ĐẶT LẠI MẬT KHẨU
    // ============================================
    public function submitReset(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6'
        ]);

        $response = Http::post($this->apiUrl("/api/v1/admin/auth/reset-password"), [
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
