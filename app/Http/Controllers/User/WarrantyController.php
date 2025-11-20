<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WarrantyController extends Controller
{
    private $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY5MTVlM2JlZGQ1MzM2NDg5NzYyYjcyMCIsIm5hbWUiOiJOZ3V5ZW4gVmFuIEEiLCJlbWFpbCI6ImFiY0BnbWFpbC5jb20iLCJyb2xlIjoiQ1VTVE9NRVIiLCJpYXQiOjE3NjM2MTYzMTEsImV4cCI6MTc2NDIyMTExMX0.q3UeOcnwKfG0-JjBP-sY7RzTbuB0myHLMabVCR2MPAg";

    /* ------------------------------------------------------
        API GET Helper
    ------------------------------------------------------ */
    private function apiGet($url, $query = [])
    {
        try {
            $res = Http::withToken($this->token)->get($url, $query);
            return $res->ok() ? $res->json() : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /* ------------------------------------------------------
        Helper: Lấy serial từ order item
    ------------------------------------------------------ */
    private function extractSerial($item)
    {
        if (!empty($item['productSnapshot']['sku'])) {
            return $item['productSnapshot']['sku'];
        }

        if (!empty($item['productSerial'])) {
            return $item['productSerial'];
        }

        return null;
    }

    /* ======================================================
        TRANG CHÍNH – GHÉP ORDERS + CLAIMS
    ====================================================== */
    public function index()
    {
        $claims = [];
        $purchased = [];
        $orderSerialMap = [];

        /* -------------------------------------------
            1. Lấy orders → map serial + purchased list
        ------------------------------------------- */
        $orderRes = $this->apiGet("http://localhost:3000/api/v1/orders/me");

        if ($orderRes && isset($orderRes['data'])) {

            foreach ($orderRes['data'] as $order) {
                foreach ($order['items'] as $item) {

                    $serial = $this->extractSerial($item);

                    // Map để ghép serial vào claim sau này
                    $orderSerialMap[$order['id']][$item['productId']] = $serial;

                    // Push purchased list
                    $purchased[] = [
                        "orderId"     => $order['id'],
                        "productId"   => $item['productId'],
                        "productName" => $item['productSnapshot']['name'] ?? $item['name'],
                        "serials"     => [$serial],
                        "quantity"    => $item['quantity'],
                        "purchasedAt" => $order['createdAt']
                    ];
                }
            }
        }

        /* -------------------------------------------
            2. Lấy Claims và ghép thêm serial
        ------------------------------------------- */
        $claimRes = $this->apiGet("http://localhost:3000/api/v1/warranty/me");

        if ($claimRes && isset($claimRes['data'])) {

            foreach ($claimRes['data'] as $c) {

                // Serial gốc từ claim
                $serial = $c['productSerial'] ?? null;

                // Nếu claim không có serial → lấy từ order
                if (!$serial) {
                    $oid = $c['orderId'] ?? null;
                    $pid = $c['productId'] ?? null;

                    if ($oid && $pid && isset($orderSerialMap[$oid][$pid])) {
                        $serial = $orderSerialMap[$oid][$pid];
                    }
                }

                $claims[] = [
                    "id"          => $c['id'],
                    "productName" => $c['productName'],
                    "serial"      => $serial ?? "Không có",
                    "description" => $c['issueDesc'] ?? "Không có mô tả",
                    "status"      => $c['status'],
                    "estimate"    => $c['estimateDate'] ?? null,
                    "createdAt"   => date('Y-m-d', strtotime($c['createdAt'])),
                ];
            }
        }

        return view("user.warranty.warranty", compact("claims", "purchased"));
    }

    /* ======================================================
        CHECK SERIAL
    ====================================================== */
    public function checkSerial(Request $request)
    {
        $request->validate(["serial_number" => "required"]);
        $serial = $request->serial_number;

        $orderRes = $this->apiGet("http://localhost:3000/api/v1/orders/me");
        $found = null;

        if ($orderRes && isset($orderRes['data'])) {

            foreach ($orderRes['data'] as $order) {
                foreach ($order['items'] as $item) {

                    $sku = $item['productSnapshot']['sku'] ?? null;

                    if ($sku === $serial) {

                        $found = [
                            "name"        => $item['productSnapshot']['name'],
                            "orderId"     => $order['id'],
                            "serial"      => $serial,
                            "purchasedAt" => $order['createdAt'],
                        ];

                        break 2;
                    }
                }
            }
        }

        if (!$found) {
            return back()->withErrors(["msg" => "Serial không tồn tại hoặc không hợp lệ!"]);
        }

        /* Tìm lý do bảo hành gần nhất */
        $claimRes = $this->apiGet("http://localhost:3000/api/v1/warranty/me");
        $lastReason = null;
        $lastDate = null;

        if ($claimRes && isset($claimRes['data'])) {

            foreach ($claimRes['data'] as $c) {

                if (($c['productSerial'] ?? null) === $serial) {

                    if (!$lastDate || strtotime($c['createdAt']) > strtotime($lastDate)) {
                        $lastDate = $c['createdAt'];
                        $lastReason = $c['issueDesc'];
                    }
                }
            }
        }

        $found['lastReason'] = $lastReason;

        return back()->with("productInfo", $found);
    }

    /* ======================================================
        SUBMIT CLAIM
    ====================================================== */
    public function submitClaim(Request $request)
    {
        $request->validate([
            "serial_number" => "required",
            "description"   => "required",
        ]);

        $serial = $request->serial_number;
        $orderRes = $this->apiGet("http://localhost:3000/api/v1/orders/me");
        $found = null;

        /* Tìm sản phẩm ứng với serial */
        if ($orderRes && isset($orderRes['data'])) {

            foreach ($orderRes['data'] as $order) {
                foreach ($order['items'] as $item) {

                    if (($item['productSnapshot']['sku'] ?? null) === $serial) {

                        $found = [
                            "orderId"     => $order['id'],
                            "productId"   => $item['productId'],
                            "productName" => $item['productSnapshot']['name'],
                            "purchasedAt" => $order['createdAt']
                        ];

                        break 2;
                    }
                }
            }
        }

        if (!$found) {
            return back()->withErrors(["msg" => "Không tìm thấy sản phẩm với serial này!"]);
        }

        /* Gửi request tạo claim */
        $payload = [
            "orderId"       => $found['orderId'],
            "productId"     => $found['productId'],
            "productName"   => $found['productName'],
            "issueDesc"     => $request->description,
            "productSerial" => $serial,
            "purchasedAt"   => $found['purchasedAt'],
            "images"        => [],
        ];

        $res = Http::withToken($this->token)
            ->post("http://localhost:3000/api/v1/warranty/claim", $payload);

        if ($res->failed()) {
            return back()->withErrors(["msg" => "Gửi yêu cầu thất bại!"]);
        }

        return back()->with("success", "Gửi yêu cầu bảo hành thành công!");
    }
}
