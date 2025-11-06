<header class="header">
    <div class="logo">Cửa Hàng Công Nghệff</ of>
    <div class="actions">
        <select onchange="window.location.href=this.value">
            <option value="?lang=vi" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>VN</option>
            <option value="?lang=en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
        </select>
        <a href="{{ route('admin.dashboard') }}" class="btn">Quản Trị</a>
        <a href="{{ route('home') }}" class="btn">Khách Hàng</a>
    </div>
</header>