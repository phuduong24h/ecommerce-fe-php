<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CategoryServiceUser;
use App\Services\InterfaceService;
use App\Services\WarrantyServiceUser; // <-- thêm service bảo hành
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $interfaceService;
    protected $warrantyService; // <-- thêm

    public function __construct(
        CategoryServiceUser $categoryServiceUser,
        InterfaceService $interfaceService,
        WarrantyServiceUser $warrantyService // <-- inject service
    ) {
        $this->categoryService = $categoryServiceUser;
        $this->interfaceService = $interfaceService;
        $this->warrantyService = $warrantyService;
    }
    public function show($id)
    {
        // 1. Lấy tất cả categories (dùng cho menu)
        $categories = $this->categoryService->getCategories();

        // 2. Tìm category hiện tại
        $category = collect($categories)->firstWhere('id', $id);
        if (!$category) {
            abort(404, 'Danh mục không tồn tại');
        }

        // 3. Lấy sản phẩm theo category id
        $productsResult = $this->interfaceService->getProducts([
            'categoryId' => $id,
            'page' => 1,
            'pageSize' => 100,
        ]);

        $products = $productsResult['success'] ? ($productsResult['data']['products'] ?? []) : [];

        // 4. Lấy danh sách chính sách bảo hành
        $policies = $this->warrantyService->getAllPolicies();

        // 5. Map warrantyPolicyId sang warranty_label & thêm logic giảm giá
        foreach ($products as &$product) {
            // --- Bảo hành ---
            $policyId = $product['warrantyPolicyId'] ?? null;
            $product['warranty_label'] = 'New';

            if ($policyId) {
                foreach ($policies as $p) {
                    $pId = $p['id'] ?? $p['_id'] ?? '';
                    if ($pId === $policyId) {
                        $days = intval($p['durationDays'] ?? 0);
                        if ($days >= 365 && $days % 365 == 0) {
                            $years = $days / 365;
                            $product['warranty_label'] = $years . ' Year' . ($years > 1 ? 's' : '');
                        } elseif ($days >= 30 && $days % 30 == 0) {
                            $months = $days / 30;
                            $product['warranty_label'] = $months . ' Months';
                        } else {
                            $product['warranty_label'] = $days . ' Days';
                        }
                        break;
                    }
                }
            }

            // --- Giảm giá ---
            $basePrice = $product['price'] ?? 0;
            $product['has_discount'] = false;
            $product['discount_percent'] = 0;
            $product['final_price'] = $basePrice;    // Giá hiển thị sau giảm
            $product['original_price'] = $basePrice; // Giá gốc (gạch ngang nếu có giảm)

            if (!empty($product['promotion'])) {
                $promo = $product['promotion'];
                $isActive = $promo['isActive'] ?? false;
                $now = now();
                $start = \Carbon\Carbon::parse($promo['startDate']);
                $end = \Carbon\Carbon::parse($promo['endDate']);

                if ($isActive && $now->between($start, $end)) {
                    $product['has_discount'] = true;
                    $product['discount_percent'] = $promo['discount'] ?? 0;
                    $product['final_price'] = $basePrice * ((100 - $product['discount_percent']) / 100);
                }
            }

            // --- Xử lý biến thể nếu có ---
            if (!empty($product['variants']) && count($product['variants']) > 0) {
                $variantPrice = $product['variants'][0]['price'] ?? 0;
                $product['original_price'] += $variantPrice;
                if ($product['has_discount']) {
                    $product['final_price'] = ($basePrice + $variantPrice) * ((100 - $product['discount_percent']) / 100);
                } else {
                    $product['final_price'] = $product['original_price'];
                }
            }
        }
        unset($product);

        // 6. Truyền sang view
        return view('user.interface.show', [
            'categories' => $categories,
            'products' => $products,
            'currentCategoryId' => $id,
            'categoryName' => $category['name'],
        ]);
    }

}
