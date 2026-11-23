# DEV NOTES – THI TRẮC NGHIỆM

> Sổ tay ghi nhớ khi phát triển, chạy thử và kiểm tra hệ thống.
> Cập nhật sau mỗi lần hoàn thành một chức năng.

## 1. Thông tin môi trường

- Project: `D:\ĐACN\thi-trac-nghiem`
- Laravel: `12.70.2`
- PHP: `8.5.12`
- Composer: `2.10.3`
- Node.js: `24.21.0`
- Git: `2.55.0.windows.5`
- Database: `thi_trac_nghiem`
- DB Host: `127.0.0.1`
- DB Port: `3306`
- GitHub: https://github.com/anhhong1308/thi-trac-nghiem
- Branch: `master`

## 2. Cách chạy project

### Trước khi chạy
1. Mở XAMPP.
2. Khởi động MySQL.
3. Kiểm tra database `thi_trac_nghiem`.
4. Mở PowerShell tại thư mục project.

### Chạy Laravel

```powershell
php artisan serve
```

### Chạy Vite

Mở terminal thứ hai:

```powershell
npm.cmd run dev
```

### Build frontend

```powershell
npm.cmd run build
```

### Kiểm tra route

```powershell
php artisan route:list
```

### Kiểm tra cú pháp PHP

```powershell
php -l đường/dẫn/file.php
```

## 3. Quy tắc quan trọng khi phát triển

- Luôn kiểm tra file hiện tại trước khi sửa.
- Không tự ý thay toàn bộ file nếu chưa xem nội dung cũ.
- Khi sửa code, kiểm tra cú pháp PHP hoặc build frontend.
- Sau khi sửa chức năng, phải kiểm tra trực tiếp trên website.
- Không chạy `php artisan migrate:fresh` trên database đang sử dụng.
- Không xóa migration cũ đã chạy.
- Nếu cần thay đổi database, tạo migration mới.
- Trước khi sửa nhiều file, kiểm tra `git status`.
- Commit Git sau khi hoàn thành một chức năng ổn định.
- Không sửa nhiều chức năng cùng lúc khi chưa kiểm tra chức năng trước.
- Không giả định chức năng hoạt động chỉ vì build thành công.
- Không lưu mật khẩu tài khoản thử nghiệm trong README hoặc GitHub.
- Không đưa file `.env` lên GitHub.

## 4. Quy tắc nghiệp vụ đã thống nhất

### Vai trò

- `admin`: Quản trị viên.
- `teacher`: Giảng viên.
- `student`: Sinh viên.

Không cho phép người dùng tự đăng ký Admin.

### Trạng thái tài khoản

- `pending`: Chờ duyệt.
- `approved`: Đã duyệt.
- `rejected`: Bị từ chối.

### Đăng ký tài khoản

- Sinh viên đăng ký: tự động `approved`.
- Giảng viên đăng ký: tự động `pending`.
- Giảng viên pending được đăng nhập nhưng không được sử dụng chức năng giảng viên.
- Admin chính duyệt tài khoản giảng viên.
- Tài khoản Admin hiện tại không bị ảnh hưởng.

### Quan hệ chuyên ngành và môn học

- Một chuyên ngành có nhiều môn học.
- Một môn học có thể thuộc nhiều chuyên ngành.
- Một giảng viên có thể đăng ký nhiều chuyên ngành.
- Một giảng viên có thể đăng ký nhiều môn học.
- Một môn học có thể được nhiều giảng viên phụ trách.
- Admin duyệt đăng ký chuyên ngành và môn học.
- Giảng viên chỉ quản lý dữ liệu thuộc môn học được duyệt.

## 5. Các lỗi và kinh nghiệm cần nhớ

### Laravel và PHP

- Nếu Laravel báo lỗi class hoặc route, kiểm tra `route:list`.
- Nếu PHP báo lỗi cú pháp, dùng `php -l`.
- Nếu sửa route hoặc cấu hình mà hệ thống chưa nhận, kiểm tra cache trước khi xử lý tiếp.

### Vue và Vite

- Nếu trang Vue trắng, kiểm tra Console trình duyệt.
- Kiểm tra tên file và đường dẫn Inertia render có khớp không.
- Sau khi sửa Vue, chạy `npm.cmd run build`.
- Không cần chạy Vite build để sử dụng mỗi lần nếu đang chạy `npm.cmd run dev`.

### Database

- Kiểm tra tên cột và khóa ngoại trước khi viết Controller.
- Không tự ý đổi tên bảng hoặc cột đã được sử dụng.
- Trước khi sửa quan hệ Model, kiểm tra cả hai phía của quan hệ.
- Không xóa dữ liệu thật để thử nghiệm.

### Git

Kiểm tra thay đổi:

```powershell
git status
```

Xem lịch sử:

```powershell
git log --oneline -10
```

Commit:

```powershell
git add .
git commit -m "Mo ta thay doi"
```

Push:

```powershell
git push origin master
```

## 6. Kiểm thử chức năng

Mỗi chức năng sau khi làm cần kiểm tra:

- [ ] Giao diện hiển thị đúng.
- [ ] Dữ liệu được lưu đúng database.
- [ ] Dữ liệu sửa được cập nhật chính xác.
- [ ] Xóa dữ liệu đúng đối tượng.
- [ ] Người không đủ quyền không truy cập được.
- [ ] Dữ liệu đầu vào không hợp lệ được báo lỗi.
- [ ] Không làm hỏng các chức năng đã có.
- [ ] Kiểm tra lại sau khi đăng xuất và đăng nhập.

Không đánh dấu hoàn thành chỉ vì không có lỗi cú pháp hoặc build thành công.

