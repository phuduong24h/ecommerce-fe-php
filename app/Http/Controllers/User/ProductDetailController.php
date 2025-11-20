<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ProductDetailService;
use App\Services\WarrantyServiceUser;
use Illuminate\Http\Request;

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
        // 1. Lấy sản phẩm
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return redirect()->route('home')->with('error', 'Sản phẩm không tồn tại.');
        }

        // 2. Lấy danh sách bảo hành
        $policies = $this->warrantyService->getAllPolicies();

        // 3. Lấy ID bảo hành từ sản phẩm
        $wId = isset($product['warrantyPolicyId']) ? trim((string)$product['warrantyPolicyId']) : 'KHÔNG CÓ ID';

        // ============================================================
        // 🔴 BƯỚC DEBUG QUAN TRỌNG (XÓA SAU KHI TÌM RA LỖI)
        // ============================================================
        // Hãy chạy trang web, nếu thấy màn hình đen code, hãy chụp ảnh gửi tôi
        // dd([
        //     '1. ID Sản Phẩm cần tìm' => $wId,
        //     '2. Danh sách Bảo Hành lấy về' => $policies,
        //     '3. Dữ liệu sản phẩm gốc' => $product
        // ]);
        // ============================================================

        // Giá trị mặc định
        $policyName = 'Bảo hành tiêu chuẩn (Mặc định)';
        $policyCoverage = 'Liên hệ cửa hàng để biết chi tiết.';
        $displayLabel = 'New';

        // 4. Vòng lặp so sánh (Đã thêm log kiểm tra chặt chẽ hơn)
        foreach ($policies as $p) {
            // Lấy ID của chính sách (xử lý cả trường hợp id và _id)
            $pId = isset($p['id']) ? (string)$p['id'] : (string)($p['_id'] ?? '');

            // So sánh
            if ($pId === $wId) {
                $policyName = $p['name'];
                $policyCoverage = $p['coverage'] ?? $policyCoverage;

                // Tính toán nhãn hiển thị
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
                break; // Tìm thấy rồi thì thoát vòng lặp
            }
        }
        // 5. Gán dữ liệu vào mảng product
        $product['warranty_label'] = $displayLabel;

        // Đảm bảo luôn có mảng warrantyPolicy để View không bị lỗi null
        $product['warrantyPolicy'] = [
            'name' => $policyName,
            'coverage' => $policyCoverage
        ];

        return view('user.interface.productDetail', compact('product'));
    }
}
