<?php
class Database {
private $host = "localhost";
private $db_name = "store";
private $username = "root";
private $password = "";
public $conn;
public function getConnection() {
    $this->conn = null;
    try {
        $this->conn = new PDO(
            "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
            $this->username,
            $this->password
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->initializeSchemaIfNeeded();
    } catch(PDOException $exception) {
        // If database does not exist yet, create it and reconnect.
        if ((int) $exception->getCode() === 1049) {
            try {
                $bootstrap = new PDO(
                    "mysql:host=" . $this->host . ";charset=utf8mb4",
                    $this->username,
                    $this->password
                );
                $bootstrap->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $bootstrap->exec(
                    "CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
                );
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->initializeSchemaIfNeeded();
            } catch(PDOException $createException) {
                echo "Connection error: " . $createException->getMessage();
            }
        } else {
            echo "Connection error: " . $exception->getMessage();
        }
    }
    return $this->conn;
    }

private function initializeSchemaIfNeeded() {
    $stmt = $this->conn->query("SHOW TABLES LIKE 'product'");
    if ($stmt->fetch() !== false) {
        return;
    }

    $schemaPath = dirname(__DIR__, 2) . '/database.sql';
    if (!file_exists($schemaPath)) {
        return;
    }

    $sql = file_get_contents($schemaPath);
    if ($sql === false) {
        return;
    }

    $lines = explode("\n", $sql);
    $filteredSql = '';
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (strpos($trimmed, '--') === 0) {
            continue;
        }
        if (stripos($trimmed, 'CREATE DATABASE') === 0 || stripos($trimmed, 'USE ') === 0) {
            continue;
        }
        $filteredSql .= $line . "\n";
    }

    $queries = explode(';', $filteredSql);
    foreach ($queries as $query) {
        $query = trim($query);
        if ($query === '') {
            continue;
        }
        $this->conn->exec($query);
    }
}
}