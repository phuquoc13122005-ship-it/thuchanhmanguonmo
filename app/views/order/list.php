<?php include __DIR__ . '/../shares/header.php'; ?>
<div class="row">
    <div class="col-12 mt-4">
        <h4 class="mb-4">Danh sách Đơn hàng của bạn</h4>
        <?php if (empty($orders)): ?>
            <p>Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm <a href="<?php echo $baseUrl; ?>/Product">tại đây</a>.</p>
        <?php else: ?>
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <th>Khách Hàng</th>
                        <?php endif; ?>
                        <th>Ngày Đặt</th>
                        <th>Trạng Thái</th>
                        <th class="text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="align-middle font-weight-bold">#<?php echo htmlspecialchars($order->id); ?></td>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <td class="align-middle text-primary font-weight-bold">
                            <?php echo htmlspecialchars($order->name); ?>
                            <br><small class="text-muted"><?php echo htmlspecialchars($order->phone); ?></small>
                        </td>
                        <?php endif; ?>
                        <td class="align-middle"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($order->created_at))); ?></td>
                        <td class="align-middle">
                            <?php 
                                if ($order->status == 'pending') echo '<span class="badge badge-warning" style="font-size:13px; padding:6px 10px;">Đang xử lý</span>';
                                elseif ($order->status == 'cancelled') echo '<span class="badge badge-danger" style="font-size:13px; padding:6px 10px;">Đã hủy</span>';
                                else echo '<span class="badge badge-success">' . htmlspecialchars($order->status) . '</span>';
                            ?>
                        </td>
                        <td class="text-center align-middle">
                            <a href="<?php echo $baseUrl; ?>/Order/show/<?php echo $order->id; ?>" class="btn btn-sm btn-info text-white" style="border-radius:20px; padding: 5px 15px;">Chi tiết</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/../shares/footer.php'; ?>
