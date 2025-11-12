<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductSerialService
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
     * Lấy tất cả product serial
     */
    public function getAllSerials()
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/serial");

        if ($response->failed()) {
            Log::error('API Error (getAllSerials): ' . $response->body());
            return [];
        }

        $json = $response->json();
        return $json['success'] ?? false ? $json['data'] : [];
    }

    /**
     * Tạo product serial mới
     */
    public function createSerial($data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/serial", $payload);

        return $response->json();
    }

    /**
     * Cập nhật product serial
     */
    public function updateSerial($id, $data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->put("{$this->baseUrl}/serial/{$id}", $payload);

        return $response->json();
    }

    /**
     * Xóa product serial
     */
    public function deleteSerial($id)
    {
        $response = Http::withHeaders($this->headers())->delete("{$this->baseUrl}/serial/{$id}");

        return $response->json();
    }

    /**
     * Chuyển dữ liệu từ form thành payload gửi API
     */
    protected function mapPayload($data)
    {
        return [
            'productId'     => $data['productId'] ?? '',       // bắt buộc
            'serial'        => $data['serial'] ?? '',          // bắt buộc
            'soldToOrderId' => $data['soldToOrderId'] ?? null,
            'soldAt'        => $data['soldAt'] ?? null,
            'registeredBy'  => $data['registeredBy'] ?? null,
            'registeredAt'  => $data['registeredAt'] ?? null,
            'status'        => $data['status'] ?? 'active',   // default 'active'
        ];
    }
        public function getById($id)
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/serial/{$id}");

        if ($response->failed()) {
            Log::error('API Error (getById): ' . $response->body());
            return null;
        }

        $json = $response->json();
        return $json['success'] ?? false ? $json['data'] : null;
    }
}
