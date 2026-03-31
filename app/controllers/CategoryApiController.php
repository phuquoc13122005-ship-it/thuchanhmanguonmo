<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../utils/JWTHandler.php';

class CategoryApiController
{
    private $categoryModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    private function authenticateAdmin()
    {
        $headers = [];
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
            }
        }

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $arr = explode(" ", $authHeader);
            $jwt = $arr[1] ?? null;
            if ($jwt) {
                $decoded = $this->jwtHandler->decode($jwt);
                if ($decoded && isset($decoded['role']) && $decoded['role'] === 'admin') {
                    return true;
                }
            }
        }
        return false;
    }

    // Lấy danh sách danh mục
    public function index()
    {
        header('Content-Type: application/json');
        if ($this->authenticateAdmin()) {
            http_response_code(200);
            $categories = $this->categoryModel->getCategories();
            echo json_encode($categories);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Access Denied. Admins only.']);
        }
    }

    // Lấy chi tiết 1 danh mục
    public function show($id)
    {
        header('Content-Type: application/json');
        if ($this->authenticateAdmin()) {
            http_response_code(200);
            $category = $this->categoryModel->getCategoryById($id);
            if ($category) {
                echo json_encode($category);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Category not found']);
            }
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Access Denied. Admins only.']);
        }
    }

    // Thêm danh mục
    public function store()
    {
        header('Content-Type: application/json');
        if ($this->authenticateAdmin()) {
            $data = json_decode(file_get_contents("php://input"), true);
            $name = $data['name'] ?? '';
            $description = $data['description'] ?? '';

            $result = $this->categoryModel->addCategory($name, $description);

            if (is_array($result)) {
                http_response_code(400);
                echo json_encode(['errors' => $result]);
            } else {
                http_response_code(201);
                echo json_encode(['message' => 'Category created successfully']);
            }
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Access Denied. Admins only.']);
        }
    }

    // Sửa danh mục
    public function update($id)
    {
        header('Content-Type: application/json');
        if ($this->authenticateAdmin()) {
            $data = json_decode(file_get_contents("php://input"), true);
            $name = $data['name'] ?? '';
            $description = $data['description'] ?? '';

            $result = $this->categoryModel->updateCategory($id, $name, $description);

            if ($result) {
                http_response_code(200);
                echo json_encode(['message' => 'Category updated successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Category update failed']);
            }
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Access Denied. Admins only.']);
        }
    }

    // Xóa danh mục
    public function destroy($id)
    {
        header('Content-Type: application/json');
        if ($this->authenticateAdmin()) {
            $result = $this->categoryModel->deleteCategory($id);

            if ($result) {
                http_response_code(200);
                echo json_encode(['message' => 'Category deleted successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Category deletion failed (Vui lòng xóa các sản phẩm trong danh mục này trước)']);
            }
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Access Denied. Admins only.']);
        }
    }
}
?>
