<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WarrantyServiceUser;
use App\Services\SerialServiceUser;
use App\Services\WarrantyPolicyServiceUser;
use App\Services\OrderServiceUser;
use Illuminate\Pagination\LengthAwarePaginator;

class WarrantyController extends Controller
{
    protected $warrantyServiceUser;
    protected $serialServiceUser;
    protected $warrantyPolicyServiceUser;
    protected $orderServiceUser;

    public function __construct(
        WarrantyServiceUser $warrantyServiceUser,
        SerialServiceUser $serialServiceUser,
        WarrantyPolicyServiceUser $warrantyPolicyServiceUser,
        OrderServiceUser $orderServiceUser
    ) {
        $this->warrantyServiceUser = $warrantyServiceUser;
        $this->serialServiceUser = $serialServiceUser;
        $this->warrantyPolicyServiceUser = $warrantyPolicyServiceUser;
        $this->orderServiceUser = $orderServiceUser;
    }

    /**
     * Trang chính bảo hành
     */
    public function index(Request $request)
    {
        $orders = $this->orderServiceUser->getMyOrders();
        $claimList = $this->warrantyServiceUser->getMyClaims();

        // Lấy tất cả serial của user
        $allSerials = $this->serialServiceUser->getMySerials();

        // Xử lý userSerials và purchased (giữ nguyên đoạn code của bạn) ...
        $userSerials = [];
        foreach ($orders as $order) {
            foreach ($order["items"] as $item) {
                $serialsForItem = array_filter($allSerials, fn($s) => $s['productId'] === $item['productId'] && $s['orderId'] === $order['id']);
                foreach ($serialsForItem as $s) {
                    $userSerials[] = [
                        "productName" => $item["productSnapshot"]["name"] ?? $item["name"],
                        "serialId" => $s['serialId'],
                        "serialCode" => $s['serialCode'],
                        "productId" => $item['productId'],
                        "orderId" => $order['id'],
                        "purchasedAt" => date("d/m/Y", strtotime($order["createdAt"]))
                    ];
                }
            }
        }

        $latestClaimBySerial = [];
        foreach ($claimList as $c) {
            $s = $c['productSerial'] ?? '';
            if (!isset($latestClaimBySerial[$s]) || strtotime($c["createdAt"]) > strtotime($latestClaimBySerial[$s]["createdAt"])) {
                $latestClaimBySerial[$s] = $c;
            }
        }

        $purchased = [];
        foreach ($orders as $order) {
            foreach ($order["items"] as $item) {
                $serialsForItem = array_filter($allSerials, fn($s) => $s['productId'] === $item['productId'] && $s['orderId'] === $order['id']);
                $serial = !empty($serialsForItem) ? array_values($serialsForItem)[0]['serialId'] : null;
                $purchased[] = [
                    "orderId" => $order["id"],
                    "productId" => $item["productId"],
                    "productName" => $item["productSnapshot"]["name"] ?? $item["name"],
                    "quantity" => $item["quantity"],
                    "purchasedAt" => date("d/m/Y", strtotime($order["createdAt"])),
                    "serialCode" => !empty($serialsForItem) ? array_values($serialsForItem)[0]['serialCode'] : null,
                    "latestClaim" => $latestClaimBySerial[$serial] ?? null
                ];
            }
        }

        // ===== Thêm phần phân trang =====
        $allClaims = collect($claimList);
        $perPage = 5;
        $page = $request->get('page', 1);
        $paginatedClaims = new LengthAwarePaginator(
            $allClaims->forPage($page, $perPage),
            $allClaims->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view("user.warranty.warranty", [
            "claims" => $paginatedClaims,
            "userSerials" => $userSerials,
            "purchased" => $purchased,
        ]);
    }

    /**
     * Kiểm tra serial
     */
    /**
     * Kiểm tra serial và bảo hành
     */
    public function checkSerial(Request $request)
    {
        $request->validate(["serial_number" => "required"]);
        $serialCode = trim($request->serial_number);

        // Gọi service để check bảo hành
        $result = $this->serialServiceUser->checkSerialWarranty($serialCode);

        if (!($result['success'] ?? false)) {
            return back()->withErrors(["msg" => $result['message'] ?? 'Không thể kiểm tra bảo hành']);
        }

        $data = $result['data'] ?? null;

        if (!$data) {
            return back()->withErrors(["msg" => "Không tìm thấy thông tin bảo hành"]);
        }

        // Map trạng thái sang tiếng Việt
        $statusVN = match ($data['status'] ?? '') {
            'VALID' => 'Còn hạn',
            'EXPIRED' => 'Hết hạn',
            default => 'Không xác định',
        };

        $productInfo = [
            'serialCode' => $data['serial'],
            'productName' => $data['productName'],
            'soldAt' => date("d/m/Y", strtotime($data['soldAt'])),
            'warrantyStatus' => $statusVN,
            'daysLeft' => max(0, $data['daysLeft'] ?? 0),
        ];

        return back()->with("productInfo", $productInfo);
    }
    /**
     * Submit yêu cầu bảo hành
     */
    public function submitClaim(Request $request)
    {
        $request->validate([
            "serial_number" => "required",
            "description" => "required|string",
        ]);

        $serialCode = $request->serial_number;
        $description = $request->description;

        $allSerials = $this->serialServiceUser->getMySerials();
        $serial = collect($allSerials)->firstWhere('serialCode', $serialCode);

        if (!$serial || empty($serial['productId']) || empty($serial['orderId'])) {
            return back()->withErrors(["msg" => "Serial không hợp lệ hoặc thiếu thông tin sản phẩm/đơn hàng"]);
        }

        $payload = [
            "productName" => $serial['productName'] ?? 'Sản phẩm',
            "productSerial" => $serial['serialCode'],
            "issueDesc" => $description,
            "productId" => $serial['productId'],
            "orderId" => $serial['orderId'],
        ];

        $result = $this->warrantyServiceUser->submitClaim($payload);

        if ($result['success'] ?? false) {
            return back()->with("success", "Gửi yêu cầu bảo hành thành công!");
        }

        return back()->withErrors(["msg" => $result['message'] ?? "Lỗi khi gửi yêu cầu bảo hành"]);
    }

    /**
     * Trang tạo claim mới
     */
    public function create()
    {
        $orders = $this->orderServiceUser->getMyOrders(); // Lấy tất cả đơn hàng của user
        $userSerials = [];

        foreach ($orders as $order) {
            foreach ($order["items"] as $item) {
                $serial = $this->serialServiceUser->getSerialForItem($item, $order["id"]);
                if ($serial) {
                    $userSerials[] = [
                        "productName" => $item["productSnapshot"]["name"] ?? $item["name"],
                        "serialId" => $serial,
                        "serialCode" => $serial['serialCode'],
                        "productId" => $item['productId'],
                        "orderId" => $order["id"],
                        "purchasedAt" => $order["createdAt"],
                    ];
                }
            }
        }

        return view('user.warranty.create', compact('userSerials'));
    }
}
