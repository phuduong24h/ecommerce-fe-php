<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InterfaceService
{
    protected $baseUrl;
    protected $authBaseUrl;
    protected $timeout;
    protected $verify;

    public function __construct()
    {
        // SỬA: Nối thêm '/api/v1'
        $this->baseUrl = config('services.api.url') . '/api/v1';

        // Auth URL sẽ tự động đúng: .../api/v1/auth
        $this->authBaseUrl = $this->baseUrl . '/auth';

        $this->timeout = config('services.api.timeout', 10);
        $this->verify = config('services.api.verify', false);
    }

    /**
     * Helper để tạo HTTP request với cấu hình chuẩn
     */
    protected function getHttp()
    {
        return Http::timeout($this->timeout)->withOptions(['verify' => $this->verify]);
    }

    public function getProducts($params = [])
    {
        try {
            $queryParams = [
                'page' => $params['page'] ?? 1,
                'pageSize' => $params['pageSize'] ?? 100,
            ];

            if (!empty($params['search'])) {
                $queryParams['search'] = $params['search'];
            }

            if (!empty($params['categoryId'])) {
                $queryParams['categoryId'] = $params['categoryId'];
            }

            // URL lúc này: http://localhost:3000/api/v1/products
            $apiUrl = "{$this->baseUrl}/products";

            Log::info('🔗 Calling API:', ['url' => $apiUrl, 'params' => $queryParams]);

            // Sử dụng helper đã cấu hình timeout/verify
            $response = $this->getHttp()->get($apiUrl, $queryParams);

            if ($response->failed()) {
                Log::error('API Error (getProducts): ' . $response->body());
                return ['success' => false, 'message' => 'Không thể kết nối tới backend', 'data' => []];
            }

            $json = $response->json();

            if (isset($json['success']) && $json['success']) {
                return ['success' => true, 'data' => $json['data']];
            }

            return ['success' => false, 'message' => $json['message'] ?? 'Lỗi backend', 'data' => []];
        } catch (\Exception $e) {
            Log::error('Exception (getProducts): ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
                'data' => ['products' => [], 'total' => 0]
            ];
        }
    }

    public function getProductById($id)
    {
        try {
            $response = $this->getHttp()->get("{$this->baseUrl}/products/{$id}");

            if ($response->failed()) {
                return ['success' => false, 'message' => 'Không tìm thấy sản phẩm'];
            }

            $json = $response->json();
            if (isset($json['success']) && $json['success']) {
                return ['success' => true, 'data' => $json['data']];
            }

            return ['success' => false, 'message' => $json['message'] ?? 'Lỗi'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getCategories()
    {
        try {
            // Lấy 100 sản phẩm mới nhất để quét danh mục
            $response = $this->getHttp()->get("{$this->baseUrl}/products", [
                'page' => 1,
                'pageSize' => 100
            ]);

            if ($response->failed()) {
                return ['success' => false, 'data' => []];
            }

            $json = $response->json();
            $products = $json['data']['products'] ?? [];

            $categories = [];
            $seenIds = []; // Đổi sang check trùng theo ID để đảm bảo tính duy nhất

            foreach ($products as $p) {
                $catName = $p['categoryName'] ?? null;
                $catId = $p['categoryId'] ?? null;

                // 🛑 CHỈ LẤY NẾU CÓ CẢ ID VÀ TÊN
                // Nếu không có catId, Backend sẽ không lọc được -> Bỏ qua
                if ($catName && $catId && !in_array($catId, $seenIds)) {
                    $seenIds[] = $catId;

                    $categories[] = [
                        'id' => $catId,
                        'name' => $catName
                    ];
                }
            }

            // Sắp xếp A-Z
            usort($categories, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            return ['success' => true, 'data' => $categories];

        } catch (\Exception $e) {
            Log::error('Error extracting categories: ' . $e->getMessage());
            return ['success' => false, 'data' => []];
        }
    }

    // Lưu ý: Tôi đã bỏ các hàm login/register/cart ở đây vì bạn đã tách chúng ra file Service riêng
    // Nếu bạn vẫn muốn giữ Login ở đây để dùng cho InterfaceController cũ thì logic tương tự bên dưới.
}
