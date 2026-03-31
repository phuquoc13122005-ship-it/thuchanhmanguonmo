<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class CategoryController
{
    private $categoryModel;
    private $db;
    
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            die("Access Denied. Admins only.");
        }
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
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
        $categories = $this->categoryModel->getCategories();
        include __DIR__ . '/../views/category/list.php';
    }

    public function add()
    {
        include_once __DIR__ . '/../views/category/add.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            $result = $this->categoryModel->addCategory($name, $description);

            if (is_array($result)) {
                $errors = $result;
                include __DIR__ . '/../views/category/add.php';
            } else {
                header('Location: ' . $this->basePath() . '/Category');
            }
        }
    }

    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            include __DIR__ . '/../views/category/edit.php';
        } else {
            echo "Không thấy danh mục.";
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            
            $edit = $this->categoryModel->updateCategory($id, $name, $description);
            if ($edit) {
                header('Location: ' . $this->basePath() . '/Category');
            } else {
                echo "Đã xảy ra lỗi khi lưu danh mục.";
            }
        }
    }

    public function delete($id)
    {
        if ($this->categoryModel->deleteCategory($id)) {
            header('Location: ' . $this->basePath() . '/Category');
        } else {
            echo "Đã xảy ra lỗi khi xóa danh mục.";
        }
    }
}
?>