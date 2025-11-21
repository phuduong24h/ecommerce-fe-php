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
        // Lấy trực tiếp từ config, không cần nối '/api/v1' nữa vì trong .env đã có rồi
        $baseUrl = config('services.api.url');
        $this->cartBaseUrl = $baseUrl . '/cart';

        $this->timeout = config('services.api.timeout', 30);
        $this->verify = config('services.api.verify', false);
    }

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

            // Dùng token của user đang đăng nhập (Session)
            $response = $this->getHttp()
                            ->withToken(session('user_token'))
                            ->get($this->cartBaseUrl);

            $json = $response->json();
            if ($response->failed() || !$json['success']) {
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

            $response = $this->getHttp()
                            ->withToken(session('user_token'))
                            ->put($this->cartBaseUrl, $body);

            $json = $response->json();
            if ($response->failed() || !$json['success']) {
                return ['success' => false, 'message' => 'Không thể cập nhật giỏ hàng'];
            }

            return ['success' => true, 'data' => $json['data'] ?? []];

        } catch (\Exception $e) {
            Log::error('Exception (updateCart): ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }
}