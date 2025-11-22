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

    public function login($email, $password)
    {
        try {
            $response = Http::timeout(10)->post("{$this->authBaseUrl}/login", [
                'email' => $email,
                'password' => $password,
            ]);

            $json = $response->json();

            if ($response->failed() || is_null($json) || !$json['success']) {

                // Backend trả message trực tiếp
                $message = $json['message'] ?? 'INVALID_CREDENTIALS';

                // Tùy chỉnh câu thông báo
                if ($message === 'INVALID_CREDENTIALS') {
                    $message = 'Sai email hoặc mật khẩu';
                }

                return ['success' => false, 'message' => $message];
            }

            return [
                'success' => true,
                'data' => $json['data']
            ];

        } catch (\Exception $e) {
            Log::error('Exception (login): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống. Vui lòng thử lại.'];
        }
    }

    public function register(array $details)
    {
        try {
            $response = Http::timeout(10)->post("{$this->authBaseUrl}/register", $details);
            $json = $response->json();

            if ($response->failed() || is_null($json) || !$json['success']) {

                // Backend cũng trả message trực tiếp
                $message = $json['message'] ?? 'REGISTER_FAILED';

                return ['success' => false, 'message' => $message];
            }

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
