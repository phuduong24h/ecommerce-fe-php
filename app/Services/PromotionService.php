<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PromotionService
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
     * Lấy tất cả promotion
     */
    public function getAllPromotions()
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/promotion");

        if ($response->failed()) {
            Log::error('API Error (getAllPromotions): ' . $response->body());
            return [];
        }

        $json = $response->json();
        return $json['success'] ?? false ? $json['data'] : [];
    }

    /**
     * Tạo promotion mới
     */
    public function createPromotion($data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/promotion", $payload);

        return $response->json();
    }

    /**
     * Cập nhật promotion
     */
    public function updatePromotion($id, $data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->put("{$this->baseUrl}/promotion/{$id}", $payload);

        return $response->json();
    }

    /**
     * Xóa promotion
     */
    public function deletePromotion($id)
    {
        $response = Http::withHeaders($this->headers())->delete("{$this->baseUrl}/promotion/{$id}");

        return $response->json();
    }

    /**
     * Chuyển dữ liệu từ form thành payload gửi API
     */
    protected function mapPayload($data)
    {
        return [
            'code' => $data['code'] ?? '',
            'description' => $data['description'] ?? '',
            'discount' => isset($data['discount']) ? floatval($data['discount']) : 0,
            'startDate' => $data['startDate'] ?? null,
            'endDate' => $data['endDate'] ?? null,
            'isActive' => isset($data['isActive']) ? boolval($data['isActive']) : true,

            // products fix cho Prisma
            'products' => isset($data['productIds']) && is_array($data['productIds']) && count(array_filter($data['productIds'])) > 0
                ? ['set' => array_map(fn($id) => ['id' => $id], array_filter($data['productIds']))]
                : ['set' => []],
        ];
    }



}
