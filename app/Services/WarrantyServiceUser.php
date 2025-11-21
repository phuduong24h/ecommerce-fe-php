<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WarrantyServiceUser
{
    protected $baseUrl;

    public function __construct()
    {
        // URL API Backend (http://localhost:3000/api/v1)
        $this->baseUrl = config('services.api.url');
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