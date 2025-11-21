<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddCartService
{
    protected $baseUrl;
    protected $cartBaseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.api.url', 'http://localhost:3000') . '/api/v1';
        $this->cartBaseUrl = $this->baseUrl . '/cart'; // <-- THÊM DÒNG NÀY
    }

    public function getCart()
    {
        try {
            if (!session('user_token')) {
                return ['success' => false, 'message' => 'Chưa đăng nhập'];
            }

            $response = Http::withToken(session('user_token'))
                            ->timeout(10)
                            ->get($this->cartBaseUrl);

            $json = $response->json();
            if ($response->failed() || !$json['success']) {
                return ['success' => false, 'message' => 'Không thể lấy giỏ hàng'];
            }

            // Trả về mảng data (là mảng giỏ hàng)
            return ['success' => true, 'data' => $json['data'] ?? []];

        } catch (\Exception $e) {
            Log::error('Exception (getCart): ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }

    /**
     * Cập nhật toàn bộ giỏ hàng lên backend
     * Yêu cầu đã đăng nhập (gửi token)
     *
     * @param array $cartArray Mảng giỏ hàng mới
     */
    public function updateCart(array $cartArray)
    {
        try {
            if (!session('user_token')) {
                return ['success' => false, 'message' => 'Chưa đăng nhập'];
            }

            // API backend yêu cầu body là { cart: [...] }
            $body = ['cart' => $cartArray];

            $response = Http::withToken(session('user_token'))
                            ->timeout(10)
                            ->put($this->cartBaseUrl, $body);

            $json = $response->json();
            if ($response->failed() || !$json['success']) {
                return ['success' => false, 'message' => 'Không thể cập nhật giỏ hàng'];
            }

            // Trả về mảng data (là mảng giỏ hàng đã cập nhật)
            return ['success' => true, 'data' => $json['data'] ?? []];

        } catch (\Exception $e) {
            Log::error('Exception (updateCart): ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }
}
