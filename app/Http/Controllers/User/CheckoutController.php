<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AddCartService; // SỬA: Dùng Service này để lấy Cart chuẩn
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // SỬA: Dùng Http để gọi API User/Order

// Kim Hải
class CheckoutController extends Controller
{
    protected AddCartService $cartService;

    // Inject AddCartService thay vì ApiClientService
    public function __construct(AddCartService $cartService)
    {
        $this->cartService = $cartService;
    }

    // Hiển thị trang checkout
    public function index()
    {
        // 1. Lấy Giỏ Hàng (Dùng Service chuẩn có User Token)
        $cartRes = $this->cartService->getCart();
        $cart = ($cartRes['success'] ?? false) ? ($cartRes['data'] ?? []) : [];

        // Nếu giỏ trống, đá về trang giỏ hàng
        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống, vui lòng thêm sản phẩm!');
        }

        // 2. Lấy Thông Tin User (Dùng Http facade + Token Session)
        // URL: http://localhost:3000/api/v1/users/me
        $userUrl = config('services.api.url') . '/api/v1/users/me';
        
        try {
            $userRes = Http::withToken(session('user_token'))->get($userUrl);
            $user = $userRes->json()['data'] ?? [];
        } catch (\Exception $e) {
            $user = [];
        }

        // 3. Tính toán
        $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);

        return view('user.checkout.index', compact('cart', 'user', 'subtotal'));
    }

    // Gửi order sang server NodeJS
    public function submit(Request $request)
    {
        // 1. Lấy lại giỏ hàng để đảm bảo dữ liệu mới nhất
        $cartRes = $this->cartService->getCart();
        $cart = ($cartRes['success'] ?? false) ? ($cartRes['data'] ?? []) : [];

        if (empty($cart)) {
            return response()->json([
                "success" => false,
                "message" => "Giỏ hàng trống! Vui lòng tải lại trang."
            ]);
        }

        $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);
        $total = $subtotal + 9.6; // Cộng phí vận chuyển/thuế nếu có

        // 2. Convert cart → order items (Format Backend yêu cầu)
        $items = collect($cart)->map(fn ($i) => [
            "productId" => $i["productId"],
            "name" => $i["name"], // Backend cần field này nếu snapshot
            "price" => $i["price"],
            "quantity" => $i["quantity"],
            // "variant" => $i["variant"] ?? null, // Bỏ comment nếu backend cần
        ])->toArray();

        $payload = [
            "items" => $items,
            "payment" => [
                "method" => $request->payment_method ?? 'CASH', // Default CASH nếu null
                "status" => "PENDING",
                "amount" => $total
            ],
            "shipment" => [
                "address" => $request->address ?? "Địa chỉ mặc định", // Cần lấy từ form nếu có
                "status" => "PENDING"
            ],
            "totalAmount" => $total
        ];

        // 3. Gửi API tạo đơn hàng (Kèm Token User)
        $orderUrl = config('services.api.url') . '/api/v1/orders';

        try {
            $response = Http::withToken(session('user_token'))
                            ->post($orderUrl, $payload);

            $res = $response->json();

            // 4. Xử lý kết quả
            if ($response->successful() && ($res['success'] ?? false)) {
                
                // Quan trọng: Xóa giỏ hàng trong Session PHP sau khi mua thành công
                // (Backend NodeJS thường tự xóa trong DB, nhưng Frontend cần cập nhật UI)
                session()->forget('user.cart'); 
                
                return response()->json([
                    'success' => true,
                    'message' => 'Đặt hàng thành công!',
                    'orderId' => $res['data']['id'] ?? null
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => $res['message'] ?? 'Lỗi tạo đơn hàng từ Backend'
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