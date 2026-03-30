<?php include __DIR__ . '/../shares/header.php'; ?>
<?php
$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($baseUrl === false) $baseUrl = '';
if ($baseUrl === '.' || $baseUrl === '\\') $baseUrl = '';
?>

<div class="container mt-5">
    <h1>Đặt hàng thành công</h1>
    <p>Mã đơn hàng của bạn: <strong><?php echo htmlspecialchars($order_number); ?></strong></p>
    <p>Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ để giao hàng sớm nhất.</p>
    <a href="<?php echo $baseUrl; ?>/Product" class="btn btn-primary">Về trang sản phẩm</a>
</div>

<?php include __DIR__ . '/../shares/footer.php'; ?>
