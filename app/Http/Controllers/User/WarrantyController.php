<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ProductSerialService;
//văn hải

class WarrantyController extends Controller
{
    // --- HELPER: Lấy dữ liệu GET với Token User ---
    private function apiGet($endpoint)
    {
        try {
            $token = session('user_token');
            $baseUrl = config('services.api.url') . '/api/v1';
            $res = Http::withToken($token)->timeout(10)->get($baseUrl . '/' . ltrim($endpoint, '/'));
            return $res->ok() ? $res->json() : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * --- HELPER QUAN TRỌNG: LẤY HOẶC TẠO SERIAL ---
     * Giúp hiển thị serial cho cả đơn hàng cũ chưa có dữ liệu
     */
    private function getProductSerial($item, $orderId)
    {

        $serialService = new ProductSerialService();

        // 1. Ưu tiên serial trong productSnapshot
        if (!empty($item['productSnapshot']['sku'])) {
            return $item['productSnapshot']['sku'];
        }

        // 2. Ưu tiên serial đã lưu trong item
        if (!empty($item['productSerial'])) {
            return $item['productSerial'];
        }

        // 3. Kiểm tra serial trong backend API
        $allSerials = $serialService->getAllSerials();
        $productSerials = array_filter($allSerials, fn($s) => ($s['productId'] ?? null) === ($item['productId'] ?? null));
        if (!empty($productSerials)) {
            // Lấy serial mới nhất
            usort($productSerials, fn($a, $b) => strtotime($b['updatedAt']) <=> strtotime($a['updatedAt']));
            return $productSerials[0]['serial'] ?? 'UNKNOWN';
        }

        // 4. Nếu không có serial thật → trả UNKNOWN
        return 'UNKNOWN';
    }

    /* ======================================================
        TRANG CHÍNH BẢO HÀNH
    ====================================================== */
    public function index()
    {
        // 1. Lấy Orders
        $orderRes = $this->apiGet("orders/me");
        $orders = $orderRes["data"] ?? [];

        // 2. Lấy Claims
        $claimRes = $this->apiGet("warranty/me");
        $claimList = $claimRes["data"] ?? [];

        // Map claim mới nhất
        $latestClaimBySerial = [];
        foreach ($claimList as $c) {
            $s = $c['productSerial'];
            if (!isset($latestClaimBySerial[$s]) || strtotime($c["createdAt"]) > strtotime($latestClaimBySerial[$s]["createdAt"])) {
                $latestClaimBySerial[$s] = $c;
            }
        }

        // 3. Ghép danh sách
        $purchased = [];

        foreach ($orders as $order) {
            foreach ($order["items"] as $item) {

                // DÙNG HELPER ĐỂ LẤY SERIAL (THẬT HOẶC ẢO)
                $serial = $this->getProductSerial($item, $order["id"]);

                $purchased[] = [
                    "orderId" => $order["id"],
                    "productId" => $item["productId"],
                    "productName" => $item["productSnapshot"]["name"] ?? $item["name"],
                    "quantity" => $item["quantity"],
                    "purchasedAt" => date("d/m/Y", strtotime($order["createdAt"])),
                    "serial" => $serial,
                    "latestClaim" => $latestClaimBySerial[$serial] ?? null
                ];
            }
        }

        return view("user.warranty.warranty", compact("purchased", "claimList"));
    }

    /* ======================================================
        KIỂM TRA SERIAL
    ====================================================== */
    public function checkSerial(Request $request)
    {
        $request->validate(["serial_number" => "required"]);
        $serial = trim($request->serial_number); // Xóa khoảng trắng thừa

        $orderRes = $this->apiGet("orders/me");
        $orders = $orderRes["data"] ?? [];

        $foundProduct = null;

        // Tìm trong đơn hàng (dùng logic getProductSerial để so sánh)
        foreach ($orders as $order) {
            foreach ($order["items"] as $item) {

                // Tính toán serial của item này
                $itemSerial = $this->getProductSerial($item, $order["id"]);

                if ($itemSerial === $serial) {
                    $foundProduct = [
                        "name" => $item["productSnapshot"]["name"] ?? $item["name"],
                        "serial" => $serial,
                        "orderId" => $order["id"],
                        "purchasedAt" => date("d/m/Y", strtotime($order["createdAt"])),
                    ];
                    break 2;
                }
            }
        }

        if (!$foundProduct) {
            return back()->withErrors(["msg" => "Serial không tồn tại trong lịch sử mua hàng của bạn."]);
        }

        // Lấy lịch sử bảo hành
        $claimRes = $this->apiGet("warranty/me");
        $claimList = $claimRes["data"] ?? [];

        $claimsForSerial = collect($claimList)
            ->where("productSerial", $serial)
            ->sortByDesc("createdAt")
            ->values()
            ->toArray();

        return back()
            ->with("productInfo", $foundProduct)
            ->with("serialClaims", $claimsForSerial);
    }

    /* ======================================================
        GỬI YÊU CẦU BẢO HÀNH
    ====================================================== */
    public function submitClaim(Request $request)
    {
        $request->validate([
            "serial_number" => "required",
            "description" => "required",
        ]);

        $serial = trim($request->serial_number);

        // 1. Tìm sản phẩm gốc từ Orders
        $orderRes = $this->apiGet("orders/me");
        $orders = $orderRes["data"] ?? [];

        $match = null;

        foreach ($orders as $order) {
            foreach ($order["items"] as $item) {

                // So sánh bằng Helper
                $itemSerial = $this->getProductSerial($item, $order["id"]);

                if ($itemSerial === $serial) {
                    $match = [
                        "orderId" => $order["id"],
                        "productId" => $item["productId"],
                        "productName" => $item["productSnapshot"]["name"] ?? $item["name"],
                        "purchasedAt" => $order["createdAt"]
                    ];
                    break 2;
                }
            }
        }

        if (!$match) {
            return back()->withErrors(["msg" => "Serial không hợp lệ!"]);
        }

        // 2. Gửi yêu cầu lên Backend
        // Backend Node.js sẽ lưu serial này (dù là serial ảo "OLD-...") vào DB
        $payload = [
            "orderId" => $match["orderId"],
            "productId" => $match["productId"],
            "productName" => $match["productName"],
            "productSerial" => $serial,
            "purchasedAt" => $match["purchasedAt"],
            "issueDesc" => $request->description,
            "images" => [],
        ];

        $token = session('user_token');
        $url = config('services.api.url') . '/api/v1/warranty/claim';

        try {
            $res = Http::withToken($token)->post($url, $payload);

            if ($res->successful()) {
                return back()->with("success", "Gửi yêu cầu bảo hành thành công!");
            } else {
                return back()->withErrors(["msg" => "Lỗi: " . ($res->json()['message'] ?? 'Backend từ chối yêu cầu')]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(["msg" => "Lỗi kết nối: " . $e->getMessage()]);
        }
    }
}