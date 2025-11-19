<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-cyan-500 via-blue-600 to-purple-700 flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white/20 backdrop-blur-xl p-10 rounded-3xl shadow-2xl border border-white/30">

        <!-- Logo + Title -->
        <div class="text-center mb-8">
            <div class="mx-auto w-24 h-24 rounded-full bg-white/30 backdrop-blur flex items-center justify-center shadow-lg">
                <i class="fas fa-shield-alt text-white text-4xl"></i>
            </div>

            <h1 class="text-3xl font-extrabold text-white mt-5 tracking-wide drop-shadow-lg">
                Admin
            </h1>

            <p class="text-white/80 mt-1 text-sm">
                Hệ thống quản trị cửa hàng công nghệ
            </p>
        </div>

        <!-- Error -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                ⚠ {{ $errors->first() }}
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="text-white font-medium">Email</label>
                <input 
                    type="text" 
                    name="email"
                    class="w-full mt-1 p-3 rounded-xl bg-white/90 border border-gray-300 shadow-sm 
                           focus:ring-2 focus:ring-cyan-400 outline-none"
                    placeholder="admin@example.com">
            </div>

            <div>
                <label class="text-white font-medium">Mật khẩu</label>
                <input 
                    type="password" 
                    name="password"
                    class="w-full mt-1 p-3 rounded-xl bg-white/90 border border-gray-300 shadow-sm 
                           focus:ring-2 focus:ring-cyan-400 outline-none"
                    placeholder="••••••••">
            </div>

            <div class="flex justify-between text-sm text-white/90">
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="w-4 h-4">
                    Ghi nhớ tôi
                </label>
                <a href="{{ route('admin.forgot') }}" class="hover:text-white hover:underline">Quên mật khẩu?</a>

            </div>

            <button 
                class="w-full py-3 mt-3 bg-gradient-to-r from-cyan-500 via-blue-600 to-purple-600 
                       text-white font-bold rounded-xl shadow-lg hover:opacity-90 transition">
                Đăng Nhập
            </button>
        </form>

       
        
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
