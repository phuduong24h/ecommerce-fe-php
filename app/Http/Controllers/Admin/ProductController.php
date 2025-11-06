<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Lấy categories tạm thời (mảng thuần) để test
     */
    private function getTestCategories()
    {
        return [
            ['id' => 1, 'name' => 'Electronics'],
            ['id' => 2, 'name' => 'Accessories'],
            ['id' => 3, 'name' => 'Parts'],
        ];
    }

    /**
     * DANH SÁCH SẢN PHẨM
     */
    public function index()
    {
        $products = Product::latest()->paginate(10);
        $categories = $this->getTestCategories();
        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * FORM THÊM MỚI
     */
    public function create()
    {
        $categories = $this->getTestCategories();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * LƯU SẢN PHẨM MỚI
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|min:3|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string|max:2000',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|integer', // dùng integer vì test data
        ], [
            'name.required'  => 'Tên sản phẩm không được để trống.',
            'image.required' => 'Vui lòng chọn ảnh sản phẩm.',
        ]);

        // Upload ảnh
        $path = $request->file('image')->store('products', 'public');
        $validated['image'] = basename($path);

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Thêm sản phẩm thành công!');
    }

    /**
     * CHI TIẾT SẢN PHẨM
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * FORM SỬA
     */
    public function edit(Product $product)
    {
        $categories = $this->getTestCategories();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * CẬP NHẬT SẢN PHẨM
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string|min:3|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string|max:2000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|integer', // dùng integer vì test data
        ], [
            'name.required' => 'Tên sản phẩm không được để trống.',
        ]);

        // Xử lý ảnh mới (nếu có)
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::delete('public/products/' . $product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = basename($path);
        }

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Cập nhật thành công!');
    }

    /**
     * XÓA SẢN PHẨM
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::delete('public/products/' . $product->image);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Xóa sản phẩm thành công!');
    }
}
