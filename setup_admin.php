<?php
require_once __DIR__ . '/app/config/database.php';
try {
    $db = (new Database())->getConnection();
    $db->exec("ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'user'");
    
    $hashed = password_hash('admin123', PASSWORD_DEFAULT);
    $db->exec("INSERT IGNORE INTO users (name, email, password, role) VALUES ('Admin', 'admin@paddy.vn', '{$hashed}', 'admin')");

    echo "Role added and admin account created (admin@paddy.vn / admin123).";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        $hashed = password_hash('admin123', PASSWORD_DEFAULT);
        $db->exec("INSERT IGNORE INTO users (name, email, password, role) VALUES ('Admin', 'admin@paddy.vn', '{$hashed}', 'admin')");
        echo "Role column already exists. Admin account ensured.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
