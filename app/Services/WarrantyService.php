<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WarrantyService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        // Đặt baseUrl đến /api/v1/admin/warranty để các request sau chỉ cần thêm /claims hoặc /claims/{id}
        $this->baseUrl = config('services.api.url') . '/api/v1/admin/warranty';
        $this->token = config('services.api.token');
    }

    /**
     * Headers mặc định cho mọi request
     */
    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];
    }

    /**
     * Lấy danh sách claim, có thể lọc theo status
     */
    public function getClaims(?string $status = null): array
    {
        $query = $status ? ['status' => $status] : [];

        try {
            $response = Http::withHeaders($this->headers())
                            ->get("{$this->baseUrl}/claims", $query);

            // Log debug
            Log::info('Fetching claims', ['url' => "{$this->baseUrl}/claims", 'query' => $query, 'response' => $response->body()]);

            if ($response->failed()) {
                Log::error('API Error (getClaims): ' . $response->body());
                return [];
            }

            $json = $response->json();

            if (!empty($json['success']) && $json['success'] === true) {
                $data = $json['data'];
                return is_array($data) ? $data : (array) $data;
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Exception (getClaims): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chi tiết một claim theo ID
     */
    public function getClaimById(string $id): ?array
    {
        try {
            $response = Http::withHeaders($this->headers())
                            ->get("{$this->baseUrl}/claims/{$id}");

            Log::info('Fetching claim by ID', ['id' => $id, 'response' => $response->body()]);

            if ($response->failed()) {
                Log::error('API Error (getClaimById): ' . $response->body());
                return null;
            }

            $json = $response->json();
            if (!empty($json['success']) && $json['success'] === true) {
                $data = $json['data'];
                return is_array($data) ? $data : (array) $data;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception (getClaimById): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật claim: status hoặc note
     */
    public function updateClaim(string $id, ?string $status = null, ?string $note = null): ?array
    {
        $payload = [];
        if ($status) $payload['status'] = $status;
        if ($note) $payload['note'] = $note;

        try {
            $response = Http::withHeaders($this->headers())
                            ->put("{$this->baseUrl}/claims/{$id}", $payload);

            Log::info('Updating claim', ['id' => $id, 'payload' => $payload, 'response' => $response->body()]);

            if ($response->failed()) {
                Log::error('API Error (updateClaim): ' . $response->body());
                return null;
            }

            $json = $response->json();
            if (!empty($json['success']) && $json['success'] === true) {
                $data = $json['data'];
                return is_array($data) ? $data : (array) $data;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception (updateClaim): ' . $e->getMessage());
            return null;
        }
    }
}
