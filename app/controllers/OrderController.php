<?php
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../config/database.php';

class OrderController
{
    private $db;
    private $orderModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // User must be logged in to view their orders
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . $this->basePath() . '/Product');
            exit;
        }

        $this->db = (new Database())->getConnection();
        $this->orderModel = new OrderModel($this->db);
    }

    private function basePath()
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($base === false || $base === '.' || $base === '\\') {
            return '';
        }
        return $base;
    }

    public function index()
    {
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            $orders = $this->orderModel->getAllOrders();
        } else {
            $user_id = $_SESSION['user_id'];
            $orders = $this->orderModel->getOrdersByUserId($user_id);
        }
        
        // Cần truyền biến cho header (số lượng giỏ hàng nếu có, v.v...)
        $cartCount = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $cartCount += $item['quantity'];
            }
        }
        
        include_once __DIR__ . '/../views/order/list.php';
    }

    public function show($id)
    {
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            $order = $this->orderModel->getOrderById($id);
        } else {
            $user_id = $_SESSION['user_id'];
            $order = $this->orderModel->getOrderById($id, $user_id);
        }
        
        if (!$order) {
            echo "Không tìm thấy đơn hàng, hoặc bạn không có quyền xem.";
            return;
        }

        $details = $this->orderModel->getOrderDetails($id);

        $cartCount = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $cartCount += $item['quantity'];
            }
        }

        include_once __DIR__ . '/../views/order/show.php';
    }

    public function cancel($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                $this->orderModel->cancelOrder($id, null); // admin cancels any
            } else {
                $user_id = $_SESSION['user_id'];
                $this->orderModel->cancelOrder($id, $user_id);
            }
        }
        header('Location: ' . $this->basePath() . '/Order/show/' . $id);
        exit;
    }

    public function updateStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            if (isset($_POST['status'])) {
                $this->orderModel->updateOrderStatus($id, $_POST['status']);
            }
        }
        header('Location: ' . $this->basePath() . '/Order/show/' . $id);
        exit;
    }
}
?>
