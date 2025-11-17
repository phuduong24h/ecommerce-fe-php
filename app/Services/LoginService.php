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
    /**
     * Gọi API đăng nhập
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login($email, $password)
    {
        try {
            $response = Http::timeout(10)->post("{$this->authBaseUrl}/login", [
                'email' => $email,
                'password' => $password,
            ]);

            $json = $response->json();

            if ($response->failed() || is_null($json) || !$json['success']) {
                $message = $json['error']['message'] ?? 'INVALID_CREDENTIALS';
                if ($message === 'INVALID_CREDENTIALS') $message = 'Sai email hoặc mật khẩu';

                return ['success' => false, 'message' => $message];
            }

            // { success: true, data: { token, user } }
            return [
                'success' => true,
                'data' => $json['data']
            ];

        } catch (\Exception $e) {
            Log::error('Exception (login): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống. Vui lòng thử lại.'];
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
            $response = Http::timeout(10)->post("{$this->authBaseUrl}/register", $details);
            $json = $response->json();

            if ($response->failed() || is_null($json) || !$json['success']) {
                $message = $json['error']['message'] ?? 'REGISTER_FAILED';
                return ['success' => false, 'message' => $message];
            }

            // { success: true, data: { ...user } }
            return [
                'success' => true,
                'data' => $json['data']
            ];

        } catch (\Exception $e) {
            Log::error('Exception (register): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống. Vui lòng thử lại.'];
        }
    }
}
