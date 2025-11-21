<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AddCartService;
use App\Services\ApiClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AddCartController extends Controller
{
    protected $addCartService;
    protected $api;

    public function __construct(AddCartService $addCartService, ApiClientService $api)
    {
        $this->addCartService = $addCartService;
        $this->api = $api;
    }

    public function add(Request $request)
    {
        // 1. Kiểm tra đăng nhập
        if (!session('user')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để mua hàng',
                'redirect' => route('login')
            ], 401);
        }

        // 2. Lấy dữ liệu sản phẩm
        $product = $request->input('product_json');
        Log::info('AddCart Request:', ['product' => $product]);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu sản phẩm lỗi'], 400);
        }

        try {
            // 3. LẤY GIỎ HÀNG TỪ API (THAY VÌ SESSION)
            $cartResponse = $this->api->get("cart");
            $cart = $cartResponse['data'] ?? [];

            $id = $product['id'] ?? $product['_id'];
            $found = false;

            // Tìm xem có trong giỏ chưa
            foreach ($cart as &$item) {
                if ($item['productId'] == $id) {
                    $item['quantity'] += 1;
                    $found = true;
                    break;
                }
            }

            // Nếu chưa có, thêm mới
            if (!$found) {
                // Xử lý ảnh
                $img = 'https://via.placeholder.com/150';
                if (!empty($product['images'][0])) {
                    $img = is_array($product['images'][0]) ? $product['images'][0]['url'] : $product['images'][0];
                } elseif (!empty($product['image'])) {
                    $img = $product['image'];
                }

                $cart[] = [
                    'productId' => $id,
                    'name' => $product['name'] ?? $product['TenSP'] ?? 'Sản phẩm',
                    'price' => $product['price'] ?? $product['GiaBan'] ?? 0,
                    'quantity' => 1,
                    'image' => $img,
                    'variant' => null
                ];
            }

            // 4. ĐẨY GIỎ HÀNG LÊN API (QUAN TRỌNG NHẤT)
            $updateResponse = $this->addCartService->updateCart($cart);

            if (!($updateResponse['success'] ?? false)) {
                throw new \Exception($updateResponse['message'] ?? 'Không thể cập nhật giỏ hàng');
            }

            // 5. ĐỒNG BỘ LẠI SESSION TỪ API (để header cập nhật)
            session(['user.cart' => $cart]);
            session()->save();
            Log::info('Cart Updated via API:', ['count' => count($cart)]);

            return response()->json([
                'success' => true,
                'newCartCount' => count($cart),
                'message' => 'Đã thêm vào giỏ hàng'
            ]);

        } catch (\Exception $e) {
            Log::error('Cart Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}