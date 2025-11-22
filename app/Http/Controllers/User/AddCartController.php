<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AddCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Thắng
class AddCartController extends Controller
{
    protected $addCartService;

    public function __construct(AddCartService $addCartService)
    {
        $this->addCartService = $addCartService;
    }

    public function add(Request $request)
    {
        if (!session('user')) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập',
                'redirect' => route('login')
            ], 401);
        }

        $product = $request->input('product_json');
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Lỗi dữ liệu'], 400);
        }

        try {
            // 1. Lấy giỏ hàng hiện tại từ API (quan trọng: dùng token user)
            $cartRes = $this->addCartService->getCart();
            
            // Nếu lỗi lấy giỏ (hoặc giỏ chưa có), ta khởi tạo mảng rỗng
            $cart = ($cartRes['success'] ?? false) ? ($cartRes['data'] ?? []) : [];

            // 2. Logic thêm sản phẩm
            $id = $product['id'] ?? $product['_id'];
            $found = false;

            foreach ($cart as &$item) {
                if ((string)$item['productId'] === (string)$id) {
                    $item['quantity'] += 1;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                 // Fallback ảnh
                $img = 'https://via.placeholder.com/150';
                if (!empty($product['images'][0])) {
                    $img = is_array($product['images'][0]) ? $product['images'][0]['url'] : $product['images'][0];
                } elseif (!empty($product['image'])) {
                    $img = $product['image'];
                }

                $cart[] = [
                    'productId' => $id,
                    'name' => $product['name'] ?? $product['TenSP'] ?? 'Product',
                    'price' => $product['price'] ?? $product['GiaBan'] ?? 0,
                    'quantity' => 1,
                    'image' => $img,
                    'variant' => null
                ];
            }

            // 3. Cập nhật ngược lên API
            $updateRes = $this->addCartService->updateCart($cart);

            if (!$updateRes['success']) {
                return response()->json(['success' => false, 'message' => 'Lỗi API: ' . $updateRes['message']], 500);
            }

            // 4. Lưu session để hiển thị header
            session(['user.cart' => $cart]);
            session()->save();

            // Mới: Đếm số phần tử
            $totalQty = count($cart); 

            return response()->json([
                'success' => true,
                'newCartCount' => $totalQty, // Trả về số loại sp (ví dụ: 2)
                'message' => 'Đã thêm vào giỏ hàng'
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}