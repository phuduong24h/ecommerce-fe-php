<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\WarrantyPolicyService;
use App\Services\ProductSerialService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;
    protected $warrantyPolicyService;
    protected $productSerialService;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService,
        WarrantyPolicyService $warrantyPolicyService,
        ProductSerialService $productSerialService
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->warrantyPolicyService = $warrantyPolicyService;
        $this->productSerialService = $productSerialService;
    }

    /**
     * Danh sách sản phẩm (phân trang thủ công)
     */
    public function index(Request $request)
    {
        // Lấy tất cả sản phẩm từ service
        $allProducts = collect($this->productService->getAllProducts());
        $categories = $this->categoryService->getAllCategories();

        // Số item mỗi trang
        $perPage = 10;
        $page = $request->get('page', 1);

        // Phân trang
        $products = new LengthAwarePaginator(
            $allProducts->forPage($page, $perPage),
            $allProducts->count(),
            $perPage,
            $page,
            ['path' => $request->url()]
        );

        // Trả view
        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }


    /**
     * Trang thêm sản phẩm
     */
    public function create()
    {
        $categories = $this->categoryService->getAllCategories();
        $policies = $this->warrantyPolicyService->getAllPolicies();

        return view('admin.products.create', compact('categories', 'policies'));
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
            'images.*' => 'nullable|url',
            'image_url' => 'nullable|url',
            'categoryId' => 'nullable|string',
            'description' => 'nullable|string',
            'warrantyPolicyId' => 'nullable|string', // vẫn lưu nếu chọn
            'isActive' => 'required|boolean',
            'variants' => 'nullable|array',
            'variants.*.name' => 'nullable|string|min:1',
            'variants.*.value' => 'nullable|string|min:1',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ]);

        // Xử lý ảnh
        if (!empty($validated['images'])) {
            // giữ nguyên
        } elseif (!empty($validated['image_url'])) {
            $validated['images'] = [$validated['image_url']];
        } else {
            $validated['images'] = [];
        }
        unset($validated['image_url']);

        // Xử lý categoryName
        if (!empty($validated['categoryId'])) {
            $categories = $this->categoryService->getAllCategories();
            $category = collect($categories)->firstWhere('id', $validated['categoryId']);
            if ($category) {
                $validated['categoryName'] = $category['name'];
            }
        }

        // Tạo sản phẩm (lưu cả warrantyPolicyId nếu có)
        $result = $this->productService->createProduct($validated);

        if (!($result['success'] ?? false)) {
            return back()->with('error', $result['message'] ?? 'Lỗi khi thêm sản phẩm');
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Thêm sản phẩm thành công!');
    }


    /**
     * Trang chỉnh sửa sản phẩm
     */
    public function edit($id)
    {
        $products = $this->productService->getAllProducts();
        $product = collect($products)->firstWhere('id', $id);
        $categories = $this->categoryService->getAllCategories();
        $policies = $this->warrantyPolicyService->getAllPolicies();

        if (!$product) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Sản phẩm không tồn tại');
        }

        return view('admin.products.edit', compact('product', 'categories', 'policies'));
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
            'images.*' => 'nullable|url',

            'image_url' => 'nullable|url',
            'categoryId' => 'nullable|string',
            'description' => 'nullable|string',
            'warrantyPolicyId' => 'nullable|string', // thêm dòng này
            'isActive' => 'required|boolean',

            'variants' => 'nullable|array',
            'variants.*.name' => 'nullable|string|min:1',
            'variants.*.value' => 'nullable|string|min:1',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ]);

        // Xử lý images
        if (!empty($validated['images'])) {
            //
        } elseif (!empty($validated['image_url'])) {
            $validated['images'] = [$validated['image_url']];
        } else {
            $validated['images'] = [];
        }
        unset($validated['image_url']);

        // Xử lý categoryName
        if (!empty($validated['categoryId'])) {
            $categories = $this->categoryService->getAllCategories();
            $category = collect($categories)->firstWhere('id', $validated['categoryId']);

            if ($category) {
                $validated['categoryName'] = $category['name'];
            }
        }

        // Cập nhật sản phẩm
        $result = $this->productService->updateProduct($id, $validated);

        if (!($result['success'] ?? false)) {
            return back()->with('error', $result['message'] ?? 'Lỗi khi cập nhật');
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }


    /**
     * Xóa sản phẩm
     */
    public function destroy($id)
    {
        $result = $this->productService->deleteProduct($id);

        if ($result['success'] ?? false) {
            return back()->with('success', 'Xóa thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi xóa');
    }
}
