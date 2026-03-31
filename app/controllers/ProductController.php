<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
class ProductController
{
    private $productModel;
    private $db;
    
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    private function basePath()
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($base === false || $base === '.' || $base === '\\') {
            return '';
        }
        return $base;
    }

    private function checkAdmin() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            die("Access Denied. Admins only.");
        }
    }
    
    public function index()
    {
        $products = $this->productModel->getProducts();
        $pageTitle = 'Tất cả sản phẩm';
        include __DIR__ . '/../views/product/list.php';
    }

    public function category($id)
    {
        $products = $this->productModel->getProductsByCategory($id);
        require_once __DIR__ . '/../models/CategoryModel.php';
        $current_category = (new CategoryModel($this->db))->getCategoryById($id);
        $pageTitle = $current_category ? $current_category->name : 'Sản phẩm danh mục';
        include __DIR__ . '/../views/product/list.php';
    }

    public function search()
    {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $products = $this->productModel->searchProducts($keyword);
        $pageTitle = $keyword ? 'Kết quả tìm kiếm cho: ' . $keyword : 'Tất cả sản phẩm';
        include __DIR__ . '/../views/product/list.php';
    }

    public function show($id)
    {
        $product = $this->productModel->getProductById($id);
        if ($product) {
        include __DIR__ . '/../views/product/show.php';
        } else {
        echo "Không thấy sản phẩm.";
        }
    }

    public function add()
    {
        $this->checkAdmin();
        $categories = (new CategoryModel($this->db))->getCategories();
        include_once __DIR__ . '/../views/product/add.php';
    }

    public function save()
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = $this->uploadImage($_FILES['image']);
            } else {
            $image = "";
            }

            $result = $this->productModel->addProduct($name, $description, $price,
            $category_id, $image);

            if (is_array($result)) {
                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                include __DIR__ . '/../views/product/add.php';
            } else {
                header('Location: ' . $this->basePath() . '/Product');
            }
        }
    }

    public function edit($id)
    {
        $this->checkAdmin();
        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();
        if ($product) {
            include __DIR__ . '/../views/product/edit.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    public function update()
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = $this->uploadImage($_FILES['image']);
            } else {
                $image = $_POST['existing_image'];
            }
            $edit = $this->productModel->updateProduct($id, $name, $description,
            $price, $category_id, $image);
            if ($edit) {
                header('Location: ' . $this->basePath() . '/Product');
            } else {
                echo "Đã xảy ra lỗi khi lưu sản phẩm.";
            }
        }
    }

    public function delete($id)
    {
        $this->checkAdmin();
        if ($this->productModel->deleteProduct($id)) {
            header('Location: ' . $this->basePath() . '/Product');
        } else {
            echo "Đã xảy ra lỗi khi xóa sản phẩm.";
        }
    }

    private function uploadImage($file)
    {
        $target_dir = "uploads/";
 
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        // Generate a unique filename to prevent cache issues and overwriting
        $unique_name = uniqid() . '_' . basename($file["name"]);
        $target_file = $target_dir . $unique_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }

        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn.");
        }
 
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType !=
        "jpeg" && $imageFileType != "gif") {
            throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG và GIF.");
        }

        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }
        return $target_file;
    }

    public function addToCart($id)
    {
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            return;
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        // Support quantity via POST (preferred) or GET param 'qty'
        $qty = 1;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        } else {
            $qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;
        }
        if ($qty < 1) $qty = 1;
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $qty,
            'image' => $product->image
            ];
        }
        header('Location: ' . $this->basePath() . '/Product/cart');
    }

    // Remove a single product from cart
    public function deleteFromCart($id)
    {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        header('Location: ' . $this->basePath() . '/Product/cart');
    }

    // Update quantities for multiple products
    public function updateCart()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quantities = $_POST['quantities'] ?? [];
            foreach ($quantities as $pid => $qty) {
                $pid = intval($pid);
                $qty = intval($qty);
                if ($qty <= 0) {
                    if (isset($_SESSION['cart'][$pid])) unset($_SESSION['cart'][$pid]);
                } else {
                    if (isset($_SESSION['cart'][$pid])) {
                        $_SESSION['cart'][$pid]['quantity'] = $qty;
                    }
                }
            }
        }
        header('Location: ' . $this->basePath() . '/Product/cart');
    }

    public function cart()
    {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            echo "Giỏ hàng trống.";
            return;
        }
        $cart = $_SESSION['cart'];
        include __DIR__ . '/../views/product/cart.php';
    }

    public function checkout()
    {
        include 'app/views/product/checkout.php';
    }

    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $address = $_POST['address'];
            $payment_method = isset($_POST['payment_method']) && $_POST['payment_method'] === 'bank_transfer'
                ? 'bank_transfer'
                : 'cod';
            $cart = $_SESSION['cart'] ?? [];
            if (empty($cart)) {
                echo "Giỏ hàng trống.";
                return;
            }
            // Lưu đơn hàng vào cơ sở dữ liệu (bảng orders và order_details)
            require_once __DIR__ . '/../models/OrderModel.php';
            $orderModel = new OrderModel($this->db);
            // phone is optional in form; try to get if provided
            $phone = $_POST['phone'] ?? '';
            $user_id = $_SESSION['user_id'] ?? null;
            $orderId = $orderModel->createOrder($name, $phone, $address, $payment_method, $user_id);
            if (!$orderId) {
                echo "Lỗi khi lưu đơn hàng.";
                return;
            }
            // insert order details
            foreach ($cart as $product_id => $item) {
                $quantity = $item['quantity'];
                $price = $item['price'];
                $orderModel->addOrderDetail($orderId, $product_id, $quantity, $price);
            }
            // Clear cart
            unset($_SESSION['cart']);
            // show confirmation page
            $order_number = $orderId;
            include __DIR__ . '/../views/product/orderConfirmation.php';
        }
    }

    public function orderConfirmation()
    {
        include 'app/views/product/order_confirmation.php';
    }
}
?>