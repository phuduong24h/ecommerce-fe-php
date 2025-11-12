<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Hiển thị danh sách người dùng
     */
    // public function index(Request $request)
    // {
    //     $search = $request->input('search');
    //     $users = $this->service->getAll($search);

    //     $roleClasses = [
    //         'admin' => 'bg-red-100 text-red-700',
    //         'customer' => 'bg-blue-100 text-blue-700',
    //         'support' => 'bg-purple-100 text-purple-700',
    //     ];

    //     $statusClasses = [
    //         'active' => 'bg-emerald-100 text-emerald-700',
    //         'inactive' => 'bg-gray-100 text-gray-700',
    //         'suspended' => 'bg-red-100 text-red-700',
    //     ];

    //     return view('admin.users.index', compact('users', 'roleClasses', 'statusClasses', 'search'));
    // }
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Lấy tất cả người dùng từ service và convert sang Collection
        $allUsers = collect($this->service->getAll($search));

        $roleClasses = [
            'admin' => 'bg-red-100 text-red-700',
            'customer' => 'bg-blue-100 text-blue-700',
            'support' => 'bg-purple-100 text-purple-700',
        ];

        $statusClasses = [
            'active' => 'bg-emerald-100 text-emerald-700',
            'inactive' => 'bg-gray-100 text-gray-700',
            'suspended' => 'bg-red-100 text-red-700',
        ];

        // --- Phân trang thủ công ---
        $perPage = 5; // số người dùng / trang
        $page = $request->get('page', 1); // trang hiện tại

        $users = new LengthAwarePaginator(
            $allUsers->forPage($page, $perPage), // slice dữ liệu
            $allUsers->count(),                  // tổng số người dùng
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()] // giữ query string
        );

        return view('admin.users.index', compact('users', 'roleClasses', 'statusClasses', 'search'));
    }

    /**
     * Form tạo mới người dùng
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu người dùng mới
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,customer,support',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $result = $this->service->create($data);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.users.index')
                ->with('success', 'Thêm người dùng thành công');
        }

        return back()->with('error', 'Có lỗi khi thêm người dùng');
    }

    /**
     * Form sửa người dùng
     */
    public function edit($id)
    {
        $user = $this->service->getById($id);
        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'Không tìm thấy người dùng');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật người dùng
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,customer,support',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $result = $this->service->update($id, $data);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.users.index')
                ->with('success', 'Cập nhật người dùng thành công');
        }

        return back()->with('error', 'Có lỗi khi cập nhật người dùng');
    }

    /**
     * Xóa người dùng
     */
    public function destroy($id)
    {
        $result = $this->service->delete($id);

        if ($result['success'] ?? false) {
            return redirect()->route('admin.users.index')
                ->with('success', 'Xóa người dùng thành công');
        }

        return back()->with('error', 'Có lỗi khi xóa người dùng');
    }
}
