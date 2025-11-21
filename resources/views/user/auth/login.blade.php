<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập & Đăng Ký</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(-45deg, #ce2a8d 0%, #4a74f4 100%);
        }
        .form-container {
            backdrop-filter: blur(10px) saturate(150%);
            -webkit-backdrop-filter: blur(10px) saturate(150%);
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.125);
        }
        /* Ẩn tab content mặc định */
        .tab-content { display: none; }
        /* Hiển thị tab active */
        .tab-content.active { display: block; }

        /* Style cho tab button */
        .tab-button {
            background-color: transparent;
            color: rgba(255, 255, 255, 0.6);
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 1.125rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }
        .tab-button.active {
            color: white;
            border-bottom-color: white;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md mx-auto">

        <div class="form-container text-white shadow-2xl p-6 sm:p-10">
            <div class="flex justify-center mb-4">
                <div class="w-20 h-20 rounded-full bg-white/30 flex items-center justify-center">
                    <i class="fa-solid fa-shield-cat text-4xl text-white"></i>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-center mb-2">Khách Hàng</h1>
            <p class="text-center text-white/80 mb-6">Chào mừng bạn đến với cửa hàng</p>

            <div class="flex justify-center mb-6">
                <button class="tab-button active" onclick="showTab('login')">Đăng Nhập</button>
                <button class="tab-button" onclick="showTab('register')">Đăng Ký</button>
            </div>

            @if(session('success'))
                <div class="bg-green-500 text-white p-3 rounded mb-4 text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any() && !session('success'))
                <div class="bg-red-500 text-white p-3 rounded mb-4 text-center">
                    <p>Có lỗi xảy ra:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div id="login" class="tab-content active">
                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="email_login" class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" id="email_login" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 bg-white/20 border border-transparent rounded-lg focus:ring-2 focus:ring-white/50 focus:outline-none text-white placeholder-white/70">
                    </div>

                    <div>
                        <label for="password_login" class="block text-sm font-medium mb-1">Mật khẩu</label>
                        <input type="password" id="password_login" name="password" required
                               class="w-full px-4 py-3 bg-white/20 border border-transparent rounded-lg focus:ring-2 focus:ring-white/50 focus:outline-none text-white">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded bg-white/30 border-transparent focus:ring-white/50">
                            <span class="ml-2 text-sm">Ghi nhớ tôi</span>
                        </label>
                        <a href="#" class="text-sm font-medium hover:underline">Quên mật khẩu?</a>
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-4 rounded-lg text-lg font-semibold text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-all duration-300">
                        Đăng Nhập
                    </button>
                </form>
            </div>

            <div id="register" class="tab-content">
                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1">Họ tên</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 bg-white/20 border border-transparent rounded-lg focus:ring-2 focus:ring-white/50 focus:outline-none text-white placeholder-white/70">
                    </div>

                    <div>
                        <label for="email_reg" class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" id="email_reg" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 bg-white/20 border border-transparent rounded-lg focus:ring-2 focus:ring-white/50 focus:outline-none text-white placeholder-white/70">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium mb-1">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                               class="w-full px-4 py-3 bg-white/20 border border-transparent rounded-lg focus:ring-2 focus:ring-white/50 focus:outline-none text-white placeholder-white/70">
                    </div>

                    <div>
                        <label for="password_reg" class="block text-sm font-medium mb-1">Mật khẩu</label>
                        <input type="password" id="password_reg" name="password" required
                               class="w-full px-4 py-3 bg-white/20 border border-transparent rounded-lg focus:ring-2 focus:ring-white/50 focus:outline-none text-white">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-1">Xác nhận mật khẩu</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-4 py-3 bg-white/20 border border-transparent rounded-lg focus:ring-2 focus:ring-white/50 focus:outline-none text-white">
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-4 rounded-lg text-lg font-semibold text-white bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300">
                        Tạo tài khoản
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const loginTab = document.getElementById('login');
        const registerTab = document.getElementById('register');
        const tabs = document.querySelectorAll('.tab-button');

        function showTab(tabName) {
            tabs.forEach(tab => tab.classList.remove('active'));

            if (tabName === 'login') {
                loginTab.classList.add('active');
                registerTab.classList.remove('active');
                document.querySelector('.tab-button[onclick="showTab(\'login\')"]').classList.add('active');
            } else {
                loginTab.classList.remove('active');
                registerTab.classList.add('active');
                document.querySelector('.tab-button[onclick="showTab(\'register\')"]').classList.add('active');
            }
        }

        // Kiểm tra xem có cần active tab register không (khi
        // submit lỗi từ form register)   
        @if(session('tab') == 'register' || $errors->has('name') || $errors->has('phone') || $errors->has('password_confirmation'))
            showTab('register');
        @else
            showTab('login');
        @endif        
    </script>
</body>
</html>
