<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.api.url') . '/api/v1/admin';
        $this->token = config('services.api.token');
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
     * Lấy tất cả category
     */
    public function getAllCategories()
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/categories");

        if ($response->failed()) {
            Log::error('API Error (getAllCategories): ' . $response->body());
            return [];
        }

        $json = $response->json();
        return $json['success'] ?? false ? $json['data'] : [];
    }

    /**
     * Tạo category mới
     */
    public function createCategory($data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/categories", $payload);

        return $response->json();
    }

    /**
     * Cập nhật category
     */
    public function updateCategory($id, $data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->put("{$this->baseUrl}/categories/{$id}", $payload);

        return $response->json();
    }

    /**
     * Xóa category
     */
    public function deleteCategory($id)
    {
        $response = Http::withHeaders($this->headers())->delete("{$this->baseUrl}/categories/{$id}");

        return $response->json();
    }

    /**
     * Chuyển dữ liệu từ form thành payload gửi API
     */
    protected function mapPayload($data)
    {
        return [
            'name' => $data['name'] ?? '',
            'parentId' => $data['parentId'] ?? null, // null nếu category gốc
        ];
    }

}
