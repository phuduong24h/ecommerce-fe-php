<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ProductDetailService;
use App\Services\WarrantyServiceUser;
use Illuminate\Http\Request;


// Thắng
class ProductDetailController extends Controller
{
    protected $productService;
    protected $warrantyService;

    public function __construct(
        ProductDetailService $productService,
        WarrantyServiceUser $warrantyService
    ) {
        $this->productService = $productService;
        $this->warrantyService = $warrantyService;
    }

    public function show($id)
    {
        // 1. Gọi API LẤY MỘT LẦN DUY NHẤT (QUAN TRỌNG)
        $result = $this->productService->getProductById($id);

        // Kiểm tra lỗi
        if (!$result || empty($result['product'])) {
            return redirect()->route('home')->with('error', 'Sản phẩm không tồn tại.');
        }

        // Tách dữ liệu ra các biến
        $productData = $result['product'];
        $responseTime = $result['time'];     // Lấy thời gian từ lần gọi duy nhất này
        $isCached = $result['is_cached'];    // Lấy trạng thái cache từ lần gọi duy nhất này

        // 2. Logic Bảo hành (Sử dụng $productData)
        $policies = $this->warrantyService->getAllPolicies();
        $wId = isset($productData['warrantyPolicyId']) ? trim((string)$productData['warrantyPolicyId']) : 'KHÔNG CÓ ID';

        // Giá trị mặc định
        $policyName = 'Bảo hành tiêu chuẩn (Mặc định)';
        $policyCoverage = 'Liên hệ cửa hàng để biết chi tiết.';
        $displayLabel = 'New';

        // 3. Vòng lặp so sánh
        foreach ($policies as $p) {
            $pId = isset($p['id']) ? (string)$p['id'] : (string)($p['_id'] ?? '');

            if ($pId === $wId) {
                $policyName = $p['name'];
                $policyCoverage = $p['coverage'] ?? $policyCoverage;

                $days = intval($p['durationDays']);
                if ($days >= 365 && $days % 365 == 0) {
                    $years = $days / 365;
                    $displayLabel = $years . ' Year' . ($years > 1 ? 's' : '');
                } elseif ($days >= 30 && $days % 30 == 0) {
                    $months = $days / 30;
                    $displayLabel = $months . ' Months';
                } else {
                    $displayLabel = $days . ' Days';
                }
                break;
            }
        }

        // 4. Gán dữ liệu bảo hành vào mảng $productData (Sửa lỗi dùng nhầm biến $product)
        $productData['warranty_label'] = $displayLabel;
        $productData['warrantyPolicy'] = [
            'name' => $policyName,
            'coverage' => $policyCoverage
        ];

        // --- ĐÃ XÓA ĐOẠN GỌI API LẦN 2 TẠI ĐÂY ---

        // 5. Trả về View
        return view('user.interface.productDetail', [
            'product' => $productData,      // Truyền mảng dữ liệu đã xử lý bảo hành
            'responseTime' => $responseTime,
            'isCached' => $isCached
        ]);
    }
}
