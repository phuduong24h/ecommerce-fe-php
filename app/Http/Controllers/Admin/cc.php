<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use View;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $productService;

    public function __construct(CategoryService $categoryService, ProductService $productService)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;
    }
    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        $categories = is_array($categories) ? $categories : [];
        return view('admin.settings.categories.index', compact('categories'));
    }

     public function create()
    {
        return view('admin.settings.categories.create');
    }

    public function store(Request $request)
    {
        $result = $this->categoryService->createCategory($request->all());

        if ($result['success'] ?? false) {
            return redirect()->route('admin.settings.categories.index')->with('success', 'Thêm danh mục thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi thêm danh mục');
    }

    public function edit($id)
    {
        $category = collect($this->categoryService->getAllCategories())
            ->firstWhere('id', $id);

        if (!$category) {
            return redirect()->route('admin.settings.categories.index')->with('error', 'Không tìm thấy danh mục');
        }

        return view('admin.settings.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $result = $this->categoryService->updateCategory($id, $request->all());

        if ($result['success'] ?? false) {
            return redirect()->route('admin.settings.categories.index')->with('success', 'Cập nhật thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi cập nhật');
    }

    public function destroy($id)
    {
        $result = $this->categoryService->deleteCategory($id);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.settings.categories.index')->with('success', 'Xóa thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi xóa');
    }
}
