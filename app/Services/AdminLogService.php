<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminLogService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.api.url') . '/api/v1/admin';
        // $this->token = config('services.api.token');
        $this->token = session('admin_token');
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
     * Lấy tất cả admin log
     */
    public function getAllLogs()
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/admin-logs");

        if ($response->failed()) {
            Log::error('API Error (getAllLogs): ' . $response->body());
            return [];
        }

        $json = $response->json();
        return $json['success'] ?? false ? $json['data'] : [];
    }

    /**
     * Tạo log mới
     */
    public function createLog($data)
    {
        $payload = $this->mapPayload($data);

        $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/admin-logs", $payload);

        return $response->json();
    }

    /**
     * Xóa log
     */
    public function deleteLog($id)
    {
        $response = Http::withHeaders($this->headers())->delete("{$this->baseUrl}/admin-logs/{$id}");

        return $response->json();
    }

    /**
     * Chuyển dữ liệu từ form thành payload gửi API
     */
    protected function mapPayload($data)
{
    return [
        'adminId'   => $data['adminId'] ?? '',      // id của admin thực hiện
        'action'    => $data['action'] ?? '',       // hành động
        'target'    => $data['target'] ?? null,     // đối tượng tác động (vd: 'Category', 'Promotion')
        'details'   => $data['details'] ?? null,    // mô tả chi tiết
        'createdAt' => $data['createdAt'] ?? now(), // ngày tạo
    ];
}

}
