<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\WarrantyService;
use App\Services\WarrantyPolicyService;
use Illuminate\Http\Request;
use App\Services\ProductSerialService;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;
    protected $warrantyPolicyService;
    protected $productSerialService;

    public function __construct(ProductService $productService, CategoryService $categoryService, WarrantyPolicyService $warrantyPolicyService, ProductSerialService $productSerialService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->warrantyPolicyService = $warrantyPolicyService;
        $this->productSerialService = $productSerialService;
    }

    /**
     * Trang danh sách sản phẩm
     */
    // public function index()
    // {
    //     $products = $this->productService->getAllProducts();
    //     $categories = $this->categoryService->getAllCategories(); // lấy từ

    //     return view('admin.products.index', compact('products', 'categories'));
    // }
    public function index(Request $request)
    {
        // Lấy tất cả sản phẩm từ service và convert sang Collection
        $allProducts = collect($this->productService->getAllProducts()); // ✅ convert sang Collection
        $categories = $this->categoryService->getAllCategories();

        // --- Phân trang thủ công ---
        $perPage = 10; // số sản phẩm / trang
        $page = $request->get('page', 1); // trang hiện tại

        $paginatedProducts = new \Illuminate\Pagination\LengthAwarePaginator(
            $allProducts->forPage($page, $perPage), // giờ sẽ chạy bình thường
            $allProducts->count(),
            $perPage,
            $page,
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
        $policies = $this->warrantyPolicyService->getAllPolicies();
        // dd($categories);// lấy từ service
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
            'warrantyPolicyId' => 'nullable|string',
            'isActive' => 'required|boolean',
            'variants' => 'nullable|array',
            'variants.*.name' => 'nullable|string|min:1',
            'variants.*.value' => 'nullable|string|min:1',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ]);

        // Map images
        if (!empty($validated['images'])) {
            // giữ nguyên array
        } elseif (!empty($validated['image_url'])) {
            $validated['images'] = [$validated['image_url']];
        } else {
            $validated['images'] = [];
        }
        unset($validated['image_url']);

        // Map categoryName từ CategoryService
        $categories = $this->categoryService->getAllCategories();
        if (!empty($validated['categoryId'])) {
            $category = collect($categories)->firstWhere('id', $validated['categoryId']);
            if ($category) {
                $validated['categoryName'] = $category['name'];
            }
        }

        // Tạo sản phẩm
        $result = $this->productService->createProduct($validated);

        if (!($result['success'] ?? false)) {
            return back()->with('error', $result['message'] ?? 'Lỗi khi thêm sản phẩm');
        }

        $product = $result['data'] ?? null;

        // Nếu policy yêu cầu serial thì tạo tự động
        if ($product && !empty($validated['warrantyPolicyId'])) {
            $policy = $this->warrantyPolicyService->getById($validated['warrantyPolicyId']);

            if ($policy && ($policy['requiresSerial'] ?? false)) {
                $serialService = new ProductSerialService();

                $newSerial = $product['id']; // hoặc format khác bạn muốn


                $serialService->createSerial([
                    'productId' => $product['id'],
                    'serial' => $newSerial,
                    'status' => 'available',
                ]);
            }
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
            return redirect()->route('admin.products.index')->with('error', 'Sản phẩm không tồn tại');
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
            'variants' => 'nullable|array',
            'variants.*.name' => 'nullable|string|min:1',
            'variants.*.value' => 'nullable|string|min:1',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ]);

        // Map images
        if (!empty($validated['images'])) {
            // giữ nguyên array
        } elseif (!empty($validated['image_url'])) {
            $validated['images'] = [$validated['image_url']];
        } else {
            $validated['images'] = [];
        }
        unset($validated['image_url']);

        // Map categoryName từ CategoryService
        if (!empty($validated['categoryId'])) {
            $categories = $this->categoryService->getAllCategories();
            $category = collect($categories)->firstWhere('_id', $validated['categoryId']);
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
            return redirect()->route('admin.products.index')->with('success', 'Xóa thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi xóa');
    }
}
