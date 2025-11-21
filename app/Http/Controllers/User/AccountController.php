<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ApiClientService;

class AccountController extends Controller
{
    protected ApiClientService $api;

    public function __construct(ApiClientService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        return $this->orders();
    }

    // Lấy đơn hàng của tôi
    public function orders()
    {
        $res = $this->api->get('orders/me');

        $orders = (isset($res['success']) && $res['success'] === false)
            ? []
            : ($res['data'] ?? []);

        return view('user.account.index', [
            'activeTab' => 'orders',
            'orders' => $orders
        ]);
    }

    // Lấy danh sách bảo hành của tôi
    public function warranty()
    {
        $res = $this->api->get('warranty/me');

        $warranties = (isset($res['success']) && $res['success'] === false)
            ? []
            : ($res['data'] ?? []);

        return view('user.account.index', [
            'activeTab' => 'warranty',
            'warranties' => $warranties
        ]);
    }

    // Lấy thông tin profile của user
    public function profile()
{
    $res = $this->api->get('users/me');

    $user = (isset($res['success']) && $res['success'] === false)
        ? []
        : ($res['data'] ?? []);

    // Format ngày tham gia: Tháng X, Năm Y
    if (!empty($user['createdAt'])) {
        $timestamp = strtotime($user['createdAt']);
        $user['member_since'] = 'Tháng ' . date('n', $timestamp) . ', ' . date('Y', $timestamp);
    }

    return view('user.account.index', [
        'activeTab' => 'profile',
        'user' => $user
    ]);
}

}
