<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AddCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AddCartController extends Controller
{
    protected $addCartService;

    public function __construct(AddCartService $addCartService)
    {
        $this->addCartService = $addCartService;
    }

    /**
     * API Endpoint cho AJAX để thêm sản phẩm vào giỏ
     * Route: POST /cart/add
     */
    public function add(Request $request)
    {
        // 1. Kiểm tra xem user đã đăng nhập chưa
        if (!session('user_token')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để thêm vào giỏ hàng.',
                'redirect' => route('login') // Báo cho JS chuyển hướng
            ], 401);
        }

        // 2. Lấy thông tin sản phẩm từ request
        $product = $request->input('product_json');
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Thiếu thông tin sản phẩm.'], 400);
        }

        try {
            // 3. Lấy giỏ hàng hiện tại từ session (cho nhanh) hoặc từ API
            // Lấy từ session mà ta đã lưu khi đăng nhập
            $cartArray = session('user.cart', []);

            $productId = $product['id'] ?? $product['_id'];
            $existingItemIndex = -1;
            foreach ($cartArray as $index => $item) {
                if ($item['productId'] == $productId) {
                    $existingItemIndex = $index;
                    break;
                }
            }

            // 4. Logic thêm/cập nhật giỏ hàng
            if ($existingItemIndex > -1) {
                // Sản phẩm đã có, tăng số lượng
                $cartArray[$existingItemIndex]['quantity'] += 1;
            } else {
                $imageUrl = 'default.jpg'; // Ảnh mặc định

                // 1. Thử lấy 'HinhAnh' (key cũ)
                if (!empty($product['HinhAnh'])) {
                    $imageUrl = $product['HinhAnh'];
                }
                // 2. Thử lấy 'image' (key trong app.js)
                elseif (!empty($product['image'])) {
                    $imageUrl = $product['image'];
                }
                // 3. Thử lấy mảng 'images' (key trong app.js)
                elseif (!empty($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
                    $firstImage = $product['images'][0];
                    if (is_array($firstImage) && !empty($firstImage['url'])) {
                        $imageUrl = $firstImage['url']; // Dạng [{ "url": "..." }]
                    } elseif (is_string($firstImage)) {
                        $imageUrl = $firstImage; // Dạng ["...", "..."]
                    }
                }
                // === KẾT THÚC SỬA LỖI ===

                $cartArray[] = [
                    'productId' => $productId,
                    'quantity' => 1,
                    'name' => $product['TenSP'] ?? $product['name'] ?? 'Sản phẩm',
                    'price' => $product['GiaBan'] ?? $product['price'] ?? 0,
                    'image' => $imageUrl // <-- ĐÃ SỬA LẠI ĐÚNG
                ];
            }

            // 5. Đồng bộ giỏ hàng mới lên backend
            $result = $this->addCartService->updateCart($cartArray);

            if (!$result['success']) {
                return response()->json($result, 500);
            }

            // 6. Lưu giỏ hàng mới vào session
            $updatedCart = $result['data'];
            session(['user.cart' => $updatedCart]);

            // 7. Trả về số lượng mới cho JS
            return response()->json([
                'success' => true,
                'newCartCount' => count($updatedCart)
            ]);

        } catch (\Exception $e) {
            Log::error('Cart Add Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống khi thêm giỏ hàng.'], 500);
        }
    }
}
