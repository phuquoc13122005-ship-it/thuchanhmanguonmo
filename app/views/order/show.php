<?php include __DIR__ . '/../shares/header.php'; ?>
<div class="row">
    <div class="col-12 mt-4">
        <h4 class="mb-4">Chi tiết Đơn hàng #<?php echo htmlspecialchars($order->id); ?></h4>
        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body">
                <h6 class="font-weight-bold mb-3">Thông tin giao hàng</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Người nhận:</strong> <?php echo htmlspecialchars($order->name); ?></p>
                        <p class="mb-2"><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order->phone); ?></p>
                        <p class="mb-2"><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order->address); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Phương thức TT:</strong> <?php echo htmlspecialchars($order->payment_method == 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng'); ?></p>
                        <p class="mb-2"><strong>Ngày đặt:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($order->created_at))); ?></p>
                        <p class="mb-2"><strong>Trạng thái:</strong> 
                            <?php 
                                if ($order->status == 'pending') echo '<span class="badge badge-warning" style="font-size:13px;">Đang xử lý</span>';
                                elseif ($order->status == 'cancelled') echo '<span class="badge badge-danger" style="font-size:13px;">Đã hủy</span>';
                                else echo '<span class="badge badge-success">' . htmlspecialchars($order->status) . '</span>';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="mb-3 font-weight-bold">Sản phẩm đã đặt</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-right">Đơn giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($details as $item): 
                        $sub_total = $item->price * $item->quantity;
                        $total += $sub_total;
                    ?>
                    <tr>
                        <td class="align-middle">
                            <?php if(!empty($item->product_image)): ?>
                                <img src="<?php echo $baseUrl . '/' . htmlspecialchars($item->product_image); ?>" width="50" height="50" style="object-fit: contain; border-radius: 8px;" class="mr-2" alt="">
                            <?php endif; ?>
                            <?php echo htmlspecialchars($item->product_name); ?>
                        </td>
                        <td class="align-middle text-right"><?php echo number_format($item->price, 0, ',', '.'); ?>đ</td>
                        <td class="align-middle text-center"><?php echo $item->quantity; ?></td>
                        <td class="align-middle text-right font-weight-bold"><?php echo number_format($sub_total, 0, ',', '.'); ?>đ</td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-light">
                        <td colspan="3" class="text-right font-weight-bold" style="font-size: 16px;">TỔNG CỘNG:</td>
                        <td class="text-right font-weight-bold text-danger" style="font-size: 18px;"><?php echo number_format($total, 0, ',', '.'); ?>đ</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 mb-5 d-flex justify-content-between align-items-center">
            <a href="<?php echo $baseUrl; ?>/Order" class="btn btn-outline-dark" style="border-radius: 25px; padding: 8px 20px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="mr-1" stroke="currentColor" stroke-width="2"><path d="M19 12H5"></path><polyline points="12 19 5 12 12 5"></polyline></svg>
                Quay lại danh sách
            </a>
            <div>
                <?php if ($order->status == 'pending'): ?>
                <form action="<?php echo $baseUrl; ?>/Order/cancel/<?php echo $order->id; ?>" method="POST" class="d-inline ml-2" onsubmit="return confirm('Hành động này không thể hoàn tác. Bạn thực sự muốn hủy đơn hàng này?');">
                    <button type="submit" class="btn btn-danger" style="border-radius: 25px; padding: 8px 15px; font-weight: bold;">Hủy Đơn Hàng</button>
                </form>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <?php if ($order->status == 'pending'): ?>
                        <form action="<?php echo $baseUrl; ?>/Order/updateStatus/<?php echo $order->id; ?>" method="POST" class="d-inline ml-2">
                            <input type="hidden" name="status" value="shipped">
                            <button type="submit" class="btn btn-info text-white" style="border-radius: 25px; padding: 8px 15px;">Đã Gửi Hàng</button>
                        </form>
                    <?php elseif ($order->status == 'shipped'): ?>
                        <form action="<?php echo $baseUrl; ?>/Order/updateStatus/<?php echo $order->id; ?>" method="POST" class="d-inline ml-2">
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success" style="border-radius: 25px; padding: 8px 15px;">Đã Giao Xong</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../shares/footer.php'; ?>
