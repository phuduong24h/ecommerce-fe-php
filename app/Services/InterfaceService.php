<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InterfaceService
{
    protected $baseUrl;
    protected $authBaseUrl;
    protected $cartBaseUrl;

    public function __construct()
    {
        // URL API backend của bạn (không cần token vì đây là public API)
        $this->baseUrl = config('services.api.url', 'http://localhost:3000') . '/api/v1';
        $this->authBaseUrl = config('services.api.url', 'http://localhost:3000') . '/api/v1/auth'; // <-- THÊM DÒNG NÀY
        $this->cartBaseUrl = $this->baseUrl . '/cart'; // <-- THÊM DÒNG NÀY
    }

    /**
     * Lấy tất cả sản phẩm với tìm kiếm và phân trang
     *
     * @param array $params - ['search' => '', 'page' => 1, 'pageSize' => 10, 'categoryId' => '']
     * @return array
     */
    public function getProducts($params = [])
    {
        try {
            // Xây dựng query parameters
            $queryParams = [
                'page' => $params['page'] ?? 1,
                'pageSize' => $params['pageSize'] ?? 100,
            ];

            // Thêm search nếu có
            if (!empty($params['search'])) {
                $queryParams['search'] = $params['search'];
            }

            // Thêm categoryId nếu có
            if (!empty($params['categoryId'])) {
                $queryParams['categoryId'] = $params['categoryId'];
            }

            // ✅ FIX: Gọi đúng endpoint /api/v1/products
            $apiUrl = "{$this->baseUrl}/products";

            Log::info('🔗 Calling API:', ['url' => $apiUrl, 'params' => $queryParams]);

            $response = Http::timeout(10)->get($apiUrl, $queryParams);

            if ($response->failed()) {
                Log::error('API Error (getProducts): ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'Không thể kết nối tới backend',
                    'data' => [ /* ... mảng data rỗng ... */ ]
                ];
            }

            $json = $response->json();

            // Kiểm tra nếu JSON không hợp lệ hoặc rỗng từ backend
            if (is_null($json)) {
                Log::error('API Error (getProducts): Invalid JSON or empty response from backend.');
                return [
                    'success' => false,
                    'message' => 'Backend trả về dữ liệu không hợp lệ',
                    'data' => [ /* ... mảng data rỗng ... */ ]
                ];
            }

            // Backend trả về: { success: true, data: { products, total, currentPage, totalPages } }
            if (isset($json['success']) && $json['success']) {
                return [
                    'success' => true,
                    'data' => $json['data']
                ];
            }

            return [
                'success' => false,
                'message' => $json['message'] ?? 'Lỗi không xác định',
                'data' => [ /* ... mảng data rỗng ... */ ]
            ];

        } catch (\Exception $e) {
            Log::error('Exception (getProducts): ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
                'data' => [
                    'products' => [],
                    'total' => 0,
                    'currentPage' => 1,
                    'totalPages' => 0
                ]
            ];
        }
    }

    /**
     * Lấy chi tiết 1 sản phẩm
     *
     * @param string $id
     * @return array
     */
    public function getProductById($id)
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/products/{$id}");

            if ($response->failed()) {
                Log::error("API Error (getProductById {$id}): " . $response->body());
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm'
                ];
            }

            $json = $response->json();

            if (isset($json['success']) && $json['success']) {
                return [
                    'success' => true,
                    'data' => $json['data']
                ];
            }

            return [
                'success' => false,
                'message' => $json['message'] ?? 'Sản phẩm không tồn tại'
            ];

        } catch (\Exception $e) {
            Log::error("Exception (getProductById {$id}): " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lấy danh mục sản phẩm (nếu backend có API này)
     *
     * @return array
     */
    public function getCategories()
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/categories");

            if ($response->failed()) {
                Log::error('API Error (getCategories): ' . $response->body());
                return [
                    'success' => false,
                    'data' => []
                ];
            }

            $json = $response->json();

            if (isset($json['success']) && $json['success']) {
                return [
                    'success' => true,
                    'data' => $json['data']
                ];
            }

            return [
                'success' => false,
                'data' => []
            ];

        } catch (\Exception $e) {
            Log::error('Exception (getCategories): ' . $e->getMessage());
            return [
                'success' => false,
                'data' => []
            ];
        }
    }

    /**
     * Gọi API đăng nhập
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login($email, $password)
    {
        try {
            $response = Http::timeout(10)->post("{$this->authBaseUrl}/login", [
                'email' => $email,
                'password' => $password,
            ]);

            $json = $response->json();

            if ($response->failed() || is_null($json) || !$json['success']) {
                $message = $json['error']['message'] ?? 'INVALID_CREDENTIALS';
                if ($message === 'INVALID_CREDENTIALS') $message = 'Sai email hoặc mật khẩu';

                return ['success' => false, 'message' => $message];
            }

            // { success: true, data: { token, user } }
            return [
                'success' => true,
                'data' => $json['data']
            ];

        } catch (\Exception $e) {
            Log::error('Exception (login): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống. Vui lòng thử lại.'];
        }
    }

    /**
     * Gọi API đăng ký
     *
     * @param array $details - ['name', 'email', 'phone', 'password']
     * @return array
     */
    public function register(array $details)
    {
        try {
            $response = Http::timeout(10)->post("{$this->authBaseUrl}/register", $details);
            $json = $response->json();

            if ($response->failed() || is_null($json) || !$json['success']) {
                $message = $json['error']['message'] ?? 'REGISTER_FAILED';
                return ['success' => false, 'message' => $message];
            }

            // { success: true, data: { ...user } }
            return [
                'success' => true,
                'data' => $json['data']
            ];

        } catch (\Exception $e) {
            Log::error('Exception (register): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống. Vui lòng thử lại.'];
        }
    }
    public function getCart()
    {
        try {
            if (!session('user_token')) {
                return ['success' => false, 'message' => 'Chưa đăng nhập'];
            }

            $response = Http::withToken(session('user_token'))
                            ->timeout(10)
                            ->get($this->cartBaseUrl);

            $json = $response->json();
            if ($response->failed() || !$json['success']) {
                return ['success' => false, 'message' => 'Không thể lấy giỏ hàng'];
            }

            // Trả về mảng data (là mảng giỏ hàng)
            return ['success' => true, 'data' => $json['data'] ?? []];

        } catch (\Exception $e) {
            Log::error('Exception (getCart): ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }

    /**
     * Cập nhật toàn bộ giỏ hàng lên backend
     * Yêu cầu đã đăng nhập (gửi token)
     *
     * @param array $cartArray Mảng giỏ hàng mới
     */
    public function updateCart(array $cartArray)
    {
        try {
            if (!session('user_token')) {
                return ['success' => false, 'message' => 'Chưa đăng nhập'];
            }

            // API backend yêu cầu body là { cart: [...] }
            $body = ['cart' => $cartArray];

            $response = Http::withToken(session('user_token'))
                            ->timeout(10)
                            ->put($this->cartBaseUrl, $body);

            $json = $response->json();
            if ($response->failed() || !$json['success']) {
                return ['success' => false, 'message' => 'Không thể cập nhật giỏ hàng'];
            }

            // Trả về mảng data (là mảng giỏ hàng đã cập nhật)
            return ['success' => true, 'data' => $json['data'] ?? []];

        } catch (\Exception $e) {
            Log::error('Exception (updateCart): ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }
}
