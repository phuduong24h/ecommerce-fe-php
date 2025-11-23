<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SerialServiceUser
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        // Base URL API + /api/v1/serial
        $this->baseUrl = rtrim(config('services.api.url'), '/') . '/api/v1/serial';
        // $this->token = session('user_token');
    }

    /**
     * Header chuẩn cho tất cả request
     */
    protected function headers(): array
    {
         $token = session('user_token'); 
        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * GET /my - lấy tất cả serial của user
     */
    public function getMySerials(): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/my");

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? [];
            }

            Log::error("API Get My Serials Failed: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("Lỗi lấy My Serials: " . $e->getMessage());
            return [];
        }
    }

    /**
     * POST /register - đăng ký serial
     */
    public function registerSerial(string $serial): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->post("{$this->baseUrl}/register", ['serial' => $serial]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("API Register Serial Failed: " . $response->body());
            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Backend từ chối yêu cầu'
            ];
        } catch (\Exception $e) {
            Log::error("Lỗi register serial: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getSerialForItem(array $item, string $orderId): ?string
    {
        $serials = $this->getMySerials(); // Lấy toàn bộ serial của user

        foreach ($serials as $s) {
            if ($s['productId'] === $item['productId'] && $s['orderId'] === $orderId) {
                return $s['serial'] ?? null; // Trả về serial đúng
            }
        }

        return null; // Nếu không tìm thấy
    }
    public function getUserSerials(): array
    {
        $serials = $this->getMySerials(); // Lấy tất cả serial của user
        $result = [];

        foreach ($serials as $s) {
            // Giả sử API trả về productName cùng với productId
            $result[] = [
                'serial' => $s['serial'],
                'productName' => $s['productName'] ?? 'Sản phẩm',
            ];
        }

        return $result;
    }
    //check bảo hành cho 1 serial xem còn hạn hay không
    public function checkSerialWarranty(string $serial): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/check/{$serial}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("API Check Serial Failed: " . $response->body());
            return [
                'success' => false,
                'message' => 'Không thể kiểm tra bảo hành'
            ];
        } catch (\Exception $e) {
            Log::error("Lỗi check serial: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }


}
