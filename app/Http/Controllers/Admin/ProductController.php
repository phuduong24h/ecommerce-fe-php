<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\WarrantyPolicyService; // Từ File 2
use App\Services\ProductSerialService;  // Từ File 2
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\PromotionService;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;
    protected $warrantyPolicyService; // Từ File 2
    protected $productSerialService;  // Từ File 2

    protected $promotionService;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService,
        WarrantyPolicyService $warrantyPolicyService,
        ProductSerialService $productSerialService,
        PromotionService $promotionService
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->warrantyPolicyService = $warrantyPolicyService;
        $this->productSerialService = $productSerialService;
        $this->promotionService = $promotionService;
    }

    /**
     * Danh sách sản phẩm
     */
    public function index(Request $request)
    {
        $allProducts = collect($this->productService->getAllProducts());
        $categories = $this->categoryService->getAllCategories();

        // Lấy cấu hình phân trang (Ưu tiên File 2 là 10 item, nhưng giữ logic query của File 1)
        $perPage = 10;
        $page = $request->get('page', 1);

        $paginatedProducts = new LengthAwarePaginator(
            $allProducts->forPage($page, $perPage),
            $allProducts->count(),
            $perPage,
            $page,
            // Gộp: Lấy logic File 1 thêm 'query' để giữ bộ lọc khi chuyển trang
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.products.index', [
            'products' => $paginatedProducts,
            'categories' => $categories
        ]);
    }

    /**
     * Trang thêm sản phẩm
     */
    public function create()
    {
        $categories = $this->categoryService->getAllCategories();
        // Gộp: Thêm policies từ File 2
        $policies = $this->warrantyPolicyService->getAllPolicies();
         $promotions = $this->promotionService->getAllPromotions(); // giả sử bạn có service PromotionService

        return view('admin.products.create', compact('categories', 'policies', 'promotions'));
    }

    /**
     * Lưu sản phẩm mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            
            // Gộp: Lấy từ File 1 (string) để chấp nhận Base64, thay vì chỉ URL như File 2
            'images.*' => 'nullable|string', 
            
            'image_url' => 'nullable|url',
            'categoryId' => 'nullable|string',
            'description' => 'nullable|string',
            
            // Các trường mới từ File 2
            'warrantyPolicyId' => 'nullable|string',
            'isActive' => 'required|boolean',
            'variants' => 'nullable|array',
            'variants.*.name' => 'nullable|string|min:1',
            'variants.*.value' => 'nullable|string|min:1',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
            'promotionId' => 'nullable|string',

        ]);

        // Xử lý ảnh (Logic gộp: Ưu tiên lọc rỗng của File 1 + fallback của File 2)
        if (!empty($validated['images'])) {
            // Logic File 1: Lọc bỏ giá trị null/rỗng
            $validated['images'] = array_values(array_filter($validated['images'], function($value) {
                return !empty($value);
            }));
        } elseif (!empty($validated['image_url'])) {
            // Logic File 2: Fallback nếu nhập single url
            $validated['images'] = [$validated['image_url']];
        } else {
            $validated['images'] = [];
        }
        unset($validated['image_url']);

        // Map categoryName
        $categories = $this->categoryService->getAllCategories();
        if (!empty($validated['categoryId'])) {
            $category = collect($categories)->firstWhere('id', $validated['categoryId']);
            if ($category) {
                $validated['categoryName'] = $category['name'];
            }
        }

        $result = $this->productService->createProduct($validated);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi thêm sản phẩm');
    }

    /**
     * Trang chỉnh sửa sản phẩm
     */
    public function edit($id)
    {
        $products = $this->productService->getAllProducts();
        $product = collect($products)->firstWhere('id', $id);
        $categories = $this->categoryService->getAllCategories();
        // Gộp: Thêm policies từ File 2
        $policies = $this->warrantyPolicyService->getAllPolicies();
        $promotions = $this->promotionService->getAllPromotions(); 

        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Sản phẩm không tồn tại');
        }

        return view('admin.products.edit', compact('product', 'categories', 'policies', 'promotions'));
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            
            // Gộp: Lấy từ File 1 (string) để chấp nhận Base64
            'images.*' => 'nullable|string',

            'image_url' => 'nullable|url',
            'categoryId' => 'nullable|string',
            'description' => 'nullable|string',
            
            // Các trường mới từ File 2
            'warrantyPolicyId' => 'nullable|string',
            'isActive' => 'required|boolean',
            'variants' => 'nullable|array',
            'variants.*.name' => 'nullable|string|min:1',
            'variants.*.value' => 'nullable|string|min:1',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
            'promotionId' => 'nullable|string',
        ]);

        // Xử lý images (Kết hợp logic lọc và fallback)
        if (!empty($validated['images'])) {
             $validated['images'] = array_values(array_filter($validated['images'], function($value) {
                return !empty($value);
            }));
        } elseif (!empty($validated['image_url'])) {
            $validated['images'] = [$validated['image_url']];
        } else {
            $validated['images'] = [];
        }
        unset($validated['image_url']);

        // Xử lý categoryName
        if (!empty($validated['categoryId'])) {
            $categories = $this->categoryService->getAllCategories();
            
            // Gộp: Lấy Logic từ File 1 (Check cả 'id' và '_id' cho MongoDB)
            $category = collect($categories)->firstWhere('id', $validated['categoryId']);
            if (!$category) {
                 $category = collect($categories)->firstWhere('_id', $validated['categoryId']);
            }
            
            if ($category) {
                $validated['categoryName'] = $category['name'];
            }
        }

        $result = $this->productService->updateProduct($id, $validated);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.products.index')->with('success', 'Cập nhật thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi cập nhật');
    }

    /**
     * Xóa sản phẩm
     */
    public function destroy($id)
    {
        $result = $this->productService->deleteProduct($id);

        if ($result['success'] ?? false) {
            // Gộp: Dùng redirect route của File 1 để an toàn hơn (tránh lỗi trang trắng khi xóa item cuối trang)
            return redirect()->route('admin.products.index')->with('success', 'Xóa thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi xóa');
    }
}