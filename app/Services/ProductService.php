<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.api.url') . '/api/v1/admin';
        // $this->token = config('services.api.token');
        $this->token = session('admin_token');
    }

    /**
     * Header chuẩn cho tất cả request
     */
    protected function headers()
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Lấy tất cả sản phẩm
     */
    public function getAllProducts()
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/products");

        if ($response->failed()) {
            Log::error('API Error (getAllProducts): ' . $response->body());
            return [];
        }

        $json = $response->json();
        return $json['success'] ?? false ? $json['data'] : [];
    }

    /**
     * Tạo sản phẩm mới
     */
    public function createProduct($data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/products", $payload);

        return $response->json();
    }

    /**
     * Cập nhật sản phẩm
     */
    public function updateProduct($id, $data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->put("{$this->baseUrl}/products/{$id}", $payload);

        return $response->json();
    }

    /**
     * Xóa sản phẩm
     */
    public function deleteProduct($id)
    {
        $response = Http::withHeaders($this->headers())->delete("{$this->baseUrl}/products/{$id}");

        return $response->json();
    }

    /**
     * Chuyển dữ liệu từ form thành payload gửi API
     */
    protected function mapPayload($data)
    {
        $payload = [
            'name' => $data['name'] ?? '',
            'price' => isset($data['price']) ? (float) $data['price'] : 0,
            'stock' => isset($data['stock']) ? (int) $data['stock'] : 0,
            'images' => !empty($data['images']) ? array_filter($data['images']) : [],
        ];

        if (!empty($data['categoryId'])) {
            $payload['categoryId'] = $data['categoryId'];
            if (!empty($data['categoryName'])) {
                $payload['categoryName'] = $data['categoryName'];
            }
        }

        if (!empty($data['description'])) {
            $payload['description'] = $data['description'];
        }
        if (isset($data['warrantyPolicyId'])) {
            $payload['warrantyPolicyId'] = $data['warrantyPolicyId'];
        }

        if (isset($data['isActive'])) {
            $payload['isActive'] = (bool) $data['isActive'];
        }

        if (!empty($data['variants']) && is_array($data['variants'])) {
            // Lọc bỏ các biến thể hoàn toàn trống
            $filteredVariants = array_filter($data['variants'], function ($v) {
                return !empty($v['name']) || !empty($v['value'])
                    || (isset($v['price']) && $v['price'] > 0)
                    || (isset($v['stock']) && $v['stock'] > 0);
            });

            $payload['variants'] = array_map(function ($v) {
                return [
                    'name' => $v['name'] ?? '',
                    'value' => $v['value'] ?? '',
                    'price' => isset($v['price']) ? max(0, (float) $v['price']) : 0,
                    'stock' => isset($v['stock']) ? max(0, (int) $v['stock']) : 0,
                ];
            }, $filteredVariants);
        }


        return $payload;
    }
}
