<?php
// app/Http/Controllers/Admin/SettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CenterService;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Services\PromotionService;
use App\Services\CategoryService;
use App\Services\productSerialService;
use App\Services\AdminLogService;
use Illuminate\Pagination\LengthAwarePaginator;
class SettingController extends Controller
{

    protected $categoryService;
    protected $promotionService;
    protected $centerService;
    protected $productSerialService;
    protected $productService;
    protected $adminLogService;
    public function __construct(CategoryService $categoryService, ProductService $productService, PromotionService $promotionService, CenterService $centerService, ProductSerialService $productSerialService, AdminLogService $adminLogService)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;
        $this->promotionService = $promotionService;
        $this->centerService = $centerService;
        $this->productSerialService = $productSerialService;
        $this->adminLogService = $adminLogService;
    }

    // public function index(Request $request)
    // {
    //     $tab = $request->get('tab', 'categories');

    //     // Lấy category để truyền cho view con
    //     $categories = $this->categoryService->getAllCategories() ?: [];

    //     return view('admin.settings.index', compact('tab', 'categories'));
    // }

    // public function index()
    // {
    //     $categories = $this->categoryService->getAllCategories() ?: [];
    //     $products = $this->productService->getAllProducts() ?: [];
    //     $promotions = $this->promotionService->getAllPromotions() ?: [];
    //     $centers = $this->centerService->getAllCenters() ?: [];
    //     $serials = $this->productSerialService->getAllSerials() ?: [];
    //     $logs = $this->adminLogService->getAllLogs();


    //     $categories = collect($categories)->map(function ($cat) use ($products) {
    //         $cat['product_count'] = collect($products)->where('categoryId', $cat['id'])->count();
    //         return $cat;
    //     })->toArray();
    //     $serials = array_map(function ($serial) use ($products) {
    //         $product = collect($products)->firstWhere('id', $serial['productId']);
    //         $serial['productName'] = $product['name'] ?? $serial['productId'];
    //         return $serial;
    //     }, $serials);
    //     $logs = collect($logs)->map(function ($log) {
    //         return [
    //             'id' => $log['id'],
    //             'action' => $log['action'] ?? null,
    //             'target' => $log['target'] ?? null,
    //             'adminName' => $log['adminName'] ?? 'Unknown',
    //             'timestamp' => isset($log['createdAt'])
    //                 ? \Carbon\Carbon::parse($log['createdAt'])->format('Y-m-d H:i')
    //                 : null,
    //         ];
    //     })->toArray();
    //     return view('admin.settings.index', compact('categories', 'promotions', 'centers', 'serials', 'logs'))
    //         ->with('activeTab', 'categories');
    // }
    public function index(Request $request)
    {
        // --- Lấy dữ liệu ---
        $categories = $this->categoryService->getAllCategories() ?: [];
        $products = $this->productService->getAllProducts() ?: [];
        $promotions = $this->promotionService->getAllPromotions() ?: [];
        $centers = $this->centerService->getAllCenters() ?: [];
        $serials = $this->productSerialService->getAllSerials() ?: [];
        $logs = $this->adminLogService->getAllLogs() ?: [];

        // --- Enrich data ---
        $categories = collect($categories)->map(function ($cat) use ($products) {
            $cat['product_count'] = collect($products)->where('categoryId', $cat['id'])->count();
            return $cat;
        })->toArray();

        $serials = array_map(function ($serial) use ($products) {
            $product = collect($products)->firstWhere('id', $serial['productId']);
            $serial['productName'] = $product['name'] ?? $serial['productId'];
            return $serial;
        }, $serials);

        $logs = collect($logs)->map(function ($log) {
            return [
                'id' => $log['id'],
                'action' => $log['action'] ?? null,
                'target' => $log['target'] ?? null,
                'adminName' => $log['adminName'] ?? 'Unknown',
                'timestamp' => isset($log['createdAt'])
                    ? \Carbon\Carbon::parse($log['createdAt'])->format('Y-m-d H:i')
                    : null,
            ];
        })->toArray();

        // --- Phân trang riêng cho từng tab ---
        $perPage = 5;

        $pageCategories = $request->get('page_categories', 1);
        $pagePromotions = $request->get('page_promotions', 1);
        $pageCenters = $request->get('page_centers', 1);
        $pageSerials = $request->get('page_serials', 1);
        $pageLogs = $request->get('page_logs', 1);

        $categoriesPaginator = new LengthAwarePaginator(
            collect($categories)->forPage($pageCategories, $perPage)->values(),
            count($categories),
            $perPage,
            $pageCategories,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'page_categories']
        );

        $promotionsPaginator = new LengthAwarePaginator(
            collect($promotions)->forPage($pagePromotions, $perPage)->values(),
            count($promotions),
            $perPage,
            $pagePromotions,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'page_promotions']
        );

        $centersPaginator = new LengthAwarePaginator(
            collect($centers)->forPage($pageCenters, $perPage)->values(),
            count($centers),
            $perPage,
            $pageCenters,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'page_centers']
        );

        $serialsPaginator = new LengthAwarePaginator(
            collect($serials)->forPage($pageSerials, $perPage)->values(),
            count($serials),
            $perPage,
            $pageSerials,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'page_serials']
        );

        $logsPaginator = new LengthAwarePaginator(
            collect($logs)->forPage($pageLogs, $perPage)->values(),
            count($logs),
            $perPage,
            $pageLogs,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'page_logs']
        );

        // --- Tab active ---
        $activeTab = $request->get('activeTab', 'categories');

        return view('admin.settings.index', compact('categories', 'promotions', 'centers', 'serials', 'logs'))
            ->with([
                'categories' => $categoriesPaginator,
                'promotions' => $promotionsPaginator,
                'centers' => $centersPaginator,
                'serials' => $serialsPaginator,
                'logs' => $logsPaginator,
                'activeTab' => $activeTab
            ]);
    }

    /**
     * Hiển thị form tạo category mới
     */
    public function createCategoryForm()
    {
        // Lấy tất cả category hiện có để chọn parent
        $categories = $this->categoryService->getAllCategories() ?: [];

        return view('admin.settings.categories.create', compact('categories'));
    }
    /**
     * Tạo category mới (POST)
     */
    /**
     * Tạo category mới (POST)
     */
    public function returnCategory()
    {
        $categories = $this->categoryService->getAllCategories();
    }
    public function createCategory(Request $request)
    {
        // Validate chỉ name và parentId
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parentId' => 'nullable|string', // category con hoặc null nếu category gốc
        ]);

        // Gọi service để tạo category
        // Chỉ truyền name và parentId
        $result = $this->categoryService->createCategory([
            'name' => $validated['name'],
            'parentId' => $validated['parentId'] ?? null,
        ]);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.settings.categories.index')
                ->with('success', 'Thêm category thành công!')
                ->with('activeTab', 'categories');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi thêm category');
    }



    /**
     * Hiển thị form edit category
     */
    public function editCategoryForm($id)
    {
        $category = collect($this->categoryService->getAllCategories())->firstWhere('id', $id);
        if (!$category) {
            return redirect()->route('admin.settings.index')->with('success', 'Cập nhật Category thành công!')
                ->with('activeTab', 'categories');
        }
        return view('admin.settings.categories.edit', compact('category'));
    }

    /**
     * Cập nhật category
     */
    /**
     * Cập nhật category
     */
    public function updateCategory(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parentId' => 'nullable|string', // thêm nếu muốn cho category con
        ]);

        $result = $this->categoryService->updateCategory($id, $validated);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.settings.categories.index')->with('success', 'Cập nhật category thành công!')
                ->with('activeTab', 'categories');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi cập nhật category');
    }


    /**
     * Xóa category
     */
    public function deleteCategory($id)
    {
        $result = $this->categoryService->deleteCategory($id);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.settings.categories.index') // ✅ đúng route
                ->with('success', 'Xóa category thành côngs!')
                ->with('activeTab', 'categories');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi xóa category');
    }


    /* ================== PROMOTIONS ================== */

    public function createPromotionForm()
    {
        $products = $this->productService->getAllProducts();
        return view('admin.settings.promotions.create', compact('products'));
    }

    public function createPromotion(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'discount' => 'required|numeric|min:0|max:99', // thêm validate cho discount
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'isActive' => 'sometimes|boolean',
            'productIds' => 'required|array',
            'productIds.*' => 'required|string',

        ]);

        // Fix isActive và format date
        $validated['isActive'] = $request->has('isActive');
        $validated['startDate'] = date('c', strtotime($validated['startDate']));
        $validated['endDate'] = date('c', strtotime($validated['endDate']));

        $result = $this->promotionService->createPromotion($validated);


        if (!empty($result['success']) && $result['success'] == true) {
            return redirect()->route('admin.settings.promotions.index', ['tab' => 'promotions'])
                ->with('success', 'Thêm promotion thành công!')
                ->with('activeTab', 'promotions');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi thêm promotion');
    }


    // =====================
// Controller
// =====================
    public function editPromotionForm($id)
    {
        $promotion = collect($this->promotionService->getAllPromotions())
            ->firstWhere('id', $id);

        if (!$promotion) {
            return redirect()->route('admin.settings.promotions.index', ['tab' => 'promotions'])
                ->with('error', 'Promotion không tồn tại!')
                ->with('activeTab', 'promotions');
        }

        // Lấy tất cả sản phẩm và đánh dấu sản phẩm đã thuộc promotion
        $products = $this->productService->getAllProducts();
        foreach ($products as &$p) {
            $p['isSelected'] = ($p['promotionId'] ?? null) === $promotion['id'];
        }

        return view('admin.settings.promotions.edit', compact('promotion', 'products'));
    }

    public function updatePromotion(Request $request, $id)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'discount' => 'required|numeric|min:0|max:99',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'isActive' => 'sometimes|boolean',
            'productIds' => 'required|array',
            'productIds.*' => 'required|string',
        ]);

        $validated['isActive'] = $request->has('isActive');
        $validated['startDate'] = date('c', strtotime($validated['startDate']));
        $validated['endDate'] = date('c', strtotime($validated['endDate']));

        $result = $this->promotionService->updatePromotion($id, $validated);

        if (!empty($result['success']) && $result['success'] == true) {
            return redirect()->route('admin.settings.promotions.index', ['tab' => 'promotions'])
                ->with('success', 'Cập nhật promotion thành công!')
                ->with('activeTab', 'promotions');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi cập nhật promotion');
    }



    public function deletePromotion($id)
    {
        $result = $this->promotionService->deletePromotion($id);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.settings.promotions.index', ['tab' => 'promotions'])
                ->with('success', 'Xóa promotion thành công!')
                ->with('activeTab', 'promotions');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi xóa promotion');
    }


    /* ================== SERVICE CENTERS ================== */

    // Form tạo mới
    public function createCenterForm()
    {
        return view('admin.settings.centers.create');
    }

    // POST tạo mới
    public function createCenter(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'openHours' => 'nullable|string|max:255',
        ]);

        $result = $this->centerService->createCenter($validated);

        if (!empty($result['success']) && $result['success'] == true) {
            return redirect()->route('admin.settings.centers.index')
                ->with('success', 'Thêm trung tâm dịch vụ thành công!')
                ->with('activeTab', 'centers');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi thêm trung tâm dịch vụ');
    }

    // Form edit
    public function editCenterForm($id)
    {
        $center = collect($this->centerService->getAllCenters())
            ->firstWhere('id', $id);

        if (!$center) {
            return redirect()->route('admin.settings.centers.index')
                ->with('error', 'Trung tâm dịch vụ không tồn tại!')
                ->with('activeTab', 'centers');
        }

        return view('admin.settings.centers.edit', compact('center'));
    }

    // PUT cập nhật
    public function updateCenter(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'openHours' => 'nullable|string|max:255',
        ]);

        $result = $this->centerService->updateCenter($id, $validated);

        if (!empty($result['success']) && $result['success'] == true) {
            return redirect()->route('admin.settings.centers.index')
                ->with('success', 'Cập nhật trung tâm dịch vụ thành công!')
                ->with('activeTab', 'centers');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi cập nhật trung tâm dịch vụ');
    }

    // DELETE
    public function deleteCenter($id)
    {
        $result = $this->centerService->deleteCenter($id);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.settings.centers.index')
                ->with('success', 'Xóa trung tâm dịch vụ thành công!')
                ->with('activeTab', 'centers');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi xóa trung tâm dịch vụ');
    }
    /* ================== SERIALS ================== */

    // Form tạo serial mới
    public function createSerialForm()
    {
        $products = $this->productService->getAllProducts() ?: [];
        return view('admin.settings.serials.create', compact('products'));
    }

    // Tạo serial
    public function createSerial(Request $request)
    {
        $validated = $request->validate([
            'productId' => 'required|string',
            'serial' => 'required|string|max:100',
            'soldToOrderId' => 'nullable|string',
            'soldAt' => 'nullable|date',
            'registeredBy' => 'nullable|string',
            'registeredAt' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $result = $this->productSerialService->createSerial($validated);

        if (!empty($result['success']) && $result['success'] == true) {
            return redirect()->route('admin.settings.serials.index')
                ->with('success', 'Thêm serial thành công!')
                ->with('activeTab', 'serials');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi thêm serial');
    }

    // Form edit serial
    public function editSerialForm($id)
    {
        $products = $this->productService->getAllProducts() ?: [];
        $serial = collect($this->productSerialService->getAllSerials())
            ->firstWhere('id', $id);

        if (!$serial) {
            return redirect()->route('admin.settings.serials.index')
                ->with('error', 'Serial không tồn tại!')
                ->with('activeTab', 'serials');
        }

        return view('admin.settings.serials.edit', compact('serial', 'products'));
    }

    // Cập nhật serial
    public function updateSerial(Request $request, $id)
    {
        $validated = $request->validate([
            'productId' => 'required|string',
            'serial' => 'required|string|max:100',
            'soldToOrderId' => 'nullable|string',
            'soldAt' => 'nullable|date',
            'registeredBy' => 'nullable|string',
            'registeredAt' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $result = $this->productSerialService->updateSerial($id, $validated);

        if (!empty($result['success']) && $result['success'] == true) {
            return redirect()->route('admin.settings.serials.index')
                ->with('success', 'Cập nhật serial thành công!')
                ->with('activeTab', 'serials');

        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi cập nhật serial');
    }

    // Xóa serial
    public function deleteSerial($id)
    {
        $result = $this->productSerialService->deleteSerial($id);

        if (!empty($result['success']) && $result['success'] == true) {
            return redirect()->route('admin.settings.serials.index')
                ->with('success', 'Xóa serial thành công!')
                ->with('activeTab', 'serials');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi xóa serial');
    }

    public function deleteLog($id)
    {
        $result = $this->adminLogService->deleteLog($id);

        if (isset($result['success']) && $result['success']) {
            return redirect()->back()->with('success', 'Log deleted successfully.')
                ->with('activeTab', 'logs');
        }

        return redirect()->back()->with('error', 'Failed to delete log.');
    }


}
