# Hướng dẫn Kiểm thử RESTful API bằng Postman

Tài liệu này cung cấp hướng dẫn chi tiết từng bước để kiểm thử các API hiện có trong dự án, bao gồm cấu hình request (Method, URL, Headers, Body) và kết quả phản hồi mong đợi.

**Lưu ý chung:**
- Base URL tham khảo: `http://localhost:81/thuchanhmanguonmo-main` (Thay đổi số cổng và tên thư mục cho khớp đúng với môi trường máy bạn).
- Bất kì API nào yêu cầu truyền dữ liệu đầu vào (VD: Thêm, Sửa, Đăng nhập), trên Postman hãy chọn mục **Body** -> Chọn **raw** -> Chọn định dạng mũi tên xổ xuống từ Text sang **JSON**.

---

## 1. API Xác thực và Phân quyền (Auth)

### Đăng nhập & Lấy JWT Token
Đây là API quan trọng nhất. Bạn phải gọi API này đầu tiên để lấy mã Token dùng cho quyền Thêm/Sửa/Xóa Sản phẩm.

* **URL:** `/api/auth`
* **Method:** `POST`
* **Headers:** Không yêu cầu
* **Body (JSON):**
```json
{
    "email": "admin",
    "password": "123456"
}
```
* **Kết quả thành công (Mã Status 200 OK):**
```json
{
    "message": "Login successful",
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE2OTA1MzMxNjMsImV4c..."
}
```
* **Hành động tiếp theo:** Hãy bôi đen copy toàn bộ chuỗi ký tự trong trường `"token"` (bỏ 2 dấu ngoặc kép) để sử dụng thiết lập bảo mật cho các API phía dưới!

---

## 2. API Quản lý Sản phẩm (Product)

> **QUAN TRỌNG:** Tất cả các API quản lý sản phẩm dưới đây đều đã được bảo vệ bằng JWT Token (từ Bài số 6).
> Vì vậy bạn **bắt buộc** phải đính kèm Header sau vào tất cả các request này trong tab **Headers**:
> * Cột **Key:** `Authorization`
> * Cột **Value:** `Bearer <dán_chuỗi_token_vừa_copy_ở_bước_1_vào_đây>` *(Chú ý có 1 dấu khoảng trắng sau chữ Bearer)*

### 2.1. Lấy toàn bộ danh sách Sản phẩm
* **URL:** `/api/product`
* **Method:** `GET`
* **Body:** Không yêu cầu
* **Kết quả thành công (200 OK):**
```json
[
    {
        "id": 1,
        "name": "Thức ăn hạt Minino",
        "description": "Dành cho Mèo trưởng thành",
        "price": 120000,
        "category_id": 2,
        "category_name": "Mèo"
    },
    ...
]
```

### 2.2. Lấy thông tin Chi tiết 1 Sản phẩm
* **URL:** `/api/product/1` *(Ví dụ lấy sản phẩm có id = 1)*
* **Method:** `GET`
* **Body:** Không yêu cầu
* **Kết quả thành công (200 OK):**
```json
{
    "id": 1,
    "name": "Thức ăn hạt Minino",
    "description": "Dành cho Mèo trưởng thành",
    "price": 120000,
    "category_id": 2,
    "category_name": "Mèo"
}
```

### 2.3. Thêm một Sản phẩm mới
* **URL:** `/api/product`
* **Method:** `POST`
* **Body (JSON):**
```json
{
    "name": "Đồ chơi gặm cho Chó",
    "description": "Làm từ cao su thiên nhiên siêu bền",
    "price": 85000,
    "category_id": 1
}
```
* **Kết quả thành công (201 Created):**
```json
{
    "message": "Product created successfully"
}
```

### 2.4. Chỉnh sửa thông tin Sản phẩm
* **URL:** `/api/product/5` *(Ví dụ cập nhật sản phẩm có id = 5)*
* **Method:** `PUT`
* **Body (JSON):**
```json
{
    "id": 5,
    "name": "Đồ chơi gặm cho Chó (Mẫu mới 2026)",
    "description": "Có thêm âm thanh vui nhộn",
    "price": 95000,
    "category_id": 1
}
```
* **Kết quả thành công (200 OK):**
```json
{
    "message": "Product updated successfully"
}
```

### 2.5. Xóa một Sản phẩm
* **URL:** `/api/product/5` *(Ví dụ xóa sản phẩm mang id = 5)*
* **Method:** `DELETE`
* **Body:** Không yêu cầu
* **Kết quả thành công (200 OK):**
```json
{
    "message": "Product deleted successfully"
}
```

---

## 3. API Quản lý Danh mục (Category)

> **QUAN TRỌNG:** Theo cấu hình mới, các API quản lý danh mục (Tuyệt đối tất cả Thêm/Sửa/Xóa và cả Lấy danh sách) hiện tại ĐỀU YÊU CẦU QUYỀN ADMIN. 
> Vì vậy bạn **bắt buộc** phải đính kèm Header (lấy Token từ tài khoản admin đăng nhập) tương tự phần Sản phẩm:
> * Cột **Key:** `Authorization`
> * Cột **Value:** `Bearer <dán_chuỗi_token_admin_vào_đây>`

### 3.1. Lấy danh sách Danh mục
* **URL:** `/api/category`
* **Method:** `GET`
* **Body:** Không yêu cầu
* **Kết quả thành công (200 OK):**
```json
[
    {
        "id": 1,
        "name": "Chó",
        "description": "Danh mục sản phẩm của cún"
    },
    {
        "id": 2,
        "name": "Mèo",
        "description": "Danh mục sản phẩm của mèo"
    }
]
```

### 3.2. Lấy thông tin Chi tiết 1 Danh mục
* **URL:** `/api/category/1` *(Ví dụ lấy danh mục có id = 1)*
* **Method:** `GET`
* **Body:** Không yêu cầu
* **Kết quả thành công (200 OK):**
```json
{
    "id": 1,
    "name": "Chó",
    "description": "Danh mục sản phẩm của cún"
}
```

### 3.3. Thêm một Danh mục mới
* **URL:** `/api/category`
* **Method:** `POST`
* **Body (JSON):**
```json
{
    "name": "Chim cảnh",
    "description": "Thức ăn và lồng cho chim"
}
```
* **Kết quả thành công (201 Created):**
```json
{
    "message": "Category created successfully"
}
```

### 3.4. Chỉnh sửa thông tin Danh mục
* **URL:** `/api/category/1` *(Ví dụ cập nhật danh mục có id = 1)*
* **Method:** `PUT`
* **Body (JSON):**
```json
{
    "name": "Chó (Đã cập nhật)",
    "description": "Tất tần tật sản phẩm cho các dòng cún"
}
```
* **Kết quả thành công (200 OK):**
```json
{
    "message": "Category updated successfully"
}
```

### 3.5. Xóa một Danh mục
* **URL:** `/api/category/1` *(Ví dụ xóa danh mục mang id = 1)*
* **Method:** `DELETE`
* **Body:** Không yêu cầu
* **Kết quả thành công (200 OK):**
```json
{
    "message": "Category deleted successfully"
}
```
