<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
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
        // dd($categories);// lấy từ service
        return view('admin.products.create', compact('categories'));
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

        // Map categoryName nếu rỗng
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

        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Sản phẩm không tồn tại');
        }

        return view('admin.products.edit', compact('product', 'categories'));
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
