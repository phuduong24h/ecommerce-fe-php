<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddCartService
{
    protected $cartBaseUrl;
    protected $timeout;
    protected $verify;

    public function __construct()
    {
        // Lấy URL gốc: http://localhost:3000
        $baseUrl = config('services.api.url');
        
        // --- SỬA DÒNG NÀY ---
        // Phải nối thêm /api/v1/cart để đúng đường dẫn backend
        $this->cartBaseUrl = $baseUrl . '/api/v1/cart';

        $this->timeout = config('services.api.timeout', 30);
        $this->verify = config('services.api.verify', false);
    }

    // ... Giữ nguyên các hàm getHttp, getCart, updateCart bên dưới ...
    protected function getHttp()
    {
        return Http::timeout($this->timeout)->withOptions(['verify' => $this->verify]);
    }

    public function getCart()
    {
        try {
            if (!session('user_token')) {
                return ['success' => false, 'message' => 'Chưa đăng nhập'];
            }

            // Gọi GET tới http://localhost:3000/api/v1/cart
            $response = $this->getHttp()
                            ->withToken(session('user_token'))
                            ->get($this->cartBaseUrl);

            $json = $response->json();
            
            // Log để debug nếu vẫn lỗi
            if ($response->failed()) {
                Log::error('API Get Cart Failed:', ['status' => $response->status(), 'body' => $response->body()]);
            }

            if ($response->failed() || !($json['success'] ?? false)) {
                return ['success' => false, 'message' => 'Không thể lấy giỏ hàng'];
            }

            return ['success' => true, 'data' => $json['data'] ?? []];

        } catch (\Exception $e) {
            Log::error('Exception (getCart): ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }

    public function updateCart(array $cartArray)
    {
        try {
            if (!session('user_token')) {
                return ['success' => false, 'message' => 'Chưa đăng nhập'];
            }

            $body = ['cart' => $cartArray];

            // Gọi PUT tới http://localhost:3000/api/v1/cart
            $response = $this->getHttp()
                            ->withToken(session('user_token'))
                            ->put($this->cartBaseUrl, $body);

            $json = $response->json();
            
            if ($response->failed()) {
                Log::error('API Update Cart Failed:', ['status' => $response->status(), 'body' => $response->body()]);
            }

            if ($response->failed() || !($json['success'] ?? false)) {
                return ['success' => false, 'message' => 'Không thể cập nhật giỏ hàng'];
            }

            return ['success' => true, 'data' => $json['data'] ?? []];

        } catch (\Exception $e) {
            Log::error('Exception (updateCart): ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }
}