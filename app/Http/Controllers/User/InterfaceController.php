<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\InterfaceService;
use App\Services\WarrantyServiceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Thắng
class InterfaceController extends Controller
{
    protected $interfaceService;
    protected $warrantyServiceUser;

    public function __construct(
        InterfaceService $interfaceService,
        WarrantyServiceUser $warrantyServiceUser
    ) {
        $this->interfaceService = $interfaceService;
        $this->warrantyServiceUser = $warrantyServiceUser;
    }

    public function index(Request $request)
    {
        // 1. Lấy Sản Phẩm (Thêm tham số page)
        $params = [
            'search' => $request->input('search', ''),
            'categoryId' => $request->input('categoryId', ''),
            'page' => $request->input('page', 1), // <--- QUAN TRỌNG: Lấy trang hiện tại
            'pageSize' => 8, // Giả sử mỗi trang hiện 8 sản phẩm để thấy phân trang rõ hơn
        ];
        $productResult = $this->interfaceService->getProducts($params);

        // 2. Lấy Danh sách Bảo Hành (Từ Service đã sửa URL đúng)
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
        }
        unset($product); // Hủy tham chiếu

        // 5. Trả về View
        return view('user.interface.home', [
            'products' => $products,
            'total' => $data['total'] ?? 0,
            'currentPage' => $data['currentPage'] ?? 1, // <--- Cần thiết cho View
            'totalPages' => $data['totalPages'] ?? 0,   // <--- Cần thiết cho View
            'searchTerm' => $params['search'],
            'categoryId' => $params['categoryId'], // Truyền lại categoryId để khi chuyển trang không bị mất lọc
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
