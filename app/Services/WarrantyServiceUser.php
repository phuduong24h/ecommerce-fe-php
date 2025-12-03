<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WarrantyServiceUser
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.api.url') . '/api/v1';
        $this->token = session('user_token'); // token user
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Lấy claims của user
     */
    public function getMyClaims(): array
    {
        try {
            $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/warranty/me");
            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? [];
            }
            Log::error("API Get My Claims Failed: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("Lỗi lấy My Claims: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Chuẩn hóa payload để gửi claim
     */
    protected function mapPayload(array $data): array
    {
        return [
            'orderId' => $data['orderId'] ?? $data['order_id'] ?? null,
            'productId' => $data['productId'] ?? $data['product_id'] ?? null,
            'productName' => $data['productName'] ?? $data['product_name'] ?? null,
            'issueDesc' => $data['description'] ?? $data['issueDesc'] ?? '',
            'purchasedAt' => $data['purchasedAt'] ?? $data['purchased_at'] ?? null,
            'productSerial' => $data['productSerial'] ?? $data['serial_number'] ?? '',
            'images' => $data['images'] ?? []
        ];
    }
    /**
     * Submit warranty claim
     */
    public function submitClaim(array $data): array
    {
        try {
            $payload = $this->mapPayload($data);

            $response = Http::withHeaders($this->headers())
                ->post("{$this->baseUrl}/warranty/create", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("API Submit Claim Failed: " . $response->body());
            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Backend từ chối yêu cầu'
            ];
        } catch (\Exception $e) {
            Log::error("Lỗi submit claim: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    public function getAllPolicies()
    {
        try {
            // --- SỬA DÒNG NÀY ---
            // Cũ: /policies (SAI)
            // Mới: /warranty-policy (ĐÚNG theo file router backend bạn gửi)
            $response = Http::get("{$this->baseUrl}/warranty-policy");

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? [];
            }

            // Debug nếu lỗi
            Log::error("API Warranty Policy Failed: " . $response->body());
            return [];

        } catch (\Exception $e) {
            Log::error("Lỗi lấy Warranty Policy: " . $e->getMessage());
            return [];
        }
    }
}
