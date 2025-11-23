<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AddCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    protected AddCartService $cartService;

    public function __construct(AddCartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Hiển thị trang thanh toán
     */
    public function index()
    {
        // 1. Lấy Giỏ Hàng từ Service (đã bao gồm việc gọi API lấy cart từ DB)
        $cartRes = $this->cartService->getCart();
        $cart = ($cartRes['success'] ?? false) ? ($cartRes['data'] ?? []) : [];

        // Nếu giỏ trống, đá về trang giỏ hàng
        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống, vui lòng thêm sản phẩm!');
        }

        // 2. Lấy Thông Tin User để hiển thị form (Tên, SDT, Địa chỉ...)
        $userUrl = config('services.api.url') . '/api/v1/users/me';
        try {
            $userRes = Http::withToken(session('user_token'))->get($userUrl);
            $user = $userRes->json()['data'] ?? [];
        } catch (\Exception $e) {
            $user = [];
        }

        // 3. Tính toán tổng tiền hàng
        $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);

        return view('user.checkout.index', compact('cart', 'user', 'subtotal'));
    }

    /**
     * Xử lý đặt hàng (Gửi API sang Node.js)
     */
    public function submit(Request $request)
    {
        // 1. Lấy lại giỏ hàng để đảm bảo dữ liệu mới nhất (tránh hack giá ở frontend)
        $cartRes = $this->cartService->getCart();
        $cart = ($cartRes['success'] ?? false) ? ($cartRes['data'] ?? []) : [];

        if (empty($cart)) {
            return response()->json([
                "success" => false,
                "message" => "Giỏ hàng trống! Vui lòng tải lại trang."
            ]);
        }

        // 2. Tính toán lại tổng tiền
        $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);
        $total = $subtotal + 9.6; // Cộng phí vận chuyển/thuế cố định (như trong View)

        // 3. Chuẩn bị dữ liệu items gửi sang Backend
        $items = collect($cart)->map(fn ($i) => [
            "productId" => $i["productId"],
            "name" => $i["name"] ?? "Unknown", // Backend có thể cần tên để lưu snapshot
            "price" => $i["price"],
            "quantity" => $i["quantity"],
            "variant" => $i["variant"] ?? null,
        ])->toArray();

        // 4. Chuẩn bị Payload
        $payload = [
            "items" => $items,
            "totalAmount" => $total, // Gửi kèm tổng tiền để backend tham khảo
            "payment" => [
                "method" => $request->payment_method ?? 'CASH',
                "status" => "PENDING",
                "amount" => $total
            ],
            // Nếu bạn có form nhập địa chỉ riêng thì lấy từ $request->address
            "shipment" => [
                "address" => $request->address ?? "Địa chỉ mặc định của User", 
                "status" => "PENDING"
            ]
        ];

        // API URL Tạo đơn hàng
        $createOrderUrl = config('services.api.url') . '/api/v1/orders/create';

        try {
            // Gọi API tạo đơn
            $response = Http::withToken(session('user_token'))
                            ->post($createOrderUrl, $payload);

            $res = $response->json();

            // Kiểm tra kết quả
            if ($response->successful() && ($res['success'] ?? false)) {
                
                // =================================================================
                // 🔴 BƯỚC 5 QUAN TRỌNG: GỌI API XÓA GIỎ HÀNG THỦ CÔNG
                // =================================================================
                // Do Backend (orders.ts) không tự xóa giỏ hàng, ta gọi thêm API này
                // để set giỏ hàng trong Database về rỗng [].
                
                $clearCartUrl = config('services.api.url') . '/api/v1/users/me/cart';
                
                try {
                    Http::withToken(session('user_token'))
                        ->put($clearCartUrl, ['cart' => []]);
                } catch (\Exception $ex) {
                    // Nếu xóa giỏ thất bại cũng không sao, đơn đã tạo rồi. 
                    // Có thể log lại lỗi này nếu cần.
                }

                // Xóa session giỏ hàng phía Laravel
                session()->forget('user.cart'); 
                
                return response()->json([
                    'success' => true,
                    'message' => 'Đặt hàng thành công!',
                    'orderId' => $res['data']['id'] ?? null
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => $res['message'] ?? 'Lỗi tạo đơn hàng từ hệ thống'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Lỗi kết nối: ' . $e->getMessage()
            ]);
        }
    }
}