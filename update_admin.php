<?php
require_once __DIR__ . '/app/config/database.php';
try {
    $db = (new Database())->getConnection();
    $hashed = password_hash('admin', PASSWORD_DEFAULT);
    
    // Insert or update 'admin' user
    $stmt = $db->prepare("SELECT id FROM users WHERE email='admin'");
    $stmt->execute();
    if ($stmt->fetch()) {
        $db->exec("UPDATE users SET password='{$hashed}', role='admin' WHERE email='admin'");
    } else {
        $db->exec("INSERT INTO users (name, email, password, role) VALUES ('Admin', 'admin', '{$hashed}', 'admin')");
    }
    
    // Optionally remove the old admin@paddy.vn user if it exists to avoid confusion
    $db->exec("DELETE FROM users WHERE email='admin@paddy.vn'");

    echo "Success: Admin account set to Username 'admin' and Password 'admin'";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
