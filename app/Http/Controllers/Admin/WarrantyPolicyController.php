<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use App\Services\WarrantyPolicyService;
use Illuminate\Http\Request;

class WarrantyPolicyController extends Controller
{
    protected $policyService;

    public function __construct(WarrantyPolicyService $policyService)
    {
        $this->policyService = $policyService;
    }

    /**
     * Trang danh sách chính sách bảo hành
     */
    // public function index()
    // {
    //     $policies = $this->policyService->getAllPolicies();

    //     return view('admin.warranty_policies.index', compact('policies'));
    // }
    public function index(Request $request)
    {
        // Lấy tất cả policies từ service và convert sang Collection
        $allPolicies = collect($this->policyService->getAllPolicies());

        // --- Phân trang thủ công ---
        $perPage = 6; // số policies / trang
        $page = $request->get('page', 1); // trang hiện tại

        $policies = new LengthAwarePaginator(
            $allPolicies->forPage($page, $perPage), // slice dữ liệu
            $allPolicies->count(),                  // tổng số policies
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()] // giữ query string
        );

        return view('admin.warranty_policies.index', compact('policies'));
    }


    /**
     * Trang thêm chính sách bảo hành
     */
    public function create()
    {
        return view('admin.warranty_policies.create');
    }

    /**
     * Lưu chính sách bảo hành mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'durationDays' => 'required|integer|min:0',
            'coverage' => 'required|string',
            'requiresSerial' => 'nullable|boolean',
        ]);

        $result = $this->policyService->createPolicy($validated);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.warranty_policies.index')
                ->with('success', 'Thêm chính sách bảo hành thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi thêm chính sách');
    }

    /**
     * Trang chỉnh sửa chính sách bảo hành
     */
    public function edit($id)
    {
        $policies = $this->policyService->getAllPolicies();
        $policy = collect($policies)->firstWhere('id', $id);

        if (!$policy) {
            return redirect()->route('admin.warranty_policies.index')
                ->with('error', 'Chính sách không tồn tại');
        }

        return view('admin.warranty_policies.edit', compact('policy'));
    }

    /**
     * Cập nhật chính sách bảo hành
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'durationDays' => 'required|integer|min:0',
            'coverage' => 'required|string',
            'requiresSerial' => 'nullable|boolean',
        ]);

        $result = $this->policyService->updatePolicy($id, $validated);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.warranty_policies.index')
                ->with('success', 'Cập nhật chính sách bảo hành thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi cập nhật chính sách');
    }

    /**
     * Xóa chính sách bảo hành
     */
    public function destroy($id)
    {
        $result = $this->policyService->deletePolicy($id);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.warranty_policies.index')
                ->with('success', 'Xóa chính sách bảo hành thành công!');
        }

        return back()->with('error', $result['message'] ?? 'Lỗi khi xóa chính sách');
    }
}
