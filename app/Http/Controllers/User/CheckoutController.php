<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ApiClientService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected ApiClientService $api;

    public function __construct(ApiClientService $api)
    {
        $this->api = $api;
    }

    // Hiển thị trang checkout
    public function index()
    {
        $cartRes = $this->api->get("cart");
        $userRes = $this->api->get("users/me");

        $cart = $cartRes["data"] ?? [];
        $user = $userRes["data"] ?? [];

        $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);

        return view('user.checkout.index', compact('cart', 'user', 'subtotal'));
    }

    // Gửi order sang server NodeJS
        public function submit(Request $request)
    {
        $cartRes = $this->api->get("cart");
        $cart = $cartRes["data"] ?? [];

        if (empty($cart)) {
            return response()->json([
                "success" => false,
                "message" => "Giỏ hàng trống!"
            ]);
        }

        $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);
        $total = $subtotal + 9.6;

        // 🔥 Convert cart → order items đúng format của Prisma
        $items = collect($cart)->map(fn ($i) => [
            "productId" => $i["productId"],
            "name" => $i["name"],
            "price" => $i["price"],
            "quantity" => $i["quantity"],
            "variant" => $i["variant"] ?? null,
            "productSnapshot" => null
        ])->toArray();

        $payload = [
            "items" => $items,
            "payment" => [
                "method" => $request->payment_method,
                "status" => "PENDING",
                "amount" => $subtotal
            ],
            "shipment" => [
                "address" => "Địa chỉ giao hàng",
                "status" => "PENDING"
            ],
            "totalAmount" => $total
        ];

        $res = $this->api->post("orders", $payload);

        return response()->json($res);
    }

}
