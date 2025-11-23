<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="bg-blue-600 text-white p-4 flex justify-between">
        <h1 class="text-xl font-bold">Admin Dashboard</h1>
        <a href="{{ route('admin.logout') }}" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">
            Đăng Xuất
        </a>
    </div>

    <div class="p-6">
        <h2 class="text-2xl font-bold mb-4">Xin chào Admin!</h2>

        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white p-4 shadow rounded">Box 1</div>
            <div class="bg-white p-4 shadow rounded">Box 2</div>
            <div class="bg-white p-4 shadow rounded">Box 3</div>
        </div>
    </div>

</body>
</html>