<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductDetailService
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.api.url') . '/api/v1';
        $this->timeout = config('services.api.timeout', 30);
    }

    public function getProductById($id)
    {
        try {
            // 1. Bấm giờ bắt đầu
            $startTime = microtime(true);

            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/products/{$id}");

            // 2. Bấm giờ kết thúc & Tính toán (ms)
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2); // Đổi sang mili-giây

            if ($response->successful()) {
                $json = $response->json();

                // 3. Trả về cả Dữ liệu + Thời gian + Trạng thái Cache
                return [
                    'product' => $json['data'],
                    'time' => $duration,
                    'is_cached' => $json['cached'] ?? false // Backend bạn đã trả về cờ này rồi
                ];
            }

            Log::error("API Error: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Exception: " . $e->getMessage());
            return null;
        }
    }
}
