<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WarrantyPolicyService
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
     * Lấy tất cả chính sách bảo hành
     */
    public function getAllPolicies()
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/warranty-policy");

        if ($response->failed()) {
            Log::error('API Error (getAllPolicies): ' . $response->body());
            return [];
        }

        $json = $response->json();
        return !empty($json['success']) && $json['success'] ? $json['data'] : [];
    }

    /**
     * Tạo chính sách bảo hành mới
     */
    public function createPolicy(array $data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/warranty-policy", $payload);

        return $response->json();
    }

    /**
     * Cập nhật chính sách bảo hành
     */
    public function updatePolicy($id, array $data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->put("{$this->baseUrl}/warranty-policy/{$id}", $payload);

        return $response->json();
    }

    /**
     * Xóa chính sách bảo hành
     */
    public function deletePolicy($id)
    {
        $response = Http::withHeaders($this->headers())->delete("{$this->baseUrl}/warranty-policy/{$id}");

        return $response->json();
    }

    /**
     * Chuyển dữ liệu từ form thành payload gửi API
     */
    protected function mapPayload(array $data)
    {
        return [
            'name' => $data['name'] ?? '',
            'durationDays' => isset($data['durationDays']) ? (int) $data['durationDays'] : 0,
            'coverage' => $data['coverage'] ?? '',
            'requiresSerial' => isset($data['requiresSerial']) ? (bool) $data['requiresSerial'] : false,
        ];
    }

}
