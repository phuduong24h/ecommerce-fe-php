@extends('layouts.app')

@section('title', 'Bảo Hành Sản Phẩm')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">

  <!-- Header -->
  <div class="flex items-center gap-4 mb-8">
    <div class="p-3 rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50">
      <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M12 1.5l6 3v4.875A8.25 8.25 0 0112 20.25 8.25 8.25 0 016 9.375V4.5l6-3z"></path>
      </svg>
    </div>

    <div>
      <h1 class="text-2xl font-semibold text-gray-900">Bảo Hành Sản Phẩm</h1>
      <p class="text-gray-500">Kiểm tra tình trạng bảo hành và gửi yêu cầu bảo hành</p>
    </div>
  </div>

  <!-- TABS -->
  <div class="mb-6">
    <div class="flex items-center gap-4">
      <button id="tabCheck" class="flex-1 py-3 rounded-full text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-400 shadow-md">
        🔍 Kiểm Tra Bảo Hành
      </button>

      <button id="tabRequest" class="flex-1 py-3 rounded-full text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
        📄 Yêu Cầu Bảo Hành
      </button>
    </div>
  </div>

  <!-- CONTENT -->
  <div id="checkSection">

    <!-- Search Card -->
    <div class="rounded-xl border border-blue-100 p-6 mb-6 bg-white shadow-sm">
      <h3 class="text-lg font-semibold mb-3">Nhập Số Serial Sản Phẩm</h3>

      <form method="POST" action="{{ route('warranty.check') }}" class="flex gap-3 items-center">
        @csrf
        <input name="serial_number" placeholder="Ví dụ: SN-12345-ABCD"
               class="flex-1 px-4 py-3 rounded-lg bg-gray-100 border border-transparent focus:outline-none focus:ring-2 focus:ring-blue-200" />

        <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-400 to-teal-300 text-white font-semibold">
          <svg class="inline-block w-4 h-4 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18A7.5 7.5 0 1010.5 3a7.5 7.5 0 000 15z"/>
          </svg>
          Kiểm Tra
        </button>
      </form>

      <p class="text-sm text-gray-500 mt-3">Tìm số serial ở mặt sau hoặc đáy sản phẩm. Nếu sản phẩm chưa có serial trong hệ thống, bạn có thể gửi yêu cầu kèm ảnh hóa đơn.</p>
    </div>


    <!-- ⭐⭐⭐ KẾT QUẢ KIỂM TRA SERIAL – ĐÃ GHÉP HOÀN CHỈNH ⭐⭐⭐ -->
    @if(session('productInfo'))
        @php
            $serialClaims = session('serialClaims') ?? [];
            $claimCount = count($serialClaims);
            $info = session('productInfo');
        @endphp

        <div class="rounded-xl bg-green-50 border border-green-200 p-6 mb-6 shadow">

            <h3 class="text-xl font-semibold text-green-700 mb-4 flex items-center gap-2">
                <span>🔎</span> Kết quả kiểm tra
            </h3>

            <p><strong>Tên sản phẩm:</strong> {{ $info['name'] }}</p>
            <p><strong>Serial:</strong> {{ $info['serial'] }}</p>
            <p><strong>Order ID:</strong> {{ $info['orderId'] }}</p>
            <p><strong>Ngày mua:</strong> {{ $info['purchasedAt'] }}</p>

            <p class="mt-4 text-green-700 font-medium">
                📌 Serial này đã được bảo hành:
                <strong class="text-green-900">{{ $claimCount }}</strong> lần
            </p>

            <hr class="my-4">

            <h4 class="text-lg font-semibold mb-3 flex items-center gap-2">
                <span>📌</span> Lịch sử bảo hành:
            </h4>

            @foreach($serialClaims as $c)
                <div class="bg-white border rounded-xl p-4 mb-3 shadow-sm">
                    <p><strong>Trạng thái:</strong> {{ $c['status'] }}</p>
                    <p><strong>Lý do:</strong> {{ $c['issueDesc'] }}</p>
                    <p><strong>Ngày gửi:</strong> {{ date('d/m/Y', strtotime($c['createdAt'])) }}</p>
                </div>
            @endforeach

        </div>
    @endif
    <!-- ⭐⭐⭐ END PHẦN THÊM ⭐⭐⭐ -->


    <!-- My Warranty Claims -->
    <div class="mb-6">
      <h3 class="text-xl font-semibold mb-4">My Warranty Claims</h3>

      @if(!empty($claimList))
        <div class="space-y-4">
          @foreach($claimList as $c)
            <div class="rounded-lg border bg-white shadow-sm p-5 flex justify-between items-start">
              <div>
                <div class="flex items-center gap-3">
                  <div class="text-lg font-semibold">{{ $c['productName'] ?? 'Sản phẩm' }}</div>
                  @php
                    $status = strtolower($c['status'] ?? '');
                    $statusColor = 'bg-gray-200 text-gray-700';
                    if ($status === 'approved' || $status === 'repaired' || $status === 'completed') $statusColor = 'bg-green-100 text-green-800';
                    if ($status === 'pending') $statusColor = 'bg-yellow-100 text-yellow-800';
                    if ($status === 'in_repair' || $status === 'in-repair' || $status === 'in_progress' || $status === 'in-progress') $statusColor = 'bg-blue-100 text-blue-800';
                    if ($status === 'rejected') $statusColor = 'bg-red-100 text-red-800';
                  @endphp
                  <span class="px-2 py-0.5 text-xs rounded-full {{ $statusColor }}">{{ ucfirst(str_replace('_',' ', $c['status'] ?? '')) }}</span>
                </div>

                <div class="text-sm text-gray-500 mt-2">
                  <div><strong>Serial:</strong> {{ $c['productSerial'] ?? 'Không có' }}</div>
                  <div class="mt-1"><strong>Claim ID:</strong> {{ $c['id'] ?? ($c['claimId'] ?? '-') }}</div>
                  <p class="mt-2 text-gray-600">{!! nl2br(e($c['issueDesc'] ?? $c['description'] ?? '')) !!}</p>

                  @if(!empty($c['estimatedAt']))
                    <a href="#" class="text-sm text-blue-600 mt-2 inline-block">Estimated resolution: {{ date("Y-m-d", strtotime($c['estimatedAt'])) }}</a>
                  @endif
                </div>
              </div>

              <div class="text-right text-sm text-gray-400">
                <div>{{ !empty($c['createdAt']) ? date("Y-m-d", strtotime($c['createdAt'])) : '' }}</div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="rounded-lg border bg-white shadow-sm p-5 text-gray-500">Bạn chưa gửi yêu cầu bảo hành nào.</div>
      @endif
    </div>

    <!-- Purchased Products -->
    <div>
      <h3 class="text-xl font-semibold mb-4">Sản phẩm bạn đã mua</h3>

      @if(!empty($purchased))
        <div class="space-y-4">
          @foreach($purchased as $p)
            <div class="rounded-lg border bg-white shadow-sm p-5">
              <div class="flex justify-between items-start">
                <div>
                  <div class="text-lg font-semibold">{{ $p['productName'] }}</div>
                  <div class="text-sm text-gray-500 mt-1">Order ID: {{ $p['orderId'] }}</div>
                  <div class="text-sm text-gray-500">Ngày mua: {{ $p['purchasedAt'] }}</div>
                </div>

                <div class="text-sm text-gray-500">
                  SL: <span class="px-2 py-1 bg-gray-100 rounded-full">{{ $p['quantity'] }}</span>
                </div>
              </div>

              <div class="mt-4 flex items-center justify-between">
                <div>
                  <div class="text-sm font-medium text-gray-700">Serial:</div>

                  @if(!empty($p['serial']))
                    <button class="use-serial-btn mt-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-full border bg-gray-50 text-gray-700 text-sm"
                            data-serial="{{ $p['serial'] }}">
                      {{ $p['serial'] }}
                    </button>
                  @else
                    <div class="italic text-gray-400 mt-2">Không có serial</div>
                  @endif

                  @if(!empty($p['latestClaim']))
                    @php
                      $desc = $p['latestClaim']['description'] ?? $p['latestClaim']['issueDesc'] ?? '';
                    @endphp
                    <p class="mt-3 text-sm text-green-700"><strong>Bảo hành gần nhất:</strong> {{ $desc }}</p>
                  @endif
                </div>

                <div class="text-sm text-gray-400"></div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="rounded-lg border bg-white shadow-sm p-5 text-gray-500">Không có sản phẩm đã mua.</div>
      @endif
    </div>

  </div> <!-- end checkSection -->

  <!-- REQUEST TAB -->
  <div id="requestSection" class="hidden">

    <div class="rounded-xl border bg-white p-6 shadow-sm">
      <h3 class="text-lg font-semibold mb-4">Submit a Warranty Claim</h3>

      <form method="POST" action="{{ route('warranty.claim') }}" class="space-y-4">
        @csrf

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Product Serial Number</label>
          <input id="claim_serial_input" name="serial_number" placeholder="SN-12345-ABCD"
                 class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-transparent focus:outline-none focus:ring-2 focus:ring-pink-200" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Describe the Issue</label>
          <textarea name="description" rows="4" placeholder="Please describe the problem you're experiencing with your product..."
                    class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-transparent focus:outline-none focus:ring-2 focus:ring-pink-200"></textarea>
        </div>

        <div class="bg-yellow-50 border border-yellow-100 p-3 rounded-md text-sm text-yellow-800">
          Please ensure your product serial number is correct. Claims are typically processed within 3-5 business days.
        </div>

        <button type="submit" class="w-full py-3 rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white font-semibold">Submit Claim</button>
      </form>
    </div>

  </div> <!-- end requestSection -->

</div>

<script>
  const tabCheck = document.getElementById("tabCheck");
  const tabRequest = document.getElementById("tabRequest");
  const checkSection = document.getElementById("checkSection");
  const requestSection = document.getElementById("requestSection");

  function switchTab(tab) {
    if (tab === "check") {
      checkSection.classList.remove("hidden");
      requestSection.classList.add("hidden");
      tabCheck.classList.add('bg-gradient-to-r','from-blue-600','to-blue-400');
      tabCheck.classList.remove('bg-gray-100','text-gray-700');
      tabRequest.classList.remove('bg-gradient-to-r','from-pink-500','to-purple-500');
      tabRequest.classList.add('bg-gray-100','text-gray-700');
    } else {
      requestSection.classList.remove("hidden");
      checkSection.classList.add("hidden");
      tabRequest.classList.add('bg-gradient-to-r','from-pink-500','to-purple-500');
      tabRequest.classList.remove('bg-gray-100','text-gray-700');
      tabCheck.classList.remove('bg-gradient-to-r','from-blue-600','to-blue-400');
      tabCheck.classList.add('bg-gray-100','text-gray-700');
    }
  }

  tabCheck.onclick = () => switchTab("check");
  tabRequest.onclick = () => switchTab("request");

  document.addEventListener('click', function(e){
    if (e.target && e.target.matches('.use-serial-btn, .use-serial-btn *')) {
      const btn = e.target.closest('.use-serial-btn');
      const serial = btn.dataset.serial;
      document.getElementById('claim_serial_input').value = serial;
      switchTab('request');

      setTimeout(() => {
        document.getElementById('claim_serial_input').scrollIntoView({ behavior: 'smooth', block: 'center' });
      }, 200);
    }
  });
</script>

@endsection
