<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\InterfaceService;
use Illuminate\Http\Request;

class InterfaceController extends Controller
{
    protected $interfaceService;

    public function __construct(InterfaceService $interfaceService)
    {
        $this->interfaceService = $interfaceService;
    }

    /**
     * Trang chủ - Hiển thị danh sách sản phẩm
     *
     * Route: GET /
     */
    public function index(Request $request)
    {
        // Lấy tham số từ query string
        $params = [
            'search' => $request->input('search', ''),
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('pageSize', 100),
            'categoryId' => $request->input('categoryId', ''),
        ];

        // Gọi service để lấy sản phẩm
        $result = $this->interfaceService->getProducts($params);

        // Kiểm tra kết quả
        if (!$result['success']) {
            return view('user.interface.home', [
                'products' => [],
                'total' => 0,
                'currentPage' => 1,
                'totalPages' => 0,
                'error' => $result['message'] ?? 'Không thể tải sản phẩm'
            ]);
        }

        $data = $result['data'];

        // Truyền dữ liệu vào view
        return view('user.interface.home', [
            'products' => $data['products'] ?? [],
            'total' => $data['total'] ?? 0,
            'currentPage' => $data['currentPage'] ?? 1,
            'totalPages' => $data['totalPages'] ?? 0,
            'searchTerm' => $params['search'],
            'error' => null
        ]);
    }

    /**
     * API endpoint để tìm kiếm sản phẩm (AJAX)
     *
     * Route: GET /api/search-products
     * Return: JSON
     */
    public function searchProducts(Request $request)
    {
        $params = [
            'search' => $request->input('search', ''),
            'page' => $request->input('page', 1),
            'pageSize' => $request->input('pageSize', 100),
            'categoryId' => $request->input('categoryId', ''),
        ];

        $result = $this->interfaceService->getProducts($params);

        // Trả về JSON cho AJAX
        return response()->json($result);
    }

    /**
     * Xem chi tiết sản phẩm
     *
     * Route: GET /product/{id}
     */
    public function show($id)
    {
        $result = $this->interfaceService->getProductById($id);

        if (!$result['success']) {
            return redirect()->route('home')->with('error', 'Sản phẩm không tồn tại');
        }

        return view('product.detail', [
            'product' => $result['data']
        ]);
    }

    /**
     * Lấy danh mục (nếu cần)
     *
     * Route: GET /api/categories
     */
    public function getCategories()
    {
        $result = $this->interfaceService->getCategories();
        return response()->json($result);
    }
}
