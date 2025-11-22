<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AddCartService;
use Illuminate\Support\Facades\Http; // <--- QUAN TRỌNG: Thêm dòng này để gọi API tạo đơn


// Kim Hải
class CartController extends Controller
{
    protected AddCartService $cartService;

    public function __construct(AddCartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $res = $this->cartService->getCart();
        
        if (!($res['success'] ?? false)) {
            $cart = [];
        } else {
            $cart = $res['data'] ?? [];
        }

        $subtotal = collect($cart)->sum(
            fn($i) => $i['price'] * $i['quantity']
        );

        return view('user.cart.index', compact('cart', 'subtotal'));
    }

    public function update(Request $request)
    {
        $index = intval($request->index);
        $qty   = intval($request->qty);

        $res = $this->cartService->getCart();
        $cart = $res['success'] ? ($res['data'] ?? []) : [];

        if (!isset($cart[$index])) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm'], 400);
        }

        $cart[$index]['quantity'] = $qty;

        $res2 = $this->cartService->updateCart($cart);

        if (!($res2['success'] ?? false)) {
            return response()->json(['success' => false, 'message' => 'Lỗi cập nhật'], 400);
        }

        $updatedCart = $res2['data'];
        if (!isset($updatedCart[$index])) {
             return response()->json(['success' => false], 400);
        }
        
        $item = $updatedCart[$index];
        $itemTotal = $item['price'] * $item['quantity'];
        $subtotal = collect($updatedCart)->sum(fn($i) => $i['price'] * $i['quantity']);

        $totalQty = count($updatedCart);

        return response()->json([
            'success'   => true,
            'item_total'=> '$' . number_format($itemTotal, 2),
            'subtotal'  => '$' . number_format($subtotal, 2),
            'total'     => '$' . number_format($subtotal + 9.6, 2), // + Thuế/Ship
            'cart_count' => $totalQty // <--- Trả về tổng số lượng thực tế
        ]);
    }

    public function remove(Request $request)
    {
        $index = intval($request->index);

        $res = $this->cartService->getCart();
        $cart = $res['success'] ? ($res['data'] ?? []) : [];

        if (!isset($cart[$index])) {
            return response()->json(['success' => false, 'message' => 'Item không tồn tại'], 400);
        }

        array_splice($cart, $index, 1);

        $res2 = $this->cartService->updateCart($cart);

        if (!($res2['success'] ?? false)) {
            return response()->json(['success' => false, 'message' => 'Lỗi xóa'], 400);
        }

        $updatedCart = $res2['data'];
        $subtotal = collect($updatedCart)->sum(fn($i) => $i['price'] * $i['quantity']);
        
        session(['user.cart' => $updatedCart]);

        $totalQty = collect($updatedCart)->sum('quantity');

        return response()->json([
            'success'   => true,
            'item_count' => count($updatedCart),
            'subtotal'  => '$' . number_format($subtotal, 2),
            'total'     => '$' . number_format($subtotal + 9.6, 2),
            'cart_count' => $totalQty
        ]);
    }

    public function checkout()
    {
        $res = $this->cartService->getCart();
        $cart = $res['success'] ? ($res['data'] ?? []) : [];

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống!');
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        return view('user.checkout.index', [
            'cart' => $cart,
            'subtotal' => $subtotal
        ]);
    }

    // --- PHẦN ĐÃ SỬA LẠI ---
    public function submitOrder(Request $request)
    {
        $address = $request->address;

        // 1. Lấy cart bằng Service chuẩn (thay vì $this->api->get)
        $res = $this->cartService->getCart();
        $cart = $res['success'] ? ($res['data'] ?? []) : [];

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống!');
        }

        // 2. Chuẩn bị payload
        $payload = [
            "items" => array_map(function ($i) {
                return [
                    "productId" => $i['productId'],
                    "name"      => $i['name'],
                    "quantity"  => $i['quantity'],
                    "price"     => $i['price']
                ];
            }, $cart),

            "payment" => [
                "method" => "CASH",
                "status" => "PENDING",
                "amount" => array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart))
            ],

            "shipment" => [
                "address" => $address,
                "status" => "PENDING"
            ],

            "totalAmount" =>
                array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart)) + 9.6
        ];

        // 3. Gọi API Orders bằng Http Facade (để kèm User Token)
        // Config URL của bạn là localhost:3000, cần nối thêm /api/v1/orders
        $url = config('services.api.url') . '/api/v1/orders';

        try {
            $response = Http::withToken(session('user_token'))
                            ->post($url, $payload);

            $json = $response->json();

            if ($response->failed() || !($json['success'] ?? false)) {
                return back()->with('error', $json['message'] ?? 'Không thể tạo đơn hàng');
            }

            // Xóa giỏ hàng trong session sau khi order thành công (Backend tự xóa trong DB)
            session()->forget('user.cart');

            return redirect()->route('account.orders')->with('success', 'Thanh toán thành công!');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi kết nối: ' . $e->getMessage());
        }
    }
}