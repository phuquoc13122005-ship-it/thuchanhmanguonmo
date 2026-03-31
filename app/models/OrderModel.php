<?php
class OrderModel {
    private $conn;
    private $orders_table = 'orders';
    private $details_table = 'order_details';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create an order and return inserted id
    public function createOrder($name, $phone, $address, $payment_method, $user_id = null) {
        $query = "INSERT INTO `" . $this->orders_table . "` (user_id, name, phone, address, payment_method) VALUES (:user_id, :name, :phone, :address, :payment_method)";
        $stmt = $this->conn->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $phone = htmlspecialchars(strip_tags($phone));
        $address = htmlspecialchars(strip_tags($address));
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':payment_method', $payment_method);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Insert order detail rows
    public function addOrderDetail($order_id, $product_id, $quantity, $price) {
        $query = "INSERT INTO `" . $this->details_table . "` (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);
        return $stmt->execute();
    }

    // Fetch orders for a specific user
    public function getOrdersByUserId($user_id) {
        $query = "SELECT * FROM `" . $this->orders_table . "` WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Fetch ALL orders (for admin)
    public function getAllOrders() {
        $query = "SELECT * FROM `" . $this->orders_table . "` ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Fetch a single order by ID (verifying user_id if provided)
    public function getOrderById($order_id, $user_id = null) {
        $query = "SELECT * FROM `" . $this->orders_table . "` WHERE id = :id";
        if ($user_id) {
            $query .= " AND user_id = :user_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $order_id);
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Fetch order details with product info
    public function getOrderDetails($order_id) {
        $query = "SELECT d.*, p.name as product_name, p.image as product_image 
                  FROM `" . $this->details_table . "` d
                  LEFT JOIN product p ON d.product_id = p.id
                  WHERE d.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Cancel an order
    public function cancelOrder($order_id, $user_id = null) {
        // Only allow cancelling if it's pending
        $query = "UPDATE `" . $this->orders_table . "` SET status = 'cancelled' WHERE id = :id AND status = 'pending'";
        if ($user_id) {
            $query .= " AND user_id = :user_id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $order_id);
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        return $stmt->execute();
    }

    // Admin updates order status
    public function updateOrderStatus($order_id, $status) {
        $query = "UPDATE `" . $this->orders_table . "` SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $order_id);
        return $stmt->execute();
    }
}
?>