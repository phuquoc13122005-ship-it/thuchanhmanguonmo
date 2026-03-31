<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    private $userModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->userModel = new UserModel($this->db);
    }

    private function basePath()
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($base === false || $base === '.' || $base === '\\') {
            return '';
        }
        return $base;
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $this->userModel->getUserByEmail($email);
            if ($user && password_verify($password, $user->password)) {
                // Success
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->name;
                $_SESSION['user_role'] = $user->role;
                
                $redirect = $_SERVER['HTTP_REFERER'] ?? $this->basePath() . '/Product';
                header("Location: " . $redirect);
                exit;
            } else {
                $_SESSION['login_error'] = "Email hoặc mật khẩu không đúng.";
                $redirect = $_SERVER['HTTP_REFERER'] ?? $this->basePath() . '/Product';
                header("Location: " . $redirect);
                exit;
            }
        }
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        
        $redirect = $_SERVER['HTTP_REFERER'] ?? $this->basePath() . '/Product';
        header("Location: " . $redirect);
        exit;
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if name is provided, otherwise split from email or use default
            $email = $_POST['email'] ?? '';
            $name = $_POST['name'] ?? explode('@', $email)[0] ?: 'Guest'; 
            $password = $_POST['password'] ?? '';

            $userId = $this->userModel->createUser($name, $email, $password);
            
            if ($userId) {
                // Auto login after register
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['register_success'] = "Tạo tài khoản thành công!";
            } else {
                $_SESSION['login_error'] = "Không thể tạo tài khoản, email này có thể đã được sử dụng.";
            }
            
            $redirect = $_SERVER['HTTP_REFERER'] ?? $this->basePath() . '/Product';
            header("Location: " . $redirect);
            exit;
        }
    }
}
?>
