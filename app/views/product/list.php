<?php include 'app/views/shares/header.php'; ?>

<!-- Tiêu đề và nút thêm mới -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="font-weight-bold" style="color: #001f3f;">Hàng mới về</h2>
    <a href="<?php echo $baseUrl; ?>/Product/add" class="btn btn-success">+ Thêm sản phẩm mới</a>
</div>

<!-- Lưới sản phẩm dạng card -->
<div class="row" id="product-list">
    <!-- Danh sách sản phẩm sẽ được tải từ API và hiển thị tại đây theo dạng Grid -->
</div>

<?php include 'app/views/shares/footer.php'; ?>

<style>
/* Style tùy chỉnh cho UI card sản phẩm đẹp hơn */
.product-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 12px;
    border: 1px solid #eee;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
}
.product-img-wrapper {
    overflow: hidden;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    background: #f9f9f9;
}
.product-img {
    object-fit: contain; 
    aspect-ratio: 1/1; 
    width: 100%;
}
.product-brand {
    color: #1a56db; /* Màu xanh Catchy */
    font-size: 0.95rem;
    font-weight: 600;
}
.product-name {
    font-size: 1rem;
    font-weight: 500;
    color: #111;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 2.8rem;
}
.product-price {
    font-size: 1.15rem;
    color: #111;
}
.heart-icon {
    color: #666;
    cursor: pointer;
    transition: fill 0.2s;
}
.heart-icon:hover {
    color: #e02424;
    fill: #e02424;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem('jwtToken');
    if (!token) {
        if (typeof toggleLoginPanel === 'function') {
            toggleLoginPanel();
        } else {
            alert('Vui lòng đăng nhập để xem sản phẩm');
        }
        return;
    }

    fetch('<?php echo $baseUrl; ?>/api/product', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.message && data.message === 'Unauthorized') {
                alert('Phiên làm việc hết hạn, vui lòng đăng nhập lại.');
                localStorage.removeItem('jwtToken');
                if (typeof toggleLoginPanel === 'function') {
                    toggleLoginPanel();
                }
                return;
            }
            
            const productList = document.getElementById('product-list');
            if (!Array.isArray(data)) {
                return;
            }
            data.forEach(product => {
                // Formatting giá tiền về dạng 27.000đ
                const formatter = new Intl.NumberFormat('vi-VN');
                const priceFormatted = formatter.format(product.price) + 'đ';
                
                // Xử lý hình ảnh
                const imageUrl = product.image ? `<?php echo $baseUrl; ?>/${product.image}` : 'https://via.placeholder.com/300x300?text=No+Image';

                // Tạo thẻ bao bọc (col)
                const productWrapper = document.createElement('div');
                productWrapper.className = 'col-6 col-md-4 col-lg-3 mb-4';
                
                productWrapper.innerHTML = `
                    <div class="card h-100 shadow-sm product-card">
                        <a href="<?php echo $baseUrl; ?>/Product/show/${product.id}" class="product-img-wrapper">
                            <img src="${imageUrl}" class="card-img-top product-img" alt="${product.name}">
                        </a>
                        <div class="card-body d-flex flex-column p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="product-brand">${product.category_name || 'Khác'}</span>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="heart-icon"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                            </div>
                            <a href="<?php echo $baseUrl; ?>/Product/show/${product.id}" class="text-decoration-none mb-2">
                                <h5 class="card-title product-name" title="${product.name}">${product.name}</h5>
                            </a>
                            <div class="mt-auto mb-3">
                                <span class="font-weight-bold product-price">${priceFormatted}</span>
                            </div>
                            
                            <!-- Nhóm nút hành động cho Admin -->
                            <div class="d-flex justify-content-between border-top pt-3 w-100 mt-auto">
                                <a href="<?php echo $baseUrl; ?>/Product/edit/${product.id}" class="btn btn-sm btn-outline-warning" style="width: 48%;">Sửa</a>
                                <button class="btn btn-sm btn-outline-danger" style="width: 48%;" onclick="deleteProduct(${product.id})">Xóa</button>
                            </div>
                        </div>
                    </div>
                `;
                productList.appendChild(productWrapper);
            });
        });
});

function deleteProduct(id) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        const token = localStorage.getItem('jwtToken');
        fetch(`<?php echo $baseUrl; ?>/api/product/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'Product deleted successfully') {
                location.reload();
            } else {
                alert('Xóa sản phẩm thất bại');
            }
        });
    }
}
</script>