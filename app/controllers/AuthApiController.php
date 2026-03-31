<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../utils/JWTHandler.php';

class AuthApiController
{
    private $userModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->userModel = new UserModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    // Method `store` được dùng cho phương thức POST (theo cấu trúc routing trong index.php)
    public function store()
    {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents("php://input"), true);

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userModel->getUserByEmail($email);
        
        if ($user && password_verify($password, $user->password)) {
            $token_data = [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ];
            
            // Tạo JWT token
            $jwt = $this->jwtHandler->encode($token_data);
            
            http_response_code(200);
            echo json_encode([
                'message' => 'Login successful', 
                'token' => $jwt
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
        }
    }
    
    // Xử lý mặc định nếu gọi sai method GET
    public function index()
    {
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed. Use POST for login.']);
    }
}
?>
