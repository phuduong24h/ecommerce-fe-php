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
    //protected $api;

    public function __construct(AddCartService $addCartService, ApiClientService $api)
    {
        $this->addCartService = $addCartService;
        //$this->api = $api;
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

        // 🟢 NHẬN THÔNG TIN VARIANT ĐƯỢC CHỌN
        // Nếu client gửi lên variant đã chọn thì lấy, không thì null
        $selectedVariant = $product['selected_variant'] ?? null;

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu sản phẩm lỗi'], 400);
        }

        try {
            // 3. LẤY GIỎ HÀNG TỪ API (THAY VÌ SESSION)
            $cartResponse = $this->addCartService->getCart();
            $cart = $cartResponse['data'] ?? [];

            // 🟢 TẠO ID DUY NHẤT CHO CART ITEM
            // Nếu sản phẩm có variant, ID trong giỏ sẽ là "ID_Sản_Phẩm" + "Variant_Value"
            // Để phân biệt iPhone Đen và iPhone Trắng là 2 dòng khác nhau
            $productId = $product['id'] ?? $product['_id'];
            $cartItemId = $selectedVariant
                ? $productId . '_' . $selectedVariant['value']
                : $productId;

            $found = false;

            // Tìm xem có trong giỏ chưa
            foreach ($cart as &$item) {
                // So sánh theo CartItemId tự tạo (hoặc so sánh cả id và variant value)
                $itemVariant = $item['variant'] ?? null;
                $reqVariant = $selectedVariant ? $selectedVariant['value'] : null;

                if ($item['productId'] == $productId && $itemVariant == $reqVariant) {
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
                // 🟢 LOGIC TÍNH GIÁ MỚI (CỘNG DỒN)
                $basePrice = $product['price'] ?? $product['GiaBan'] ?? 0;
                $variantPrice = $selectedVariant ? ($selectedVariant['price'] ?? 0) : 0;

                $finalPrice = $basePrice + $variantPrice;

                $cart[] = [
                    'productId' => $productId,
                    'name' => $product['name'] ?? 'Sản phẩm',
                    'price' => $finalPrice, // Lưu tổng tiền
                    'quantity' => 1,
                    'image' => $img,
                    'variant' => $selectedVariant ? $selectedVariant['value'] : null
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
