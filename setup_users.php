<?php
require_once __DIR__ . '/app/config/database.php';
try {
    $db = (new Database())->getConnection();
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Add a default user for testing
    // password is '123456'
    $hashed = password_hash('123456', PASSWORD_DEFAULT);
    $db->exec("INSERT IGNORE INTO users (name, email, password) VALUES ('Khabib', 'khabib@example.com', '{$hashed}')");

    echo "Users table created and test user (khabib@example.com / 123456) seeded successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
