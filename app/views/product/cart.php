<?php include __DIR__ . '/../shares/header.php'; ?>
<?php
$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($baseUrl === false) $baseUrl = '';
if ($baseUrl === '.' || $baseUrl === '\\') $baseUrl = '';
?>

<div class="paddy-section-title">
    <h4 class="mb-0">Giỏ hàng của bạn</h4>
    <small>Kiểm tra sản phẩm trước khi thanh toán</small>
</div>

<?php if (empty($_SESSION['cart'])): ?>
    <div class="paddy-cart-wrap p-4">
        <p class="mb-2">Giỏ hàng trống.</p>
        <a href="<?php echo $baseUrl; ?>/Product" class="btn btn-secondary">Quay lại mua sắm</a>
    </div>
<?php else: ?>
    <form method="post" action="<?php echo $baseUrl; ?>/Product/updateCart">
        <div class="paddy-cart-wrap mb-4">
            <div class="cart-header">
                <strong>Sản phẩm trong giỏ</strong>
                <button type="submit" class="btn btn-sm btn-outline-secondary">Cập nhật giỏ hàng</button>
            </div>
            <div class="table-responsive p-3">
        <table class="table cart-table mb-0">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; foreach ($_SESSION['cart'] as $id => $item): ?>
                <tr>
                    <td style="vertical-align: middle;">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?php echo $baseUrl . '/' . htmlspecialchars($item['image']); ?>" alt="" class="cart-thumb mr-2">
                        <?php endif; ?>
                        <?php echo htmlspecialchars($item['name']); ?>
                    </td>
                    <td style="vertical-align: middle;"><?php echo htmlspecialchars(number_format((float)$item['price'], 0, ',', '.')); ?> VNĐ</td>
                    <td style="vertical-align: middle;">
                        <input type="number" name="quantities[<?php echo $id; ?>]" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="0" class="form-control" style="width:100px; display:inline-block;" />
                    </td>
                    <td style="vertical-align: middle;">
                        <?php $line = $item['price'] * $item['quantity']; $total += $line; echo htmlspecialchars(number_format((float)$line, 0, ',', '.')); ?> VNĐ
                    </td>
                    <td style="vertical-align: middle;"><a href="<?php echo $baseUrl; ?>/Product/deleteFromCart/<?php echo $id; ?>" class="btn btn-sm btn-danger">Xóa</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Tổng: <?php echo htmlspecialchars(number_format((float)$total, 0, ',', '.')); ?> VNĐ</h3>
            <div>
                <a href="<?php echo $baseUrl; ?>/Product" class="btn btn-light">Tiếp tục mua sắm</a>
            </div>
        </div>
    </form>

    <div class="paddy-cart-wrap p-4">
    <h5 class="mb-3">Thông tin thanh toán</h5>
    <form method="post" action="<?php echo $baseUrl; ?>/Product/processCheckout">
        <div class="form-group">
            <label>Họ tên</label>
            <input required class="form-control" name="name" value="">
        </div>
        <div class="form-group">
            <label>Số điện thoại</label>
            <input required class="form-control" name="phone" value="">
        </div>
        <div class="form-group">
            <label>Địa chỉ</label>
            <textarea required class="form-control" name="address"></textarea>
        </div>
        <div class="form-group">
            <label>Phương thức thanh toán</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
                <label class="form-check-label" for="payment_cod">
                    Thanh toán khi nhận hàng (COD)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="payment_bank" value="bank_transfer">
                <label class="form-check-label" for="payment_bank">
                    Chuyển khoản ngân hàng
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-success paddy-btn-primary">Thanh toán</button>
    </form>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../shares/footer.php'; ?>
