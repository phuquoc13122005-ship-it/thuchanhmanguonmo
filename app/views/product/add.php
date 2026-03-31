<?php include 'app/views/shares/header.php'; ?>

<h1>Thêm sản phẩm mới</h1>
<form id="add-product-form">
    <div class="form-group">
        <label for="name">Tên sản phẩm:</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="description">Mô tả:</label>
        <textarea id="description" name="description" class="form-control" required></textarea>
    </div>
    <div class="form-group">
        <label for="price">Giá:</label>
        <input type="number" id="price" name="price" class="form-control" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="category_id">Danh mục:</label>
        <select id="category_id" name="category_id" class="form-control" required>
            <!-- Các danh mục sẽ được tải từ API và hiển thị tại đây -->
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
</form>

<a href="<?php echo $baseUrl; ?>/Product/list" class="btn btn-secondary mt-2">Quay lại danh sách sản phẩm</a>

<?php include 'app/views/shares/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('<?php echo $baseUrl; ?>/api/category')
        .then(response => response.json())
        .then(data => {
            const categorySelect = document.getElementById('category_id');
            data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        });

    document.getElementById('add-product-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        // Gửi dữ liệu API POST logic (phần cuối slide 4)
        const token = localStorage.getItem('jwtToken');
        fetch('<?php echo $baseUrl; ?>/api/product', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.json().then(err => { throw err; });
            }
        })
        .then(data => {
            alert('Thêm sản phẩm thành công!');
            window.location.href = '<?php echo $baseUrl; ?>/Product/list'; // Chuyển về trang danh sách
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi thêm sản phẩm. Vui lòng thử lại!');
        });
    });
});
</script>