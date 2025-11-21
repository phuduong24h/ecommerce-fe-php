<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác minh OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-500 via-purple-600 to-pink-600 flex justify-center items-center p-4">

<div class="bg-white/20 backdrop-blur-xl w-full max-w-md p-8 rounded-3xl shadow-2xl border border-white/30">
    <h1 class="text-3xl font-bold text-white text-center mb-6">Xác minh OTP</h1>

    @if($errors->any())
        <div class="p-3 bg-red-100 text-red-700 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('success'))
        <div class="p-3 bg-green-100 text-green-700 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.verify.submit') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="text-white">Mã OTP</label>
            <input type="text" name="otp" required maxlength="6"
                   class="w-full mt-1 p-3 rounded-xl bg-white/90 border shadow"
                   placeholder="Nhập 6 số OTP">
        </div>

        <button class="w-full py-3 bg-purple-600 text-white font-bold rounded-xl shadow-lg hover:bg-purple-700">
            Xác minh
        </button>
    </form>
</div>

</body>
</html>