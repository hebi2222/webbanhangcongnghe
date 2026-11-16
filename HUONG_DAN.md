# Bài bán hàng điện tử Online — Tri

Bài thực hành PHP thuần + MongoDB, dùng hàm và các file PHP riêng; không dùng framework, MVC hoặc đăng nhập. Giao diện tối, hồng rượu vang và vàng theo phong cách Tsuki. Đây là mã phục vụ bài thực hành chạy trên máy cá nhân.

## 1. Tên theo đúng đề

| Thành phần | Tên |
|---|---|
| Họ tên dùng trong tên CSDL | tri |
| CSDL | bh_tri |
| Collection hàng hóa | hanghoa_tri |
| Collection hóa đơn | hoadon_tri |
| Collection chi tiết hóa đơn | cthd_tri |
| Thư mục web | web_bh_tri |

Tên field, tên CSDL, tên collection và nội dung dữ liệu mẫu đều không dấu. Giao diện và hướng dẫn có dấu để dễ đọc. Giá là dữ liệu giả định dùng cho bài tập, không phải báo giá thị trường.

## 2. Kiểu dữ liệu đơn giản

### hanghoa_tri

| Field | Kiểu | Ví dụ |
|---|---|---|
| _id | String | HH001 |
| name | String | iPhone 15 |
| image | String | assets/images/hh001.svg |
| brand | String | Apple hoặc Samsung |
| price | Number | 15990000 |
| ngaycapnhat | Date | ISODate("2026-10-20T03:00:00Z") |

### hoadon_tri

| Field | Kiểu | Ví dụ |
|---|---|---|
| _id | String | HD001 |
| tenkh | String | Nguyen Van An |
| ngayhd | Date | ISODate("2026-10-21T03:00:00Z") |

### cthd_tri

| Field | Kiểu | Ví dụ |
|---|---|---|
| _id | String | CT001 |
| _idhoadon | String | HD001 |
| _idhanghoa | String | HH001 |
| soluong | Number (số nguyên) | 2 |

`cthd_tri._idhoadon` liên kết với `hoadon_tri._id`.
`cthd_tri._idhanghoa` liên kết với `hanghoa_tri._id`.

Ví dụ: CT001 nghĩa là hóa đơn HD001 mua sản phẩm HH001 với số lượng 2. Một hóa đơn có thể có nhiều dòng chi tiết.

**Chỗ hơi lệch trong đề:** `hoadon_tri` không có hai field `_idhoadon`, `_idhanghoa` trong danh sách field nhưng đề vẫn yêu cầu tạo index cho chúng. Bài này giữ nguyên cấu trúc hóa đơn và tạo hai index đó để đúng câu chữ. MongoDB cho phép tạo index cho field chưa có trong document; chúng không được dùng để liên kết ở bài này. Các index có tác dụng tra cứu dòng chi tiết nằm ở `cthd_tri`. Mỗi collection còn có index `_id_` tự động.

## 3. Nhập dữ liệu trong MongoDB Compass

1. Mở Compass, kết nối MongoDB đang chạy trên máy.
2. Mở **Open MongoDB shell** (một số phiên bản gọi là mongosh).
3. Mở `database/01_tao_va_nhap_du_lieu.js` bằng VS Code hoặc Notepad.
4. Dán các phần vào shell theo thứ tự: `use bh_tri`, tạo collection, tạo index, nhập dữ liệu.
5. Refresh Compass, mở CSDL `bh_tri`.

Chỉ chạy phần nhập mẫu một lần. Nếu đã có các mã HH001, HD001, CT001… mà chạy `insertMany` lại thì sẽ báo trùng `_id`. Không xóa CSDL cũ để xử lý lỗi này. Hãy xem lại dữ liệu đã nhập trước.

Nếu không có shell trong Compass, mở mongosh riêng và kết nối cùng địa chỉ Compass. File có lệnh `use bh_tri` là cú pháp shell tương tác; không chạy file này bằng Node.js hoặc trình duyệt.

Kết quả số document của dữ liệu mẫu:

| Collection | Số document |
|---|---:|
| hanghoa_tri | 8 |
| hoadon_tri | 6 |
| cthd_tri | 14 |

### Có thể nhập bằng JSON thay vì insertMany

Trong từng collection, chọn **Add Data → Import JSON or CSV file**, chọn file JSON tương ứng trong `database/`. Ba file là mảng JSON có Extended JSON `$date`, giúp MongoDB đọc đúng kiểu Date. Sau khi nhập, vẫn chạy bốn lệnh `createIndex` trong file lệnh.

Chọn một cách nhập: shell HOẶC JSON. Không nhập cả hai lần vào cùng collection.

## 4. Lệnh và ảnh cần chụp

Mở `database/02_truy_van.js`, chạy từng lệnh rồi chụp kết quả thật trên máy. File `ket_qua_mau.json` chỉ là kết quả đối chiếu tính từ dữ liệu mẫu, không thay cho ảnh chụp MongoDB.

### D1 — Chứng minh CSDL, collection và field

Chụp lần lượt:

1. CSDL `bh_tri` và danh sách ba collection.
2. Tab Documents của `hanghoa_tri`, hiển thị đủ các field.
3. Tab Documents của `hoadon_tri`.
4. Tab Documents của `cthd_tri`.

Có thể nhấn Edit một document để thấy kiểu dữ liệu, chụp rồi Cancel để tránh sửa nhầm.

### D3 — Index

```javascript
use bh_tri
db.cthd_tri.getIndexes()
```

Kết quả phải có ba index: `_id_`, `_idhoadon_1`, `_idhanghoa_1`.

### D3 tiếp theo — Doanh số bán hàng theo ngày

Truy vấn trong `02_truy_van.js` dùng ba bước chính:

1. `$lookup`: ghép dòng chi tiết với hóa đơn để lấy ngày và với hàng hóa để lấy giá.
2. `$unwind`: mở mảng kết quả ghép thành từng document để dùng các field bên trong.
3. `$group`: gom theo ngày, cộng số lượng và cộng `soluong × price`.

`$sort` sắp xếp ngày tăng dần. Ngày được tính theo múi giờ Việt Nam.

Kết quả tính từ dữ liệu mẫu ban đầu:

| Ngày | Tổng số lượng | Doanh số (VNĐ) |
|---|---:|---:|
| 2026-10-21 | 8 | 75.420.000 |
| 2026-10-22 | 10 | 71.900.000 |
| 2026-10-23 | 13 | 119.370.000 |

Không cộng trực tiếp `price` vì một dòng có thể mua nhiều sản phẩm. Không gom theo toàn bộ timestamp vì một ngày có nhiều giờ khác nhau.

### D4 — Top 5 tiêu thụ nhanh nhất

Trong bài này, hiểu là năm mặt hàng có **tổng số lượng bán cao nhất trong toàn bộ dữ liệu**. Đề không quy định khoảng thời gian hay tốc độ bán/ngày.

`$group` cộng số lượng theo mã hàng, `$sort` giảm dần, `$limit: 5` lấy năm dòng, `$lookup` lấy tên sản phẩm. Khi bằng số lượng, sắp theo mã để kết quả ổn định.

| Thứ tự | Mã | Sản phẩm | Tổng số lượng |
|---|---|---|---:|
| 1 | HH004 | AirPods 4 | 10 |
| 2 | HH006 | Samsung Galaxy A55 | 9 |
| 3 | HH003 | iPad Gen 10 | 5 |
| 4 | HH001 | iPhone 15 | 4 |
| 5 | HH005 | Samsung Galaxy S24 | 3 |

Theo đúng các field mà đề cho, doanh số dùng giá hiện tại trong `hanghoa_tri`. Vì không có field giá tại thời điểm bán, sửa giá hoặc xóa hàng đã bán sẽ làm kết quả thống kê thay đổi. Chụp kết quả thống kê trước khi thử sửa/xóa; dùng sản phẩm mới HH009 để kiểm tra chức năng xóa. Một hệ thống thực tế cần lưu thêm đơn giá bán trong chi tiết hóa đơn, nhưng bài này giữ đúng cấu trúc được giao.

## 5. Xuất JSON để nộp

Các file JSON mẫu đã có trong `database/`:

- hanghoa_tri.json
- hoadon_tri.json
- cthd_tri.json

Nếu cần chứng minh xuất dữ liệu thực tế hoặc đã sửa dữ liệu, tự xuất lại từ Compass:

1. Mở collection và bỏ trống bộ lọc để lấy đủ document.
2. Chọn **Export Data → Export the full collection** (tên nút có thể khác theo phiên bản).
3. Chọn JSON; chọn Extended JSON nếu có lựa chọn định dạng để giữ kiểu Date.
4. Lưu theo đúng tên collection ở trên.
5. Lặp lại cho ba collection.

File JSON không mang theo index. Index phải được tạo bằng lệnh và chụp riêng.

## 6. Chạy website

### Điều kiện

- PHP 8.1 trở lên, extension MongoDB đã bật.
- PHP có các extension mà Composer yêu cầu, gồm DOM và mbstring cho Dompdf.
- MongoDB đang chạy; Compass chỉ là công cụ quản lý, không thay cho MongoDB Server.
- Composer đã cài trên máy.

MongoDB PHP extension và thư viện `mongodb/mongodb` là hai thành phần khác nhau. Bật extension trước rồi mới cài thư viện bằng Composer.

### Bước 1: Đặt thư mục web

Giải nén, chép thư mục `web_bh_tri` vào:

```text
C:\xampp\htdocs\web_bh_tri
```

Nếu môi trường PHP của Hebi dùng thư mục web khác, chép vào thư mục phục vụ của môi trường đó. Giữ tên `web_bh_tri` theo yêu cầu.

### Bước 2: Sửa kết nối

Mở `config.php`:

```php
$mongoUri = 'mongodb://localhost:27017';
$databaseName = 'bh_tri';
```

Đổi `27017` thành `27018` nếu Compass của Hebi đang kết nối cổng 27018. Địa chỉ PHP và Compass phải trùng nhau. Không đổi sang CSDL bài nhà sách cũ.

### Bước 3: Cài thư viện

Mở Terminal/CMD trong `web_bh_tri`:

```bat
composer install
```

Lệnh đọc `composer.json`, cài thư viện MongoDB và Dompdf, tạo thư mục `vendor` cùng file khóa phiên bản `composer.lock`. Bộ mã nguồn ban đầu chưa kèm `vendor` hoặc lock vì thư viện chưa được cài trong môi trường tạo bài.

Nếu không tìm thấy `php`, thêm thư mục PHP vào PATH hoặc dùng CMD:

```bat
set PATH=C:\xampp\php;%PATH%
composer install
```

Nếu Composer báo `ext-mongodb` bị thiếu, kiểm tra `php --ini`, bật extension MongoDB đúng với phiên bản PHP/kiến trúc/TS hoặc NTS đang dùng, rồi khởi động lại Apache. Không dùng `--ignore-platform-reqs` để bỏ qua lỗi.

### Bước 4: Mở web

Bật Apache rồi mở:

```text
http://localhost/web_bh_tri/
```

Nếu Apache dùng cổng 8080:

```text
http://localhost:8080/web_bh_tri/
```

Hoặc đứng trong thư mục web, chạy PHP development server:

```bat
php -S localhost:8080
```

Khi đó mở `http://localhost:8080/`.

### Bước 5: Mở quản lý

Nhấn liên kết “Quản lý sản phẩm” ở cuối trang hoặc mở `quanly.php`. Thanh menu chính giữ đúng ba mục: Trang chủ, Apple, Samsung.

## 7. Các file PHP để học theo

| File | Nhiệm vụ |
|---|---|
| config.php | Kết nối bh_tri, chọn hanghoa_tri |
| functions.php | Hàm định dạng tiền/ngày, lọc tìm kiếm, thông báo, token |
| header.php / footer.php | Phần đầu và cuối dùng chung |
| index.php | Mỗi hãng lấy ba sản phẩm mới nhất |
| the_sanpham.php | Thẻ hiển thị một sản phẩm |
| sanpham.php | Nhận brand và q bằng GET, lọc và đếm kết quả |
| chitiet.php | Nhận id, tìm một sản phẩm |
| quanly.php | Danh sách mới nhất, lọc, tìm, nút thêm/sửa/xóa/PDF |
| form.php | Một form dùng chung cho thêm và sửa; kiểm tra giá phía PHP |
| xoa.php | Nhận POST, xóa, thông báo kết quả |
| xuat_pdf.php | Xuất danh sách đang lọc/tìm thành PDF thật qua Dompdf |
| assets/style.css | Giao diện và bố cục trên điện thoại |

Không có giỏ hàng, thanh toán hoặc quản lý hóa đơn trên web vì phần E chỉ yêu cầu hiển thị và quản lý sản phẩm. Hóa đơn, chi tiết hóa đơn phục vụ dữ liệu và truy vấn phần A–D.

Tìm kiếm tên không phân biệt chữ hoa/thường, tìm theo một đoạn chữ; dữ liệu mẫu không dấu nên nhập từ khóa không dấu, ví dụ `iphone`, `galaxy`, `airpods`. Kết quả có số lượng, hình, tên, giá, hãng và ngày cập nhật; nhấn vào hình/tên để xem chi tiết.

Hình trong bộ mã là hình minh họa SVG tự dựng, không phải ảnh chụp sản phẩm. Có thể chép ảnh thật vào `assets/images/`, ví dụ `iphone15.jpg`, rồi sửa field image thành `assets/images/iphone15.jpg`. Không nhập đường dẫn tuyệt đối như `C:\...`; dùng đường dẫn tương đối. Khi thêm/sửa từ web, nhập tên sản phẩm không dấu nếu muốn toàn bộ dữ liệu tiếp tục không dấu.

## 8. Kiểm tra chức năng trên máy

| Thử | Kết quả mong đợi |
|---|---|
| Trang chủ | Hai cụm Apple, Samsung; mỗi cụm ba sản phẩm mới nhất |
| Menu Apple | Bốn sản phẩm Apple với dữ liệu ban đầu |
| Menu Samsung | Bốn sản phẩm Samsung với dữ liệu ban đầu |
| Tìm `iphone` | Một sản phẩm, có báo số lượng |
| Tìm `khongcotensanpham` | Không có sản phẩm, báo số lượng 0 |
| Nhấn tên hoặc ảnh | Mở đúng chi tiết theo _id dạng chuỗi |
| Vào mã không tồn tại | Hiển thị không tìm thấy sản phẩm |
| Thêm HH009, tên Test Apple, giá 1000000 | Báo thành công, sản phẩm nằm đầu danh sách |
| Thêm lại mã HH009 | Báo mã đã tồn tại |
| Sửa giá thành 0 hoặc -100 | PHP từ chối, báo giá phải lớn hơn 0; không cập nhật DB |
| Sửa giá HH009 thành 1200000 | Báo thành công, lưu giá và ngày cập nhật mới |
| Lọc Apple rồi xuất PDF | Tải file hanghoa_tri.pdf, chỉ gồm Apple |
| Xóa HH009 | Xác nhận, xóa thành công và quay về danh sách |

Đề yêu cầu kiểm tra `price > 0`, phần kiểm tra nằm trong PHP nên không phụ thuộc kiểm tra HTML của trình duyệt.

## 9. Phần đã và chưa kiểm tra ở môi trường tạo bài

Đã kiểm tra số lượng, các field, kiểu dữ liệu JSON mẫu, mã liên kết, tổng tiền theo ngày, Top 5, tên file ảnh và các đường dẫn nội bộ. Đã kiểm tra cú pháp các file lệnh bằng JavaScript sau khi thay phần lệnh shell tương tác để kiểm tra cú pháp.

Môi trường tạo bài không có PHP, MongoDB Server và Composer, nên chưa chạy `php -l`, chưa chạy website với MongoDB thật và chưa render PDF qua Dompdf. Cần thực hiện bảng kiểm tra ở mục 8 trên máy Hebi sau khi cài thư viện. Không có ảnh chụp MongoDB giả lập trong bài.

## 10. Tham khảo cài thư viện

- MongoDB PHP Library: https://www.mongodb.com/docs/php-library/current/get-started/
- Dompdf: https://github.com/dompdf/dompdf
