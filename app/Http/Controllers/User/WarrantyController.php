<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WarrantyController extends Controller
{
    // XÓA biến private $token cứng ở đây vì không dùng được cho nhiều user
    
    /* ------------------------------------------------------
       HELPER: GỌI API VỚI TOKEN CỦA USER ĐANG ĐĂNG NHẬP
    ------------------------------------------------------ */
    private function apiGet($path)
    {
        try {
            // 1. Lấy Token từ Session (Token của người đang đăng nhập)
            $token = session('user_token');
            
            // 2. Lấy URL gốc từ file cấu hình (đã setup ở các bước trước)
            // Kết quả sẽ là: http://localhost:3000/api/v1/orders/me
            $baseUrl = config('services.api.url') . '/api/v1';
            $url = $baseUrl . '/' . ltrim($path, '/');

            // 3. Gọi API
            $res = Http::withToken($token)->timeout(10)->get($url);
            
            return $res->ok() ? $res->json() : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /* ======================================================
        HIỂN THỊ TRANG BẢO HÀNH
    ====================================================== */
    public function index()
    {
        // 1. Lấy danh sách claim (Lịch sử bảo hành)
        $claimRes = $this->apiGet("warranty/me"); // Gọi ngắn gọn endpoint
        $claimList = $claimRes["data"] ?? [];

        // claim mới nhất theo serial
        $latestClaimBySerial = [];

        foreach ($claimList as $c) {
            $serial = $c["productSerial"];

            if (
                !isset($latestClaimBySerial[$serial]) ||
                strtotime($c["createdAt"]) > strtotime($latestClaimBySerial[$serial]["createdAt"])
            ) {

                $latestClaimBySerial[$serial] = [
                    "serial"      => $serial,
                    "productName" => $c["productName"],
                    "status"      => $c["status"],
                    "description" => $c["issueDesc"] ?? "",
                    "createdAt"   => date("d/m/Y", strtotime($c["createdAt"])),
                    "estimate"    => $c["estimatedAt"] ?? null,
                    "claimId"     => $c["id"]
                ];
            }
        }

        // 2. Lấy order (Lịch sử mua hàng)
        $orderRes = $this->apiGet("orders/me");
        $orders = $orderRes["data"] ?? [];

        $purchased = [];

        foreach ($orders as $order) {

            foreach ($order["items"] as $item) {

                $snapshot = $item["productSnapshot"] ?? null;

                $productName =
                    $snapshot["name"]
                    ?? $item["name"]
                    ?? "Tên sản phẩm không xác định";

                $serial =
                    $snapshot["sku"] // Giả sử SKU được dùng làm Serial
                    ?? $item["productSerial"] // Hoặc field riêng nếu có
                    ?? null;

                // Chỉ hiển thị nếu sản phẩm có Serial (đồ điện tử)
                // if ($serial) {
                    $purchased[] = [
                        "orderId"     => $order["id"],
                        "productId"   => $item["productId"],
                        "productName" => $productName,
                        "serial"      => $serial,
                        "quantity"    => $item["quantity"],
                        "purchasedAt" => date("d/m/Y", strtotime($order["createdAt"])),
                        "latestClaim" => $latestClaimBySerial[$serial] ?? null
                    ];
                //}
            }
        }

        return view("user.warranty.warranty", compact("purchased", "latestClaimBySerial", "claimList"));
    }


    /* ======================================================
        KIỂM TRA SỐ SERIAL — HIỂN THỊ TOÀN BỘ LỊCH SỬ
    ====================================================== */
    public function checkSerial(Request $request)
    {
        $request->validate(["serial_number" => "required"]);
        $serial = $request->serial_number;

        // BƯỚC 1: Phải kiểm tra trong ORDERS trước xem User có mua nó không
        $orderRes = $this->apiGet("orders/me");
        $orders = $orderRes["data"] ?? [];
        $productInfo = null;

        foreach ($orders as $order) {
            foreach ($order["items"] as $item) {
                $itemSerial = $item["productSnapshot"]["sku"] ?? $item["productSerial"] ?? '';
                if ($itemSerial === $serial) {
                    $productInfo = [
                        "name"        => $item["productSnapshot"]["name"] ?? $item["name"],
                        "serial"      => $serial,
                        "orderId"     => $order["id"],
                        "purchasedAt" => date("d/m/Y", strtotime($order["createdAt"])),
                    ];
                    break 2; // Tìm thấy rồi thì thoát vòng lặp
                }
            }
        }

        if (!$productInfo) {
            return back()->withErrors(["msg" => "Serial không tồn tại hoặc không phải sản phẩm bạn đã mua."]);
        }

        // BƯỚC 2: Lấy lịch sử bảo hành của Serial này
        $claimRes = $this->apiGet("warranty/me");
        $claimList = $claimRes["data"] ?? [];

        // Lọc các claim liên quan đến serial này
        $claimsForSerial = collect($claimList)
            ->where("productSerial", $serial)
            ->sortByDesc("createdAt")
            ->values()
            ->toArray();

        // Trả về View kèm Info sản phẩm và Lịch sử sửa chữa
        return back()
            ->with("productInfo", $productInfo)
            ->with("serialClaims", $claimsForSerial);
    }


    /* ======================================================
        GỬI YÊU CẦU BẢO HÀNH
    ====================================================== */
    public function submitClaim(Request $request)
    {
        $request->validate([
            "serial_number" => "required",
            "description"   => "required",
        ]);

        $serial = $request->serial_number;

        // SỬA LOGIC: Lấy thông tin từ ORDER (đơn hàng) chứ không phải từ Claim cũ
        // Vì nếu bảo hành lần đầu thì chưa có trong warranty/me
        $orderRes = $this->apiGet("orders/me");
        $orders = $orderRes["data"] ?? [];

        $match = null;

        // Tìm sản phẩm trong danh sách đã mua
        foreach ($orders as $order) {
            foreach ($order["items"] as $item) {
                $itemSerial = $item["productSnapshot"]["sku"] ?? $item["productSerial"] ?? '';
                
                if ($itemSerial === $serial) {
                    $match = [
                        "orderId"     => $order["id"],
                        "productId"   => $item["productId"],
                        "productName" => $item["productSnapshot"]["name"] ?? $item["name"],
                        "purchasedAt" => $order["createdAt"], // Giữ nguyên định dạng gốc để gửi API
                    ];
                    break 2;
                }
            }
        }

        if (!$match) {
            return back()->withErrors(["msg" => "Serial không hợp lệ hoặc bạn chưa mua sản phẩm này!"]);
        }

        // Chuẩn bị Payload gửi lên Node.js
        $payload = [
            "orderId"       => $match["orderId"],
            "productId"     => $match["productId"],
            "productName"   => $match["productName"],
            "productSerial" => $serial,
            "purchasedAt"   => $match["purchasedAt"],
            "issueDesc"     => $request->description,
            "images"        => [],
        ];

        // Gửi POST request (Dùng session token)
        $url = config('services.api.url') . '/api/v1/warranty/claim';
        $token = session('user_token');

        $response = Http::withToken($token)->post($url, $payload);

        if ($response->successful()) {
            return back()->with("success", "Gửi yêu cầu bảo hành thành công!");
        } else {
            return back()->withErrors(["msg" => "Lỗi hệ thống: " . ($response->json()['message'] ?? 'Không thể gửi yêu cầu')]);
        }
    }
}