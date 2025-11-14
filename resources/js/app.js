import './bootstrap';

/**
 * Hàm này chịu trách nhiệm gọi API và hiển thị sản phẩm ra màn hình
 * @param {string} searchTerm - Từ khóa tìm kiếm (mặc định là chuỗi rỗng)
 */
function loadProducts(searchTerm = '') {
    // Lấy container sản phẩm
    const productContainer = document.querySelector('.home-product .grid__row');

    if (!productContainer) {
        console.error('Không tìm thấy container .home-product .grid__row');
        return;
    }

    // ✅ FIX 1: Sửa URL API cho khớp với backend (bỏ /user)
    // Backend: router.get("/") → /api/products
    let apiUrl = 'http://localhost:3000/api/v1/products';

    // ✅ FIX 2: Backend dùng query param là "search", không phải "keyword"
    const params = new URLSearchParams();
    if (searchTerm) {
        params.append('search', searchTerm);
    }
    // Thêm page và pageSize mặc định
    params.append('page', '1');
    params.append('pageSize', '100'); // Lấy nhiều sản phẩm hơn

    if (params.toString()) {
        apiUrl += `?${params.toString()}`;
    }

    console.log('🔗 API URL:', apiUrl);

    // Hiển thị "Đang tải..."
    productContainer.innerHTML = '<p style="padding: 20px; text-align: center;">Đang tải sản phẩm...</p>';

    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(responseData => {
            console.log('📦 Response:', responseData);

            // ✅ FIX 3: Backend trả về responseSuccess({ data: { products, total, ... } })
            // Nên cấu trúc là: responseData.data.products
            const productsData = responseData.data;

            if (!productsData || !productsData.products) {
                productContainer.innerHTML = '<p style="padding: 20px; text-align: center;">Không có dữ liệu sản phẩm</p>';
                return;
            }

            const products = productsData.products;
            console.log(`✅ Loaded ${products.length} products`);

            // Xóa nội dung "Đang tải..."
            productContainer.innerHTML = '';

            if (products.length === 0) {
                if(searchTerm) {
                    productContainer.innerHTML = `<p style="padding: 20px; text-align: center;">Không tìm thấy sản phẩm nào với từ khóa "${searchTerm}".</p>`;
                } else {
                    productContainer.innerHTML = '<p style="padding: 20px; text-align: center;">Chưa có sản phẩm nào.</p>';
                }
                return;
            }

            // Lặp và hiển thị sản phẩm
            products.forEach(product => {
                let imageUrl = 'https://via.placeholder.com/300?text=No+Image';

                if (product.image) {
                    imageUrl = product.image;
                } else if (product.images && product.images.length > 0) {
                    const firstImage = product.images[0];
                    imageUrl = (typeof firstImage === 'object' && firstImage.url) ? firstImage.url : firstImage;
                }

                const productHtml = `
                    <div class="grid__column-4">
                        <div class="home-product-item">
                            <div class="home-product-item__img" style="background-image: url(${imageUrl});"></div>
                            <div class="home-product-item__body">
                                <div class="home-product-name__wrap">
                                    <h4 class="home-product-item__name">${product.name || 'N/A'}</h4>
                                    <span class="home-product-item__tag">${product.categoryName || 'New'}</span>
                                </div>
                                <div class="home-product-item__rating">
                                    <i class="fa-solid fa-star"></i>
                                    <span>(${product.rating || '0'})</span>
                                </div>
                                <p class="home-product-item__category">${product.categoryId || 'N/A'}</p>
                                <div class="home-product-item__footer">
                                    <span class="home-product-item__price">$${product.price || '0'}</span>
                                    <span class="home-product-item__stock">${product.stock > 0 ? 'Còn Hàng' : 'Hết Hàng'}</span>
                                </div>
                                <button class="home-product-item__button btn_css btn--primary_css">
                                    <i class="home-product-item__cart fa-solid fa-cart-shopping"></i>
                                    Thêm vào Giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                productContainer.insertAdjacentHTML('beforeend', productHtml);
            });
        })
        .catch(error => {
            console.error('❌ Lỗi khi tải sản phẩm:', error);
            productContainer.innerHTML = '<p style="padding: 20px; text-align: center;">Không thể tải sản phẩm. Vui lòng kiểm tra backend.</p>';
        });
}

// ==========================================================
// SỰ KIỆN CHÍNH
// ==========================================================
document.addEventListener('DOMContentLoaded', function() {

    // 1. Tải tất cả sản phẩm ngay khi trang được mở
    loadProducts();

    // 2. Lắng nghe sự kiện gõ phím trên ô tìm kiếm
    const searchInput = document.querySelector('.header__search-input');

    if (searchInput) {
        // ✅ FIX 4: Thêm debounce để tránh gọi API quá nhiều lần
        let debounceTimer;

        searchInput.addEventListener('keyup', function(event) {
            // Xóa timer cũ
            clearTimeout(debounceTimer);

            // Đợi 500ms sau khi người dùng ngừng gõ mới gọi API
            debounceTimer = setTimeout(() => {
                const searchTerm = event.target.value.trim();
                console.log('🔍 Searching for:', searchTerm);
                loadProducts(searchTerm);
            }, 500);
        });
    } else {
        console.warn('⚠️ Không tìm thấy input search .header__search-input');
    }
});

// ✅ FIX 5: Export hàm loadProducts để có thể gọi từ inline script trong blade
window.loadProducts = loadProducts;
