<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Trang danh sách đơn hàng
     */
    // public function index()
    // {
    //     // Lấy tất cả đơn hàng từ API
    //     $orders = $this->orderService->getAllOrders();

    //     return view('admin.orders.index', compact('orders'));
    // }
    public function index(Request $request)
    {
        // Lấy tất cả đơn hàng từ service
        $allOrders = collect($this->orderService->getAllOrders()); // ✅ convert sang Collection

        $perPage = 5; // số đơn hàng / trang
        $page = $request->get('page', 1); // trang hiện tại

        $paginatedOrders = new LengthAwarePaginator(
            $allOrders->forPage($page, $perPage), // slice dữ liệu
            $allOrders->count(),                  // tổng số đơn hàng
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()] // giữ query string
        );

        return view('admin.orders.index', [
            'orders' => $paginatedOrders
        ]);
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $status = $request->input('status');

        $response = $this->orderService->updateOrderStatus($id, $status);

        if ($response['success'] ?? false) {
            return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
        }

        return redirect()->back()->with('error', 'Cập nhật trạng thái đơn hàng thất bại.');
    }

    /**
     * Xem chi tiết đơn hàng (nếu cần)
     */
    public function show($id)
    {
        $order = $this->orderService->getOrderById($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Đơn hàng không tồn tại.');
        }

        return view('admin.orders.show', compact('order'));
    }
}
