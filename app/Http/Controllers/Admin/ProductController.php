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

    public function index(Request $request)
    {
        $allProducts = collect($this->productService->getAllProducts()); 
        $categories = $this->categoryService->getAllCategories();

        $perPage = 5; 
        $page = $request->get('page', 1); 

        $paginatedProducts = new \Illuminate\Pagination\LengthAwarePaginator(
            $allProducts->forPage($page, $perPage),
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

    public function create()
    {
        $categories = $this->categoryService->getAllCategories();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            // QUAN TRỌNG: Để 'string' để chấp nhận cả URL và Base64
            'images.*' => 'nullable|string', 
            'image_url' => 'nullable|url',
            'categoryId' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Lọc bỏ các giá trị null hoặc rỗng trong mảng images
        if (!empty($validated['images'])) {
            $validated['images'] = array_values(array_filter($validated['images'], function($value) {
                return !empty($value);
            }));
        }

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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            // QUAN TRỌNG: Để 'string' để chấp nhận cả URL và Base64
            'images.*' => 'nullable|string',
            'image_url' => 'nullable|url',
            'categoryId' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if (!empty($validated['images'])) {
            $validated['images'] = array_values(array_filter($validated['images'], function($value) {
                return !empty($value);
            }));
        }

        if (!empty($validated['categoryId'])) {
            $categories = $this->categoryService->getAllCategories();
            // Lưu ý: kiểm tra xem id trong mảng category là 'id' hay '_id' tùy API trả về
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

    public function destroy($id)
    {
        $result = $this->productService->deleteProduct($id);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.products.index')->with('success', 'Xóa thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi xóa');
    }
}