<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        // Dùng cùng token và baseUrl như ProductService
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
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];
    }

    /**
     * Lấy tất cả đơn hàng
     */
    public function getAllOrders()
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/orders");

        if ($response->failed()) {
            Log::error('API Error (getAllOrders): ' . $response->body());
            return [];
        }

        $json = $response->json();
        return $json['success'] ?? false ? $json['data'] : [];
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateOrderStatus($id, $status)
    {
        $payload = ['status' => $status];

        $response = Http::withHeaders($this->headers())->put("{$this->baseUrl}/orders/{$id}/status", $payload);

        if ($response->failed()) {
            Log::error("API Error (updateOrderStatus): OrderID={$id}, Body=" . $response->body());
        }

        return $response->json();
    }

    /**
     * Lấy chi tiết đơn hàng theo ID
     */
    public function getOrderById($id)
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/orders/{$id}");

        if ($response->failed()) {
            Log::error("API Error (getOrderById): OrderID={$id}, Body=" . $response->body());
            return null;
        }

        $json = $response->json();
        return $json['success'] ?? false ? $json['data'] : null;
    }
}
