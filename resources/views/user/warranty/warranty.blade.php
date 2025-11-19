@extends('layouts.app')

@section('title', 'Bảo Hành Sản Phẩm')

@section('content')

<div class="max-w-5xl mx-auto px-4 py-8">

    <!-- PAGE HEADER -->
    <div class="flex items-center gap-4 mb-8">
        <div class="bg-blue-100 text-blue-600 p-3 rounded-2xl">
            <i class="fas fa-shield-alt text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bảo Hành Sản Phẩm</h1>
            <p class="text-gray-500 text-sm">Kiểm tra tình trạng bảo hành và yêu cầu bảo hành</p>
        </div>
    </div>

    <!-- TABS -->
    <div class="flex bg-gray-100 rounded-full p-1 mb-8">

        <button id="tabCheck"
            class="flex-1 py-3 rounded-full text-center font-semibold text-white
                   bg-gradient-to-r from-blue-600 to-blue-400 shadow">
            🔍 Kiểm Tra Bảo Hành
        </button>

        <button id="tabRequest"
            class="flex-1 py-3 rounded-full text-center font-semibold text-gray-600 hover:bg-gray-200 transition">
            📄 Yêu Cầu Bảo Hành
        </button>
    </div>



    <!-- ====================== -->
    <!-- CHECK WARRANTY SECTION -->
    <!-- ====================== -->
    <div id="checkSection">

        <!-- Search Serial Box -->
        <div class="border border-blue-100 bg-blue-50 rounded-2xl p-6 mb-8">

            <h3 class="text-gray-700 font-semibold mb-2">Nhập Số Serial Sản Phẩm</h3>

            <form method="POST" action="{{ route('warranty.check') }}">
                @csrf

                <div class="flex bg-white border rounded-full px-5 py-3 shadow-sm
                            items-center focus-within:ring-2 focus-within:ring-blue-300 transition">

                    <input id="serial_check_input" name="serial_number"
                        placeholder="Ví dụ: SN-12345-ABCD"
                        class="flex-1 outline-none bg-transparent text-gray-700 placeholder-gray-400">

                    <button type="submit"
                        class="ml-3 px-5 py-2 rounded-full bg-blue-600 text-white font-semibold
                               hover:bg-blue-700 flex items-center gap-2 transition">
                        <i class="fas fa-search"></i> Kiểm Tra
                    </button>
                </div>
            </form>

            <p class="text-sm text-gray-500 mt-2">Tìm số serial ở mặt sau hoặc dưới sản phẩm</p>
        </div>


        <!-- PRODUCT INFO RESULT -->
        @if(session('productInfo'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-8 shadow-sm">

            <p class="font-semibold text-green-700 mb-2 text-lg">🔎 Kết quả kiểm tra</p>

            <p><strong>Tên sản phẩm:</strong> {{ session('productInfo')['name'] }}</p>
            <p><strong>Serial:</strong> {{ session('productInfo')['serial'] }}</p>
            <p><strong>Order ID:</strong> {{ session('productInfo')['orderId'] }}</p>
            <p><strong>Ngày mua:</strong> {{ date('d/m/Y', strtotime(session('productInfo')['purchasedAt'])) }}</p>

            <hr class="my-3">

            <!-- SHOW LATEST CLAIM FOR THIS SERIAL -->
            @php
                $serial = session('productInfo')['serial'];
                $claimMatch = collect($claims)->firstWhere('serial', $serial);
            @endphp

            <p class="text-gray-800 font-medium">Lý do bảo hành gần nhất:</p>

            @if($claimMatch)
                <p><strong>Trạng thái:</strong> {{ $claimMatch['status'] }}</p>
                <p><strong>Lý do:</strong> {{ $claimMatch['description'] }}</p>
                <p><strong>Ngày gửi:</strong> {{ $claimMatch['createdAt'] }}</p>
            @else
                <p class="text-gray-500 italic">Chưa có yêu cầu bảo hành nào cho serial này.</p>
            @endif

        </div>
        @endif



        <!-- PURCHASED PRODUCT LIST -->
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Sản phẩm bạn đã mua</h2>

        @foreach ($purchased as $p)
        <div class="bg-white border rounded-xl p-5 mb-5 shadow-sm hover:shadow-md transition">

            <div class="flex justify-between items-center">
                <div>
                    <p class="text-lg font-semibold text-gray-900">{{ $p['productName'] }}</p>
                    <p class="text-gray-500 text-sm">Order ID: {{ $p['orderId'] }}</p>
                    <p class="text-gray-500 text-sm">
                        Ngày mua: {{ date('d/m/Y', strtotime($p['purchasedAt'])) }}
                    </p>
                </div>
                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm shadow">
                    SL: {{ $p['quantity'] }}
                </span>
            </div>

            <div class="mt-3">
                <p class="font-medium text-sm text-gray-700">Serial(s):</p>

                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach ($p['serials'] as $s)
                    <button
                        class="use-serial-btn px-4 py-1.5 rounded-full bg-gray-100 hover:bg-blue-50
                               border text-gray-700 shadow-sm transition text-sm"
                        data-serial="{{ $s }}">
                        <i class="fas fa-barcode text-blue-400"></i> {{ $s }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach

    </div>





    <!-- ====================== -->
    <!-- REQUEST CLAIM SECTION -->
    <!-- ====================== -->
    <div id="requestSection" class="hidden">

        <!-- Claim Form -->
        <div class="border border-gray-200 bg-white rounded-2xl p-6 shadow-sm">

            <h2 class="text-xl font-bold text-gray-800 mb-4">Submit a Warranty Claim</h2>

            <form method="POST" action="{{ route('warranty.claim') }}" class="space-y-5">
                @csrf

                <!-- Serial -->
                <div>
                    <label class="font-semibold text-gray-700 mb-1 block">Product Serial Number</label>
                    <input id="claim_serial_input" name="serial_number"
                        placeholder="SN-12345-ABCD"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none shadow-sm">
                </div>

                <!-- Issue -->
                <div>
                    <label class="font-semibold text-gray-700 mb-1 block">Describe the Issue</label>
                    <textarea name="description" rows="4"
                        placeholder="Describe the problem with your product..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none shadow-sm"></textarea>
                </div>

                <!-- Warning -->
                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl text-yellow-700 text-sm flex items-start gap-3">
                    <i class="fas fa-info-circle mt-1"></i>
                    <span>Please ensure the serial number is correct. Claims are processed in 3–5 business days.</span>
                </div>

                <!-- Submit Button -->
                <button
                    class="w-full py-3 font-semibold text-white rounded-xl shadow-md
                           bg-gradient-to-r from-purple-500 to-pink-500 hover:opacity-90 transition">
                    Submit Claim
                </button>
            </form>
        </div>


        <!-- Claim List -->
        <h2 class="text-xl font-bold text-gray-900 mt-8 mb-3">My Warranty Claims</h2>

        @php
            $badgeColor = [
                'approved' => 'bg-green-100 text-green-700',
                'in-progress' => 'bg-blue-100 text-blue-700',
                'pending' => 'bg-yellow-100 text-yellow-700',
                'rejected' => 'bg-red-100 text-red-700'
            ];
        @endphp

        @foreach($claims as $c)

        <div class="bg-white border rounded-xl p-5 mb-5 shadow-sm hover:shadow-md transition">

            <div class="flex justify-between items-center">
                <p class="text-lg font-semibold text-gray-900">{{ $c['productName'] }}</p>
                <span class="text-gray-500 text-sm">{{ $c['createdAt'] }}</span>
            </div>

            <span class="px-3 py-1 text-xs rounded-full font-semibold mt-2 inline-block shadow {{ $badgeColor[$c['status']] ?? 'bg-gray-100 text-gray-700' }}">
                {{ $c['status'] }}
            </span>

            <p class="mt-3 text-sm"><strong>Serial:</strong> {{ $c['productSerial'] ?? 'Không có' }}</p>
            <p class="mt-1 text-sm"><strong>Claim ID:</strong> {{ $c['id'] }}</p>

            <p class="text-gray-700 text-sm mt-1">
                <strong>Lý do bảo hành:</strong> {{ $c['description'] }}
            </p>

            @if($c['estimate'])
            <p class="text-blue-600 text-sm font-medium mt-2">
                Estimated resolution: {{ $c['estimate'] }}
            </p>
            @endif

        </div>

        @endforeach

    </div>

</div>




<script>
    const tabCheck = document.getElementById("tabCheck");
    const tabRequest = document.getElementById("tabRequest");
    const checkSection = document.getElementById("checkSection");
    const requestSection = document.getElementById("requestSection");

    function switchTab(tab) {
        if (tab === "check") {
            tabCheck.classList.add("bg-gradient-to-r", "from-blue-600", "to-blue-400", "text-white");
            tabRequest.classList.remove("bg-gradient-to-r", "from-purple-500", "to-pink-500", "text-white");

            checkSection.classList.remove("hidden");
            requestSection.classList.add("hidden");
        } else {
            tabRequest.classList.add("bg-gradient-to-r", "from-purple-500", "to-pink-500", "text-white");
            tabCheck.classList.remove("bg-gradient-to-r", "from-blue-600", "to-blue-400", "text-white");

            requestSection.classList.remove("hidden");
            checkSection.classList.add("hidden");
        }
    }

    tabCheck.onclick = () => switchTab("check");
    tabRequest.onclick = () => switchTab("request");

    // Click serial → autofill + switch tab
    document.querySelectorAll(".use-serial-btn").forEach(btn => {
        btn.addEventListener("click", function() {
            document.getElementById("claim_serial_input").value = this.dataset.serial;
            switchTab("request");
        });
    });
</script>

@endsection
