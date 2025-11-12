<header class="border-bottom bg-white">
    <div class="container py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-4">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-shield-alt text-primary fs-4"></i>
                <div>
                    <h5 class="mb-0 fw-bold">Cửa Hàng Công Nghệ</h5>
                    <small class="text-muted">Cửa Hàng</small>
                </div>
            </div>
            <nav class="d-flex gap-4">
                <a href="/" class="text-dark text-decoration-none d-flex align-items-center gap-1">
                    <i class="fas fa-home"></i> Trang Chủ
                </a>
                <a href="#" class="text-dark text-decoration-none d-flex align-items-center gap-1">
                    <i class="fas fa-shield"></i> Bảo Hành
                </a>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="#" class="text-dark"><i class="fas fa-user"></i></a>
            <a href="/cart" class="position-relative text-dark">
                <i class="fas fa-shopping-cart"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ count(session('cart', [])) }}
                </span>
            </a>
            <div class="dropdown">
                <button class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-globe"></i> VN
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Tiếng Việt</a></li>
                    <li><a class="dropdown-item" href="#">English</a></li>
                </ul>
            </div>
            <button class="btn btn-outline-secondary btn-sm">Quản Trị</button>
            <button class="btn btn-magenta text-white btn-sm">Khách Hàng</button>
        </div>
    </div>
</header>

<style>
.btn-magenta {
    background: linear-gradient(135deg, #ff6bd6, #ff8c00);
}
</style>