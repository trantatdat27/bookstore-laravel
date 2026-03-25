# 📚 Hệ Thống Quản Lý Nhà Sách - Laravel Project

Dự án website bán sách trực tuyến được xây dựng dựa trên framework Laravel, cung cấp đầy đủ các tính năng quản lý cho Admin và trải nghiệm mua sắm cho khách hàng.

## 🌟 Các Tính Năng Đã Hoàn Thành

### 👨‍💼 Quản trị viên (Admin)
- **Quản lý kho sách**: Thêm mới, chỉnh sửa thông tin sách (Tên, tác giả, giá, ảnh bìa, mô tả) và xóa sách.
- **Quản lý danh mục**: Phân loại sách theo các chủ đề.
- **Quản lý đơn hàng**: Theo dõi danh sách đơn đặt hàng từ khách và cập nhật trạng thái (Chờ xử lý, Đang giao, Hoàn thành, Hủy).

### 🛒 Khách hàng (Client)
- **Cửa hàng trực tuyến**: Xem danh sách sách mới nhất, tìm kiếm sách theo tên hoặc lọc theo danh mục.
- **Chi tiết sản phẩm**: Xem mô tả chi tiết nội dung cuốn sách.
- **Giỏ hàng & Thanh toán**: Đặt hàng trực tuyến dễ dàng.
- **🔍 Tra cứu đơn hàng**: Tính năng đặc biệt cho phép khách hàng tra cứu lịch sử và trạng thái đơn hàng chỉ bằng số điện thoại.

## 🛠 Công Nghệ Sử Dụng
- **Backend**: Laravel 9/10 (PHP 8.0+)
- **Frontend**: Blade Template, Bootstrap 5 (Responsive giao diện máy tính & điện thoại), CSS3 & HTML5.
- **Database**: MySQL.
- **Auth**: Laravel Breeze (Hệ thống đăng nhập/đăng ký).

## 🚀 Hướng Dẫn Cài Đặt
1. Clone dự án: `git clone https://github.com/trantatdat27/bookstore-laravel.git`
2. Cài đặt PHP dependencies: `composer install`
3. Cài đặt Frontend dependencies: `npm install && npm run dev`
4. Cấu hình file `.env` (tạo database và kết nối).
5. Chạy migration: `php artisan migrate`
6. Khởi động server: `php artisan serve`

---
**Tác giả:** Trần Tất Đạt