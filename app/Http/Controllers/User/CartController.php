<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AddCartService;
use App\Services\InterfaceService; // 🟢 Import quan trọng
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    protected AddCartService $cartService;
    protected InterfaceService $interfaceService; // 🟢 Khai báo

    public function __construct(AddCartService $cartService, InterfaceService $interfaceService)
    {
        $this->cartService = $cartService;
        $this->interfaceService = $interfaceService; // 🟢 Inject
    }

    public function index()
    {
        // 1. Lấy giỏ hàng (Dữ liệu có thể bị thiếu stock hoặc stock cũ)
        $res = $this->cartService->getCart();
        $cart = ($res['success'] ?? false) ? ($res['data'] ?? []) : [];

        // 2. 🟢 ĐỒNG BỘ LẠI STOCK MỚI NHẤT TỪ BACKEND
        foreach ($cart as &$item) {
            $productId = $item['productId'];
            // Gọi API lấy chi tiết sản phẩm để có stock chuẩn
            $productInfo = $this->interfaceService->getProductById($productId);

            // Nếu lấy được thông tin
            if (isset($productInfo['success']) && $productInfo['success']) {
                $pData = $productInfo['data'];
                
                // Nếu item là biến thể -> Lấy stock biến thể
                if (!empty($item['variant']) && !empty($pData['variants'])) {
                    foreach ($pData['variants'] as $v) {
                        if ($v['value'] === $item['variant']) {
                            $item['stock'] = $v['stock'] ?? 0;
                            break;
                        }
                    }
                } else {
                    // Sản phẩm thường
                    $item['stock'] = $pData['stock'] ?? 0;
                }
            } else {
                // Không lấy được thông tin (VD lỗi mạng) -> Set an toàn là 0 hoặc giữ nguyên
                $item['stock'] = $item['stock'] ?? 0; 
            }
        }
        unset($item); // Hủy tham chiếu

        // 3. Lưu lại stock mới vào Session để các hàm checkout dùng được
        session(['user.cart' => $cart]);

        // 4. Tính tổng tiền
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        return view('user.cart.index', compact('cart', 'subtotal'));
    }

    // --- Các hàm update, remove, checkout, submitOrder ... (GIỮ NGUYÊN NHƯ CŨ) ---
    public function update(Request $request)
    {
        $index = intval($request->index);
        $qty   = intval($request->qty);
        $res = $this->cartService->getCart();
        $cart = $res['success'] ? ($res['data'] ?? []) : [];

        if (!isset($cart[$index])) return response()->json(['success' => false], 400);

        // Check Stock từ Session (đã được update ở index)
        $stockLimit = $cart[$index]['stock'] ?? 0;
        if ($qty > $stockLimit) {
            return response()->json(['success' => false, 'message' => "Quá số lượng tồn kho! (Còn: $stockLimit)"], 400);
        }

        $cart[$index]['quantity'] = $qty;
        $this->cartService->updateCart($cart);
        
        // Trả kết quả
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $itemTotal = $cart[$index]['price'] * $qty;
        
        return response()->json([
            'success' => true,
            'item_total' => '$' . number_format($itemTotal, 2),
            'subtotal' => '$' . number_format($subtotal, 2),
            'total' => '$' . number_format($subtotal + 9.6, 2),
            'cart_count' => count($cart)
        ]);
    }

    public function remove(Request $request)
    {
        $index = intval($request->index);
        $res = $this->cartService->getCart();
        $cart = $res['success'] ? ($res['data'] ?? []) : [];
        
        if (isset($cart[$index])) {
            array_splice($cart, $index, 1);
            $this->cartService->updateCart($cart);
            session(['user.cart' => $cart]);
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        return response()->json([
            'success' => true,
            'item_count' => count($cart),
            'subtotal' => '$' . number_format($subtotal, 2),
            'total' => '$' . number_format($subtotal + 9.6, 2),
            'cart_count' => count($cart)
        ]);
    }

    public function checkout()
    {
        $res = $this->cartService->getCart();
        $cart = $res['success'] ? ($res['data'] ?? []) : [];
        if (empty($cart)) return redirect('/cart')->with('error', 'Giỏ trống');

        foreach ($cart as $item) {
            $stock = $item['stock'] ?? 0;
            if ($item['quantity'] > $stock) {
                return redirect('/cart')->with('error', "Sản phẩm '{$item['name']}' vượt quá tồn kho.");
            }
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        return view('user.checkout.index', [
            'cart' => $cart, 'subtotal' => $subtotal, 'user' => session('user')
        ]);
    }

    public function submitOrder(Request $request)
    {
        $res = $this->cartService->getCart();
        $cart = ($res['success'] ?? false) ? ($res['data'] ?? []) : [];
        if (empty($cart)) return redirect('/cart');

        foreach ($cart as $item) {
            if ($item['quantity'] > ($item['stock'] ?? 0)) {
                return back()->with('error', 'Một số sản phẩm đã hết hàng hoặc vượt quá số lượng.');
            }
        }

        $payload = [
            "items" => array_map(function ($i) {
                return [
                    "productId" => $i['productId'],
                    "name" => $i['name'],
                    "quantity" => $i['quantity'],
                    "price" => $i['price'],
                    "variant" => $i['variant'] ?? null
                ];
            }, $cart),
            "payment" => ["method" => $request->payment_method ?? "CASH", "status" => "PENDING", "amount" => 0],
            "shipment" => ["address" => $request->address ?? "Default", "status" => "PENDING"],
            "totalAmount" => collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']) + 9.6
        ];
        $payload['payment']['amount'] = $payload['totalAmount'];

        $url = config('services.api.url') . '/api/v1/orders/create';
        try {
            $response = Http::withToken(session('user_token'))->post($url, $payload);
            if ($response->successful() && $response->json()['success']) {
                session()->forget('user.cart');
                return redirect()->route('account.orders')->with('success', 'Thanh toán thành công!');
            }
            return back()->with('error', 'Lỗi tạo đơn hàng');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}