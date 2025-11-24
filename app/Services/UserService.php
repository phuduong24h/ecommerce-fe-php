<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.api.url') . '/api/v1/admin';
        // $this->token = config('services.api.token');
        $this->token = session('admin_token');
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];
    }

    public function getAll($search = null)
    {
        $url = "{$this->baseUrl}/customers";
        if ($search) $url .= '?search=' . urlencode($search);

        $response = Http::withHeaders($this->headers())->get($url);

        if ($response->failed()) {
            Log::error('API Error (getAllUsers): ' . $response->body());
            return collect();
        }

        $json = $response->json();
        $data = $json['success'] ?? false ? ($json['data'] ?? []) : [];
        return collect($data)->map(fn($item) => (object) $item);
    }

    public function getById($id)
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/customers/{$id}");

        if ($response->failed()) {
            Log::error("API Error (getById: {$id}): " . $response->body());
            return null;
        }

        $json = $response->json();
        $data = $json['success'] ?? false ? ($json['data'] ?? null) : null;
        return $data ? (object) $data : null;
    }

    public function create(array $data)
    {
        $payload = $this->mapPayload($data);
        Log::info('Creating user payload:', $payload);

        $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/customers", $payload);
        $json = $response->json();

        Log::info('Create user response:', $json);

        return $json;
    }

    public function update(string $id, array $data)
    {
        // Chuyển role sang UPPERCASE nếu có
        if (!empty($data['role'])) {
            $data['role'] = strtoupper($data['role']);
        }

        $payload = $this->mapPayload($data);
        Log::info("Updating user {$id} payload:", $payload);

        $response = Http::withHeaders($this->headers())->put("{$this->baseUrl}/customers/{$id}", $payload);
        $json = $response->json();

        Log::info("Update user {$id} response:", $json);

        return $json;
    }

    public function delete(string $id)
    {
        $response = Http::withHeaders($this->headers())->delete("{$this->baseUrl}/customers/{$id}");
        $json = $response->json();

        Log::info("Delete user {$id} response:", $json);

        return $json;
    }

    protected function mapPayload(array $data): array
    {
        $payload = [];

        foreach (['name', 'email', 'phone', 'address', 'role'] as $field) {
            if (!empty($data[$field])) {
                $payload[$field] = $data[$field];
            }
        }

        if (!empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        return $payload;
    }
}
