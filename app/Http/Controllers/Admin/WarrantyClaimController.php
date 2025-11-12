<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use App\Services\WarrantyService;
use Illuminate\Http\Request;

class WarrantyClaimController extends Controller
{
    protected $warrantyService;

    public function __construct(WarrantyService $warrantyService)
    {
        $this->warrantyService = $warrantyService;
    }

    /**
     * Hiển thị danh sách tất cả claim
     */
    // public function index(Request $request)
    // {
    //     $status = $request->query('status');
    //     $claims = $this->warrantyService->getClaims($status) ?? [];

    //     // Trả về view Blade
    //     return view('admin.warranty.index', compact('claims'));
    // }

    public function index(Request $request)
    {
        $status = $request->query('status');

        // Lấy tất cả claims từ service và convert sang Collection
        $allClaims = collect($this->warrantyService->getClaims($status) ?? []);

        // --- Phân trang thủ công ---
        $perPage = 5; // số claims / trang
        $page = $request->get('page', 1); // trang hiện tại

        $claims = new LengthAwarePaginator(
            $allClaims->forPage($page, $perPage), // slice dữ liệu
            $allClaims->count(),                  // tổng số claims
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()] // giữ query string
        );

        // Trả về view Blade
        return view('admin.warranty.index', compact('claims'));
    }

    /**
     * Hiển thị chi tiết claim
     */
    public function show(string $id)
    {
        $claim = $this->warrantyService->getClaimById($id);

        if (!$claim) {
            return redirect()->back()->with('error', 'Yêu cầu bảo hành không tồn tại.');
        }

        return view('admin.warranty.show', compact('claim'));
    }

    /**
     * Cập nhật claim
     */
    public function update(Request $request, string $id)
    {
        $status = $request->input('status');
        $note = $request->input('note');

        $claim = $this->warrantyService->updateClaim($id, $status, $note);

        if ($request->ajax()) {
            if ($claim) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật yêu cầu bảo hành thành công.',
                    'data' => $claim
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Cập nhật thất bại, vui lòng thử lại.'
                ], 400);
            }
        }

        // Nếu không phải AJAX thì redirect như cũ
        return redirect()->back()->with($claim ? 'success' : 'error', $claim ? 'Cập nhật yêu cầu bảo hành thành công.' : 'Cập nhật thất bại.');
    }
}
