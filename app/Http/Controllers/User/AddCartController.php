<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AddCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Thư viện xử lý ngày giờ

class AddCartController extends Controller
{
    protected $addCartService;

    public function __construct(AddCartService $addCartService)
    {
        $this->addCartService = $addCartService;
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
        $selectedVariant = $product['selected_variant'] ?? null;

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu lỗi'], 400);
        }

        try {
            // Lấy giỏ hàng hiện tại
            $cartResponse = $this->addCartService->getCart();
            $cart = $cartResponse['data'] ?? [];

            // Tạo ID duy nhất
            $productId = $product['id'] ?? $product['_id'];
            $cartItemId = $selectedVariant
                ? $productId . '_' . $selectedVariant['value']
                : $productId;

            // ====================================================
            // 🔴 1. TÍNH TOÁN GIÁ CUỐI CÙNG (BAO GỒM KHUYẾN MÃI)
            // ====================================================
            $basePrice = $product['price'] ?? 0;
            $variantPrice = $selectedVariant ? ($selectedVariant['price'] ?? 0) : 0;
            
            // Giá trước khi giảm (Giá gốc + Giá biến thể)
            $finalPrice = $basePrice + $variantPrice;

            // Kiểm tra Promotion
            if (!empty($product['promotion'])) {
                $promo = $product['promotion'];
                
                // Kiểm tra xem khuyến mãi có đang chạy không
                $now = Carbon::now();
                $start = Carbon::parse($promo['startDate']);
                $end = Carbon::parse($promo['endDate']);
                $isActive = $promo['isActive'] ?? false;

                if ($isActive && $now->between($start, $end)) {
                    $discountPercent = floatval($promo['discount']); // VD: 15
                    
                    // Áp dụng giảm giá: Giá = Giá cũ * (100 - %)/100
                    $finalPrice = $finalPrice * ((100 - $discountPercent) / 100);
                }
            }
            // ====================================================

            // ====================================================
            // 🔴 2. XÁC ĐỊNH TỒN KHO
            // ====================================================
            $currentStock = 0;
            if ($selectedVariant) {
                $currentStock = $selectedVariant['stock'] ?? 0;
            } else {
                $currentStock = $product['stock'] ?? 0;
            }

            if ($currentStock <= 0) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm đã hết hàng!'], 400);
            }
            // ====================================================

            $found = false;

            // Duyệt giỏ hàng để tìm sản phẩm trùng
            foreach ($cart as &$item) {
                $itemVariant = $item['variant'] ?? null;
                $reqVariant = $selectedVariant ? $selectedVariant['value'] : null;

                if ($item['productId'] == $productId && $itemVariant == $reqVariant) {
                    
                    // Chặn nếu cộng thêm sẽ vượt quá kho
                    if (($item['quantity'] + 1) > $currentStock) {
                        return response()->json([
                            'success' => false, 
                            'message' => "Kho chỉ còn $currentStock sản phẩm. Không thể thêm tiếp!"
                        ], 400);
                    }

                    $item['quantity'] += 1;
                    $item['stock'] = $currentStock; // Cập nhật lại stock mới nhất
                    $item['price'] = $finalPrice;   // Cập nhật lại giá (phòng khi giá thay đổi)
                    
                    $found = true;
                    break;
                }
            }

            // Nếu chưa có thì thêm mới
            if (!$found) {
                $img = 'https://via.placeholder.com/150';
                if (!empty($product['images'][0])) {
                    $img = is_array($product['images'][0]) ? $product['images'][0]['url'] : $product['images'][0];
                } elseif (!empty($product['image'])) {
                    $img = $product['image'];
                }

                $cart[] = [
                    'productId' => $productId,
                    'name' => $product['name'] ?? 'Sản phẩm',
                    'price' => $finalPrice, // Lưu giá ĐÃ GIẢM
                    'quantity' => 1,
                    'image' => $img,
                    'variant' => $selectedVariant ? $selectedVariant['value'] : null,
                    'stock' => $currentStock // Lưu tồn kho để check sau này
                ];
            }

            // Cập nhật qua API Backend
            $updateResponse = $this->addCartService->updateCart($cart);

            if (!($updateResponse['success'] ?? false)) {
                throw new \Exception($updateResponse['message'] ?? 'Lỗi cập nhật giỏ hàng');
            }

            // Lưu session
            session(['user.cart' => $updateResponse['data']]);
            session()->save();

            return response()->json([
                'success' => true,
                'newCartCount' => count($cart),
                'message' => 'Đã thêm vào giỏ hàng'
            ]);

        } catch (\Exception $e) {
            Log::error('Cart Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }
}