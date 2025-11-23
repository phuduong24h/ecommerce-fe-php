<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderServiceUser
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.api.url'), '/') . '/api/v1';
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
     * Lấy tất cả order của user
     */
    public function getMyOrders(): array
    {
        try {
            $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/orders/me");

            if ($response->failed()) {
                Log::error("API getMyOrders failed: " . $response->body());
                return [];
            }

            $json = $response->json();
            return $json['success'] ?? false ? $json['data'] : [];
        } catch (\Exception $e) {
            Log::error("Error getMyOrders: " . $e->getMessage());
            return [];
        }
    }

}
