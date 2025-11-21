<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

// Kim Hải
class AccountController extends Controller
{
    // Hàm helper để lấy Base URL đúng (đã thêm /api/v1)
    private function getBaseUrl()
    {
        return config('services.api.url') . '/api/v1';
    }

    // Hàm helper để gọi API GET kèm Token User
    private function getData($endpoint)
    {
        $url = $this->getBaseUrl() . '/' . $endpoint;
        
        try {
            $response = Http::withToken(session('user_token')) // <--- QUAN TRỌNG NHẤT
                            ->timeout(10)
                            ->get($url);

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? [];
            }
        } catch (\Exception $e) {
            // Log lỗi nếu cần thiết
        }

        return [];
    }

    public function index()
    {
        return $this->orders();
    }

    // 1. Lấy đơn hàng của tôi
    public function orders()
    {
        // Gọi API: /orders/me
        $orders = $this->getData('orders/me');

        return view('user.account.index', [
            'activeTab' => 'orders',
            'orders' => $orders
        ]);
    }

    // 2. Lấy danh sách bảo hành của tôi
    public function warranty()
    {
        // Gọi API: /warranty/me
        $warranties = $this->getData('warranty/me');

        return view('user.account.index', [
            'activeTab' => 'warranty',
            'warranties' => $warranties
        ]);
    }

    // 3. Lấy thông tin profile của user
    public function profile()
    {
        // Gọi API: /users/me
        $user = $this->getData('users/me');

        // Xử lý hiển thị ngày tham gia
        if (!empty($user['createdAt'])) {
            $timestamp = strtotime($user['createdAt']);
            $user['member_since'] = 'Tháng ' . date('n', $timestamp) . ', ' . date('Y', $timestamp);
        } else {
            $user['member_since'] = 'Thành viên mới';
        }

        // Fallback dữ liệu nếu API trả về thiếu
        $user['name'] = $user['name'] ?? 'Người dùng';
        $user['email'] = $user['email'] ?? session('user.email'); // Lấy tạm từ session nếu API lỗi

        return view('user.account.index', [
            'activeTab' => 'profile',
            'user' => $user
        ]);
    }
}