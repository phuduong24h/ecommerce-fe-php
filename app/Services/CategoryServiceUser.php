<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CategoryServiceUser
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        // API user endpoint
        $this->baseUrl = config('services.api.url') . '/api/v1';
        $this->token = session('admin_token'); // hoặc user_token tuỳ bạn
    }

    /**
     * Header chung
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
     * Lấy tất cả Category
     */
    public function getCategories()
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/categories");

            if ($response->failed()) {
                Log::error("API Error (getCategories): " . $response->body());
                return [];
            }

            $json = $response->json();

            return $json['success'] ?? false ? ($json['data'] ?? []) : [];

        } catch (\Exception $e) {
            Log::error("Exception (getCategories): " . $e->getMessage());
            return [];
        }
    }
}
