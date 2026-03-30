<?php
$baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($baseUrl === false) $baseUrl = '';
if ($baseUrl === '.' || $baseUrl === '\\') $baseUrl = '';

$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $qty = isset($item['quantity']) ? (int)$item['quantity'] : 0;
        $cartCount += max(0, $qty);
    }
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/CategoryModel.php';
$headerDb = (new Database())->getConnection();
$headerCategories = (new CategoryModel($headerDb))->getCategories();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TerPiQue</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/public/styles/paddy.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white paddy-header">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>/Product"><span style="color:#14a44d;">Khabib</span> Shop</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <form class="form-inline ml-lg-4 paddy-search w-100 w-lg-auto" action="<?php echo $baseUrl; ?>/Product/search" method="GET">
                    <div class="input-group">
                        <input class="form-control" type="search" name="q" placeholder="Tìm kiếm phổ biến: Royal Canin, đồ chơi..." aria-label="Search" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <div class="input-group-append">
                            <button class="btn btn-success" type="submit">Tìm</button>
                        </div>
                    </div>
                </form>
                <ul class="navbar-nav ml-auto mt-3 mt-lg-0 align-items-center">
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/Product">Sản phẩm</a></li>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/Category">Danh mục</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/Product/cart">Giỏ hàng<?php if ($cartCount > 0): ?> <span class="paddy-badge ml-1"><?php echo $cartCount; ?></span><?php endif; ?></a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/Order">Đơn hàng</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/Product/add">Đăng bán</a></li>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item ml-lg-3">
                        <a class="nav-link user-action" href="<?php echo $baseUrl; ?>/User/logout" onclick="localStorage.removeItem('jwtToken')">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Đăng Xuất</span>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item ml-lg-3">
                        <a class="nav-link user-action" href="javascript:void(0)" onclick="toggleLoginPanel()">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Đăng Nhập</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-3 mb-3 paddy-cat-row">
        <div class="row no-gutters justify-content-center">
            <?php foreach ($headerCategories as $hCat): ?>
            <div class="col-6 col-md-2 cat-item text-center">
                <a href="<?php echo $baseUrl; ?>/Product/category/<?php echo $hCat->id; ?>">
                    <?php echo htmlspecialchars($hCat->name); ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</header>
<main class="site-main flex-grow-1">
    <div class="container">

<!-- Auth Slide Panel -->
<div class="auth-panel-overlay" id="authOverlay" onclick="toggleLoginPanel()"></div>
<div class="auth-panel" id="authPanel">
    <div class="auth-panel-header">
        <h5 class="mb-0 font-weight-bold">Đăng Nhập</h5>
        <button class="close-btn" onclick="toggleLoginPanel()">&times;</button>
    </div>
    <div class="auth-panel-body">
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-danger" style="font-size: 13px; padding: 10px;">
                <?php echo htmlspecialchars($_SESSION['login_error']); unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['register_success'])): ?>
            <div class="alert alert-success" style="font-size: 13px; padding: 10px;">
                <?php echo htmlspecialchars($_SESSION['register_success']); unset($_SESSION['register_success']); ?>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo $baseUrl; ?>/User/login" method="POST" id="loginForm">
            <div class="form-group mb-3">
                <label>Tài khoản / Email <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="email" placeholder="Tên tài khoản hoặc Email" required>
            </div>
            <div class="form-group mb-4">
                <label>Mật Khẩu <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
            </div>
            <button type="submit" class="btn btn-block auth-btn">Đăng Nhập</button>
            <div class="text-center mt-3 mb-4">
                <a href="#" class="text-dark font-weight-bold" style="text-decoration: underline; font-size: 14px;">Quên mật khẩu?</a>
            </div>
            <div class="text-center mt-2">
                <button type="button" class="btn btn-block auth-btn-solid" onclick="toggleRegisterForm()">Tạo Tài Khoản</button>
            </div>
        </form>

        <form action="<?php echo $baseUrl; ?>/User/register" method="POST" id="registerForm" style="display: none;">
            <p class="font-weight-bold mb-3" style="font-size: 18px;">Đăng ký mới</p>
            <div class="form-group mb-3">
                <label>Họ Tên</label>
                <input type="text" class="form-control" name="name" placeholder="Họ tên của bạn">
            </div>
            <div class="form-group mb-3">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="form-group mb-4">
                <label>Mật Khẩu <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="password" placeholder="Mật khẩu" required>
            </div>
            <button type="submit" class="btn btn-block auth-btn-solid mb-3">Đăng Ký</button>
            <div class="text-center">
                <a href="javascript:void(0)" onclick="toggleRegisterForm()" class="text-dark" style="text-decoration: underline; font-size: 14px;">Quay lại Đăng nhập</a>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleLoginPanel() {
        document.getElementById('authPanel').classList.toggle('active');
        document.getElementById('authOverlay').classList.toggle('active');
    }
    
    function toggleRegisterForm() {
        const form = document.getElementById('registerForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
        document.getElementById('loginForm').style.display = form.style.display === 'block' ? 'none' : 'block';
    }

    // Auto open if error exists
    <?php if (isset($_SESSION['login_error']) || isset($_SESSION['register_success'])): ?>
    document.addEventListener("DOMContentLoaded", function() {
        toggleLoginPanel();
    });
    <?php endif; ?>

    // JWT Intercept Logic from Lesson 6
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Stop normal submission temporarily
            
            const formData = new FormData(this);
            const jsonData = {};
            formData.forEach((value, key) => jsonData[key] = value);

            fetch('<?php echo $baseUrl; ?>/api/auth', { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(jsonData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.token) {
                    localStorage.setItem('jwtToken', data.token);
                }
            })
            .catch(err => console.error("JWT login failed", err))
            .finally(() => {
                // Submit form normally for $_SESSION
                HTMLFormElement.prototype.submit.call(loginForm);
            });
        });
    }
</script>