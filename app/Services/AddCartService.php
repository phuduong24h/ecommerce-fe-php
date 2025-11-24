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
        // Lấy URL gốc và cắt dấu / thừa ở cuối để tránh lỗi "double slash"
        $baseUrl = rtrim(config('services.api.url'), '/');
        
        // Nối đúng đường dẫn API Backend
        $this->cartBaseUrl = $baseUrl . '/api/v1/cart';

        $this->timeout = config('services.api.timeout', 30);
        $this->verify = config('services.api.verify', false);
    }

    protected function getHttp()
    {
        return Http::timeout($this->timeout)->withOptions(['verify' => $this->verify]);
    }

    /**
     * Lấy giỏ hàng (SỬA LỖI F5 BỊ MẤT STOCK)
     */
    public function getCart()
    {
        try {
            if (!session('user_token')) {
                return ['success' => false, 'message' => 'Chưa đăng nhập'];
            }

            // 1. Gọi API lấy dữ liệu mới nhất từ Backend (Dữ liệu này KHÔNG có stock)
            $response = $this->getHttp()
                            ->withToken(session('user_token'))
                            ->get($this->cartBaseUrl);

            $json = $response->json();
            
            if ($response->failed() || !($json['success'] ?? false)) {
                return ['success' => false, 'message' => 'Không thể lấy giỏ hàng'];
            }

            $apiCart = $json['data'] ?? [];

            // 2. 🔥 LOGIC KHÔI PHỤC STOCK TỪ SESSION 🔥
            // Lấy giỏ hàng đang lưu trong session (đang chứa stock đúng từ lần trước)
            $sessionCart = session('user.cart', []); 
            
            // Duyệt qua cart từ API và điền lại stock từ Session vào
            foreach ($apiCart as &$item) {
                // Mặc định là 0 nếu không tìm thấy trong session
                $item['stock'] = 0; 

                foreach ($sessionCart as $sItem) {
                    // So sánh ProductID và Variant để tìm đúng món hàng
                    $sVariant = $sItem['variant'] ?? null;
                    $iVariant = $item['variant'] ?? null;

                    if ($sItem['productId'] == $item['productId'] && $sVariant == $iVariant) {
                        // Khôi phục stock
                        $item['stock'] = $sItem['stock'] ?? 0;
                        break;
                    }
                }
            }
            unset($item); // Hủy tham chiếu

            return ['success' => true, 'data' => $apiCart];

        } catch (\Exception $e) {
            Log::error('Exception (getCart): ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }

    /**
     * Cập nhật giỏ hàng (SỬA LỖI KHI BẤM CỘNG TRỪ)
     */
    public function updateCart(array $cartArray)
    {
        try {
            if (!session('user_token')) {
                return ['success' => false, 'message' => 'Chưa đăng nhập'];
            }

            // BƯỚC 1: Lọc bỏ 'stock' để gửi lên Backend sạch sẽ
            $cleanCart = array_map(function($item) {
                if(isset($item['stock'])) unset($item['stock']);
                return $item;
            }, $cartArray);

            $body = ['cart' => $cleanCart];

            // BƯỚC 2: Gọi API PUT
            $response = $this->getHttp()
                            ->withToken(session('user_token'))
                            ->put($this->cartBaseUrl, $body);

            $json = $response->json();
            
            if ($response->failed() || !($json['success'] ?? false)) {
                return ['success' => false, 'message' => 'Không thể cập nhật giỏ hàng'];
            }

            // Dữ liệu Backend trả về (đang bị thiếu stock)
            $returnedCart = $json['data'] ?? [];

            // =================================================================
            // 🟢 BƯỚC 3: GHÉP LẠI STOCK VÀO KẾT QUẢ TRẢ VỀ
            // =================================================================
            foreach ($returnedCart as $key => &$item) {
                if (isset($cartArray[$key]) && isset($cartArray[$key]['stock'])) {
                    $item['stock'] = $cartArray[$key]['stock'];
                }
            }
            unset($item); 
            // =================================================================

            return ['success' => true, 'data' => $returnedCart];

        } catch (\Exception $e) {
            Log::error('Exception (updateCart): ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }
}