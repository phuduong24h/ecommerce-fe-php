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
       // SỬA: Nối thêm '/api/v1' vì config mặc định chỉ là localhost:3000
        $this->baseUrl = config('services.api.url') . '/api/v1';
        $this->timeout = config('services.api.timeout', 30);
    }

    public function getProductById($id)
    {
        try {
            // URL đúng sẽ là: http://localhost:3000/api/v1/products/{id}
            // Không cần thêm /api/v1 ở đây nữa vì $this->baseUrl đã có rồi
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/products/{$id}");

            if ($response->successful()) {
                return $response->json()['data'];
            }

            // Log lỗi để admin kiểm tra ngầm, không dùng dd() làm gián đoạn người dùng
            Log::error("API Error (getProductById $id): " . $response->body());

            return null;
        } catch (\Exception $e) {
            Log::error("Exception (getProductById): " . $e->getMessage());
            return null;
        }
    }
}
