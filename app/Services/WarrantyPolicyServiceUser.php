<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WarrantyPolicyServiceUser
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.api.url'), '/') . '/api/v1/warranty-policy';
        $this->token = session('user_token');
    }

    /**
     * Header chuẩn cho tất cả request
     */
    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * GET / - Lấy tất cả policy
     */
    public function getAllPolicies(): array
    {
        try {
            $response = Http::withHeaders($this->headers())->get($this->baseUrl);

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? [];
            }

            Log::error("API Get All Policies Failed: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("Lỗi lấy Warranty Policies: " . $e->getMessage());
            return [];
        }
    }

    /**
     * GET /{id} - Lấy policy theo ID
     */
    public function getPolicyById(string $id): ?array
    {
        try {
            $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/{$id}");

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? null;
            }

            Log::error("API Get Policy By ID Failed: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Lỗi lấy Warranty Policy: " . $e->getMessage());
            return null;
        }
    }
}
