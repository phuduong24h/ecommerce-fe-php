<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    // Trang chính - mặc định hiển thị đơn hàng
    public function index()
    {
        return $this->orders();
    }

    // Tab: Đơn Hàng Của Tôi
    public function orders()
    {
        // Dữ liệu mẫu - thay bằng dữ liệu thật từ database
        $orders = [
            [
                'id' => 'ORD-001',
                'status' => 'delivered',
                'status_text' => 'Đã Giao',
                'date' => '2025-10-28',
                'total' => 159.98,
                'items' => [
                    ['name' => 'Wireless Mouse', 'quantity' => 2, 'price' => 59.98],
                    ['name' => 'Monitor 27"', 'quantity' => 1, 'price' => 299.99],
                ]
            ],
            [
                'id' => 'ORD-002',
                'status' => 'pending',
                'status_text' => 'Đã Gửi',
                'date' => '2025-10-25',
                'total' => 89.99,
                'items' => [
                    ['name' => 'Mechanical Keyboard', 'quantity' => 1, 'price' => 89.99],
                ]
            ],
            [
                'id' => 'ORD-003',
                'status' => 'delivered',
                'status_text' => 'Đã Giao',
                'date' => '2025-10-20',
                'total' => 45.99,
                'items' => [
                    ['name' => 'Laptop Stand', 'quantity' => 1, 'price' => 45.99],
                ]
            ],
        ];

        return view('user.account.index', [
            'activeTab' => 'orders',
            'orders' => $orders
        ]);
    }

    // Tab: Bảo Hành Của Tôi
    public function warranty()
    {
        // Dữ liệu mẫu
        $warranties = [
            [
                'product_name' => 'Wireless Mouse',
                'serial_number' => 'SN-12345-ABCD',
                'purchase_date' => '2024-05-15',
                'warranty_end' => '2026-05-15',
                'status' => 'active',
                'status_text' => 'Đang Hoạt Động'
            ],
            [
                'product_name' => 'Mechanical Keyboard',
                'serial_number' => 'SN-67890-EFGH',
                'purchase_date' => '2023-08-20',
                'warranty_end' => '2025-08-20',
                'status' => 'active',
                'status_text' => 'Đang Hoạt Động'
            ],
        ];

        return view('user.account.index', [ // ← Sửa đây
            'activeTab' => 'warranty',
            'warranties' => $warranties
        ]);
    }

    // Tab: Hồ Sơ
    public function profile()
    {
        // Dữ liệu mẫu
        $user = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '(555) 123-4567',
            'address' => '123 Đường Chính, Thành Phố, 12345',
            'member_since' => 'Tháng 1, 2024'
        ];

        return view('user.account.index', [ // ← Sửa đây
            'activeTab' => 'profile',
            'user' => $user
        ]);
    }
}