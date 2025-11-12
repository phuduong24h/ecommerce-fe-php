<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CenterService
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
     * Lấy tất cả service center
     */
    public function getAllCenters()
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/service-center");

        if ($response->failed()) {
            Log::error('API Error (getAllCenters): ' . $response->body());
            return [];
        }

        $json = $response->json();
        return $json['success'] ?? false ? $json['data'] : [];
    }

    /**
     * Tạo service center mới
     */
    public function createCenter($data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/service-center", $payload);

        return $response->json();
    }

    /**
     * Cập nhật service center
     */
    public function updateCenter($id, $data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->put("{$this->baseUrl}/service-center/{$id}", $payload);

        return $response->json();
    }

    /**
     * Xóa service center
     */
    public function deleteCenter($id)
    {
        $response = Http::withHeaders($this->headers())->delete("{$this->baseUrl}/service-center/{$id}");

        return $response->json();
    }

    /**
     * Chuyển dữ liệu từ form thành payload gửi API
     */
    protected function mapPayload($data)
    {
            return [
        'name'      => $data['name'] ?? '',           // bắt buộc
        'address'   => $data['address'] ?? '',        // bắt buộc
        'phone'     => $data['phone'] ?? null,        // nullable
        'email'     => $data['email'] ?? null,        // nullable
        'openHours' => $data['openHours'] ?? null,    // nullable
    ];
    }
}
