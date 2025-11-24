<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginService
{
    protected $authBaseUrl;

    public function __construct()
    {
        $this->authBaseUrl = config('services.api.url', 'http://localhost:3000') . '/api/v1/auth'; // <-- THÊM DÒNG NÀY
    }
<<<<<<< Updated upstream
    /**
     * Gọi API đăng nhập
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login($email, $password)
=======

    public function login($email, $password, $address = null)
>>>>>>> Stashed changes
    {
        try {
            // Chuẩn bị dữ liệu gửi đi
            $payload = [
                'email' => $email,
                'password' => $password,
            ];

            // Lưu ý: Hiện tại auth.ts endpoint /login KHÔNG xử lý address.
            // Nhưng ta cứ gửi đi để code không bị lỗi logic.
            if ($address) {
                $payload['address'] = $address;
            }

            // CHỈ GỌI API 1 LẦN DUY NHẤT
            $response = Http::timeout(10)->post("{$this->authBaseUrl}/login", $payload);

            $json = $response->json();

<<<<<<< Updated upstream
            if ($response->failed() || is_null($json) || !$json['success']) {
                $message = $json['error']['message'] ?? 'INVALID_CREDENTIALS';
                if ($message === 'INVALID_CREDENTIALS') $message = 'Sai email hoặc mật khẩu';
=======
            // Xử lý lỗi từ Backend hoặc lỗi mạng
            if ($response->failed() || is_null($json) || !($json['success'] ?? false)) {
                $message = $json['message'] ?? 'INVALID_CREDENTIALS';

                if ($message === 'INVALID_CREDENTIALS') {
                    $message = 'Sai email hoặc mật khẩu';
                }
                if ($message === 'MISSING_FIELDS') {
                    $message = 'Vui lòng nhập đầy đủ thông tin';
                }
>>>>>>> Stashed changes

                return ['success' => false, 'message' => $message];
            }

            // { success: true, data: { token, user } }
            return [
                'success' => true,
                'data' => $json['data']
            ];

        } catch (\Exception $e) {
            Log::error('Exception (login): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi kết nối đến máy chủ.'];
        }
    }

    /**
     * Gọi API đăng ký
     *
     * @param array $details - ['name', 'email', 'phone', 'password']
     * @return array
     */
    public function register(array $details)
    {
        try {
            // Endpoint /register trong auth.ts CÓ hỗ trợ address
            $response = Http::timeout(10)->post("{$this->authBaseUrl}/register", $details);
            $json = $response->json();

<<<<<<< Updated upstream
            if ($response->failed() || is_null($json) || !$json['success']) {
                $message = $json['error']['message'] ?? 'REGISTER_FAILED';
=======
            if ($response->failed() || is_null($json) || !($json['success'] ?? false)) {
                $message = $json['message'] ?? 'REGISTER_FAILED';

                if ($message === 'EMAIL_TAKEN') {
                    $message = 'Email này đã được sử dụng.';
                }
                if ($message === 'MISSING_FIELDS') {
                    $message = 'Thiếu thông tin đăng ký.';
                }

>>>>>>> Stashed changes
                return ['success' => false, 'message' => $message];
            }

            // { success: true, data: { ...user } }
            return [
                'success' => true,
                'data' => $json['data']
            ];

        } catch (\Exception $e) {
            Log::error('Exception (register): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi kết nối đến máy chủ.'];
        }
    }
}
