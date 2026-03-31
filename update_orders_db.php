<?php
require_once __DIR__ . '/app/config/database.php';
try {
    $db = (new Database())->getConnection();
    
    // Check if orders table exists, if not, create it.
    $db->exec("CREATE TABLE IF NOT EXISTS `orders` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `phone` varchar(50) NOT NULL,
        `address` text NOT NULL,
        `payment_method` varchar(50) NOT NULL,
        PRIMARY KEY (`id`)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS `order_details` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `order_id` int(11) NOT NULL,
        `product_id` int(11) NOT NULL,
        `quantity` int(11) NOT NULL,
        `price` decimal(10,2) NOT NULL,
        PRIMARY KEY (`id`)
    )");

    try { $db->exec("ALTER TABLE orders ADD COLUMN user_id INT NULL DEFAULT NULL AFTER id"); } catch(Exception $e) {}
    try { $db->exec("ALTER TABLE orders ADD COLUMN status VARCHAR(50) DEFAULT 'pending' AFTER payment_method"); } catch(Exception $e) {}
    try { $db->exec("ALTER TABLE orders ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"); } catch(Exception $e) {}

    echo "Orders DB updated.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
