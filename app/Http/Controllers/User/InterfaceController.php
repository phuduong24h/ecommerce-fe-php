<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\InterfaceService;
use App\Services\WarrantyServiceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Thắng
class InterfaceController extends Controller
{
    protected $interfaceService;
    protected $warrantyServiceUser;

    public function __construct(
        InterfaceService $interfaceService,
        WarrantyServiceUser $warrantyServiceUser,
    ) {
        $this->interfaceService = $interfaceService;
        $this->warrantyServiceUser = $warrantyServiceUser;
    }

    public function index(Request $request)
    {
        // 1. Lấy Sản Phẩm
        $params = [
            'search' => $request->input('search', ''),
            'categoryId' => $request->input('categoryId', ''),
            'page' => $request->input('page', 1),
            'pageSize' => 8,
        ];
        $productResult = $this->interfaceService->getProducts($params);

        // ========================================================
        // [MỚI] 1b. Lấy Danh sách Danh mục (Categories)
        // ========================================================
        $catResult = $this->interfaceService->getCategories();
        $categories = $catResult['success'] ? ($catResult['data'] ?? []) : [];

        // Tìm tên danh mục đang chọn để hiển thị ra label (UX tốt hơn)
        $currentCategoryName = 'Tất cả danh mục';
        if (!empty($params['categoryId'])) {
            foreach ($categories as $cat) {
                // So sánh ID (ép kiểu string cho chắc chắn)
                if ((string)$cat['id'] === (string)$params['categoryId']) {
                    $currentCategoryName = $cat['name'];
                    break;
                }
            }
        }

        // 2. Lấy Danh sách Bảo Hành
        $policies = $this->warrantyServiceUser->getAllPolicies();

        // 3. Tạo Map tra cứu (ÉP KIỂU STRING ĐỂ SO SÁNH CHÍNH XÁC)
        $policiesMap = [];
        foreach ($policies as $p) {
            // Lấy ID dù nó là 'id' hay '_id'
            $rawId = $p['id'] ?? $p['_id'] ?? '';
            // Ép sang chuỗi và xóa khoảng trắng thừa
            $strId = trim((string) $rawId);

            if ($strId !== '') {
                $policiesMap[$strId] = $p;
            }
        }

        // Xử lý dữ liệu sản phẩm
        $data = $productResult['success'] ? $productResult['data'] : [];
        $products = $data['products'] ?? [];

        // 4. Ghép dữ liệu (Mapping)
        foreach ($products as &$product) {
            // Lấy ID bảo hành từ sản phẩm
            $wIdRaw = $product['warrantyPolicyId'] ?? '';
            $wId = trim((string) $wIdRaw);

            $displayLabel = 'New'; // Mặc định

            // So sánh trong Map
            if ($wId !== '' && isset($policiesMap[$wId])) {
                $policy = $policiesMap[$wId];
                $days = intval($policy['durationDays']);

                // Logic đổi Ngày -> Năm/Tháng
                if ($days >= 365 && $days % 365 == 0) {
                    $years = $days / 365;
                    $displayLabel = $years . ' Year' . ($years > 1 ? 's' : '');
                } elseif ($days >= 30 && $days % 30 == 0) {
                    $months = $days / 30;
                    $displayLabel = $months . ' Months';
                } else {
                    $displayLabel = $days . ' Days';
                }
            }

            // Gán kết quả vào sản phẩm
            $product['warranty_label'] = $displayLabel;

            // --- B. LOGIC KHUYẾN MÃI (MỚI) ---
            $basePrice = $product['price'] ?? 0;
            
            // Mặc định không giảm
            $product['has_discount'] = false;
            $product['discount_percent'] = 0;
            $product['final_price'] = $basePrice; // Giá cuối cùng để hiển thị
            $product['original_price'] = $basePrice; // Giá gốc để gạch ngang

            // Kiểm tra xem có promotion object từ API không
            if (!empty($product['promotion'])) {
                $promo = $product['promotion'];
                
                // Kiểm tra active và ngày tháng
                $isActive = $promo['isActive'] ?? false;
                $now = Carbon::now();
                $start = Carbon::parse($promo['startDate']);
                $end = Carbon::parse($promo['endDate']);

                if ($isActive && $now->between($start, $end)) {
                    $product['has_discount'] = true;
                    $product['discount_percent'] = $promo['discount']; // Ví dụ: 15
                    
                    // Tính giá sau giảm: Giá gốc * (100 - 15) / 100
                    $product['final_price'] = $basePrice * ((100 - $promo['discount']) / 100);
                }
            }
            
            // Xử lý hiển thị giá biến thể ở trang chủ (Nếu có)
            if (!empty($product['variants']) && count($product['variants']) > 0) {
                // Nếu có biến thể, cộng thêm giá biến thể vào giá gốc
                $variantPrice = $product['variants'][0]['price'] ?? 0;
                $product['original_price'] += $variantPrice;
                
                // Nếu có giảm giá, tính trên tổng (Gốc + Biến thể)
                if ($product['has_discount']) {
                    $product['final_price'] = ($basePrice + $variantPrice) * ((100 - $product['discount_percent']) / 100);
                } else {
                    $product['final_price'] = $product['original_price'];
                }
            }
        }
        unset($product); // Hủy tham chiếu

        // 5. Trả về View
        return view('user.interface.home', [
            'products' => $products,
            'categories' => $categories, // <--- TRUYỀN CATEGORIES XUỐNG
            'currentCategoryName' => $currentCategoryName, // <--- TÊN DANH MỤC ĐANG CHỌN
            'total' => $data['total'] ?? 0,
            'currentPage' => $data['currentPage'] ?? 1,
            'totalPages' => $data['totalPages'] ?? 0,
            'searchTerm' => $params['search'],
            'categoryId' => $params['categoryId'],
            'error' => null
        ]);
    }
    // Các hàm khác giữ nguyên (searchProducts, getCategories...)
    public function searchProducts(Request $request)
    {
        $params = [
            'search' => $request->input('search', ''),
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('pageSize', 100),
            'categoryId' => $request->input('categoryId', ''),
        ];
        return response()->json($this->interfaceService->getProducts($params));
    }

    public function getCategories()
    {
        return response()->json($this->interfaceService->getCategories());
    }
}
