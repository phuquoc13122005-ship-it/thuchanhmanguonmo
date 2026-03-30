<?php include 'app/views/shares/header.php'; ?>
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="mb-0">Quản lý Danh mục</h2>
    </div>
    <div class="col text-right">
        <a href="<?php echo $baseUrl; ?>/Category/add" class="btn btn-success btn-sm paddy-btn-primary">Thêm danh mục mới</a>
    </div>
</div>

<div class="paddy-cart-wrap">
    <table class="table mb-0 cart-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th width="150" class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category->id; ?></td>
                        <td><strong><?php echo htmlspecialchars($category->name); ?></strong></td>
                        <td><?php echo htmlspecialchars($category->description); ?></td>
                        <td class="text-center">
                            <a href="<?php echo $baseUrl; ?>/Category/edit/<?php echo $category->id; ?>" class="btn btn-warning btn-sm text-white">Sửa</a>
                            <a href="<?php echo $baseUrl; ?>/Category/delete/<?php echo $category->id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center py-4">Chưa có danh mục nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include 'app/views/shares/footer.php'; ?>
