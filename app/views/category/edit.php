<?php include 'app/views/shares/header.php'; ?>
<h2 class="mb-4">Chỉnh sửa Danh mục</h2>
<div class="card shadow-sm border-0" style="border-radius: 12px; max-width: 600px;">
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errors as $error) echo "<li>" . htmlspecialchars($error) . "</li>"; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo $baseUrl; ?>/Category/update" method="POST">
            <input type="hidden" name="id" value="<?php echo $category->id; ?>">
            <div class="form-group mb-3">
                <label for="name" class="font-weight-bold">Tên danh mục</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category->name); ?>" required style="border-radius: 8px;">
            </div>
            <div class="form-group mb-4">
                <label for="description" class="font-weight-bold">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="4" style="border-radius: 8px;"><?php echo htmlspecialchars($category->description); ?></textarea>
            </div>
            <div class="d-flex justify-content-between">
                <a href="<?php echo $baseUrl; ?>/Category" class="btn btn-secondary" style="border-radius: 8px;">Trở lại</a>
                <button type="submit" class="btn btn-success paddy-btn-primary" style="border-radius: 8px;">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>
