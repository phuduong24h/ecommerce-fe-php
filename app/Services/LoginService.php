<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginService
{
    protected $authBaseUrl;

    public function __construct()
    {
        // SỬA: Thêm /api/v1 trước /auth
        $this->authBaseUrl = config('services.api.url', 'http://localhost:3000') . '/api/v1/auth';
    }

    /**
     * Gọi API đăng nhập
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login($email, $password, $address = null)
    {
        try {
            // Chuẩn bị dữ liệu gửi đi
            $payload = [
                'email' => $email,
                'password' => $password,
            ];

            // Nếu có address thì gửi lên
            if ($address) {
                $payload['address'] = $address;
            }

            // Gọi API
            $response = Http::timeout(10)->post("{$this->authBaseUrl}/login", $payload);
            $json = $response->json();

            // Xử lý lỗi
            if ($response->failed() || is_null($json) || !($json['success'] ?? false)) {

                $message = $json['message'] ?? 'INVALID_CREDENTIALS';

                if ($message === 'INVALID_CREDENTIALS') {
                    $message = 'Sai email hoặc mật khẩu';
                }

                if ($message === 'MISSING_FIELDS') {
                    $message = 'Vui lòng nhập đầy đủ thông tin';
                }

                return ['success' => false, 'message' => $message];
            }

            return [
                'success' => true,
                'data' => $json['data']
            ];

        } catch (\Exception $e) {
            Log::error('Exception (login): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi kết nối đến máy chủ.'];
        }
    }

    public function register(array $details)
    {
        try {
            // Gọi API đăng ký
            $response = Http::timeout(10)->post("{$this->authBaseUrl}/register", $details);
            $json = $response->json();

            if ($response->failed() || is_null($json) || !($json['success'] ?? false)) {

                $message = $json['message'] ?? 'REGISTER_FAILED';

                if ($message === 'EMAIL_TAKEN') {
                    $message = 'Email này đã được sử dụng.';
                }

                if ($message === 'MISSING_FIELDS') {
                    $message = 'Thiếu thông tin đăng ký.';
                }

                return ['success' => false, 'message' => $message];
            }

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
