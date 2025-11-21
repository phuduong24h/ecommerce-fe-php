<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ApiClientService;

class CartController extends Controller
{
    protected ApiClientService $api;

    public function __construct(ApiClientService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $res = $this->api->get("cart");
        $cart = $res['data'] ?? [];

        $subtotal = collect($cart)->sum(
            fn($i) => $i['price'] * $i['quantity']
        );

        return view('user.cart.index', compact('cart', 'subtotal'));
    }

    public function update(Request $request)
    {
        $index = intval($request->index);
        $qty   = intval($request->qty);

        // 1. lấy cart hiện tại
        $res = $this->api->get("cart");
        $cart = $res["data"] ?? [];

        // 2. cập nhật số lượng
        if (!isset($cart[$index])) {
            return response()->json(['success' => false], 400);
        }

        $cart[$index]['quantity'] = $qty;

        // 3. gửi PUT toàn bộ cart lên Node
        $res2 = $this->api->put("cart", [
            'cart' => $cart
        ]);

        if (!($res2['success'] ?? false)) {
            return response()->json(['success' => false], 400);
        }

        // 4. tính toán lại và trả về
        $updatedCart = $res2['data'];
        $item = $updatedCart[$index];

        $itemTotal = $item['price'] * $item['quantity'];
        $subtotal = collect($updatedCart)->sum(fn($i) => $i['price'] * $i['quantity']);

        return response()->json([
            'success'   => true,
            'item_total'=> '$' . number_format($itemTotal, 2),
            'subtotal'  => '$' . number_format($subtotal, 2),
            'total'     => '$' . number_format($subtotal + 9.6, 2),
            'cart_count' => count($updatedCart)
        ]);
    }


    public function remove(Request $request)
    {
        $index = intval($request->index);

        // lấy giỏ
        $res = $this->api->get("cart");
        $cart = $res["data"] ?? [];

        if (!isset($cart[$index])) {
            return response()->json(['success' => false], 400);
        }

        // xoá item
        array_splice($cart, $index, 1);

        // update giỏ hàng mới
        $res2 = $this->api->put("cart", [
            'cart' => $cart
        ]);

        if (!($res2['success'] ?? false)) {
            return response()->json(['success' => false], 400);
        }

        $updatedCart = $res2['data'];
        $subtotal = collect($updatedCart)->sum(fn($i) => $i['price'] * $i['quantity']);

        return response()->json([
            'success'   => true,
            'item_count' => count($updatedCart),
            'subtotal'  => '$' . number_format($subtotal, 2),
            'total'     => '$' . number_format($subtotal + 9.6, 2),
            'cart_count' => count($updatedCart)
        ]);
    }
        public function checkout()
    {
        // lấy lại giỏ hàng
        $res = $this->api->get("cart");
        $cart = $res['data'] ?? [];

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống!');
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        return view('user.checkout.index', [
            'cart' => $cart,
            'subtotal' => $subtotal
        ]);
    }

    public function submitOrder(Request $request)
    {
        $address = $request->address;

        // lấy cart
        $res = $this->api->get("cart");
        $cart = $res['data'] ?? [];

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống!');
        }

        // chuẩn bị payload
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

        // gọi NodeJS tạo đơn hàng
        $res2 = $this->api->post("orders", $payload);

        if (!($res2['success'] ?? false)) {
            return back()->with('error', 'Không thể tạo đơn hàng');
        }

        return redirect()->route('account.orders')->with('success', 'Thanh toán thành công!');
    }

}
