-- ============================================================
-- DỰ ÁN: WEBSITE TRAO ĐỔI VÀ MUA BÁN ĐỒ CŨ
-- PHỤ TRÁCH CSDL: QUỲNH
-- ============================================================

CREATE DATABASE IF NOT EXISTS web_do_cu;
USE web_do_cu;

-- XÓA BẢNG CŨ NẾU ĐÃ TỒN TẠI
DROP TABLE IF EXISTS danh_gia;
DROP TABLE IF EXISTS giao_dich;
DROP TABLE IF EXISTS kiem_tra_chat_luong;
DROP TABLE IF EXISTS san_pham;
DROP TABLE IF EXISTS danh_muc;
DROP TABLE IF EXISTS nguoi_dung;

-- ============================================================
-- 1. TẠO CẤU TRÚC BẢNG (SCHEMA)
-- ============================================================

-- 1.1 Bảng Người dùng (Phụ trách: Vi)
CREATE TABLE nguoi_dung (
    ma_nguoi_dung INT PRIMARY KEY AUTO_INCREMENT,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mat_khau VARCHAR(255) NOT NULL,
    so_dien_thoai VARCHAR(15),
    dia_chi TEXT,
    vai_tro ENUM('user', 'admin') DEFAULT 'user',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 1.2 Bảng Danh mục (Phụ trách: Quỳnh)
CREATE TABLE danh_muc (
    ma_danh_muc INT PRIMARY KEY AUTO_INCREMENT,
    ten_danh_muc VARCHAR(100) NOT NULL,
    mo_ta TEXT
);

-- 1.3 Bảng Sản phẩm & Tin đăng (Phụ trách: Sang)
CREATE TABLE san_pham (
    ma_san_pham INT PRIMARY KEY AUTO_INCREMENT,
    ma_nguoi_dung INT NOT NULL,
    ma_danh_muc INT NOT NULL,
    ten_san_pham VARCHAR(255) NOT NULL,
    mo_ta TEXT,
    hinh_anh VARCHAR(255), -- Lưu đường dẫn hình ảnh đuôi .webp
    gia DECIMAL(10, 2) NOT NULL,
    tinh_trang VARCHAR(50) DEFAULT 'da_qua_su_dung',
    trang_thai ENUM('con_hang', 'da_dat', 'da_ban') DEFAULT 'con_hang',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ma_nguoi_dung) REFERENCES nguoi_dung(ma_nguoi_dung) ON DELETE CASCADE,
    FOREIGN KEY (ma_danh_muc) REFERENCES danh_muc(ma_danh_muc)
);

-- 1.4 Bảng Smart-Checklist Kiểm định (Phụ trách: Sang)
CREATE TABLE kiem_tra_chat_luong (
    ma_kiem_tra INT PRIMARY KEY AUTO_INCREMENT,
    ma_san_pham INT NOT NULL,
    tieu_chi VARCHAR(255) NOT NULL,
    dat_yeu_cau BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (ma_san_pham) REFERENCES san_pham(ma_san_pham) ON DELETE CASCADE
);

-- 1.5 Bảng Giao dịch (Phụ trách: Quỳnh)
CREATE TABLE giao_dich (
    ma_giao_dich INT PRIMARY KEY AUTO_INCREMENT,
    ma_san_pham INT NOT NULL,
    ma_nguoi_ban INT NOT NULL,
    ma_nguoi_mua INT NOT NULL,
    loai_giao_dich ENUM('mua', 'trao_doi') DEFAULT 'mua',
    trang_thai ENUM('cho_xac_nhan', 'da_xac_nhan', 'hoan_thanh', 'da_huy') DEFAULT 'cho_xac_nhan',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ma_san_pham) REFERENCES san_pham(ma_san_pham),
    FOREIGN KEY (ma_nguoi_ban) REFERENCES nguoi_dung(ma_nguoi_dung),
    FOREIGN KEY (ma_nguoi_mua) REFERENCES nguoi_dung(ma_nguoi_dung)
);

-- 1.6 Bảng Đánh giá (Phụ trách: Quỳnh)
CREATE TABLE danh_gia (
    ma_danh_gia INT PRIMARY KEY AUTO_INCREMENT,
    ma_giao_dich INT NOT NULL,
    ma_nguoi_danh_gia INT NOT NULL,
    ma_nguoi_nhan_danh_gia INT NOT NULL,
    diem_danh_gia INT CHECK (diem_danh_gia >= 1 AND diem_danh_gia <= 5),
    nhan_xet TEXT,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ma_giao_dich) REFERENCES giao_dich(ma_giao_dich),
    FOREIGN KEY (ma_nguoi_danh_gia) REFERENCES nguoi_dung(ma_nguoi_dung),
    FOREIGN KEY (ma_nguoi_nhan_danh_gia) REFERENCES nguoi_dung(ma_nguoi_dung)
);

-- Chỉ mục hỗ trợ tìm kiếm
CREATE INDEX idx_san_pham_tim_kiem ON san_pham(ten_san_pham, ma_danh_muc, gia);


-- ============================================================
-- 2. CHÈN DỮ LIỆU MẪU CÓ HÌNH ÁNH ĐUÔI WEBP (h1.webp -> h8.webp)
-- ============================================================

-- 2.1 Chèn Người dùng
INSERT INTO nguoi_dung (ma_nguoi_dung, ho_ten, email, mat_khau, so_dien_thoai, dia_chi, vai_tro) VALUES
(1, 'Quản Trị Viên (Admin)', 'admin@weddocu.com', '123456', '0901234567', 'TP. Hồ Chí Minh', 'admin'),
(2, 'Như Quỳnh', 'quynh@gmail.com', '123456', '0912345678', 'Quận 1, TP.HCM', 'user'),
(3, 'Tấn Sang', 'sang@gmail.com', '123456', '0923456789', 'Quận 5, TP.HCM', 'user'),
(4, 'Tường Vi', 'vi@gmail.com', '123456', '0934567890', 'Quận 7, TP.HCM', 'user');

-- 2.2 Chèn Danh mục sản phẩm
INSERT INTO danh_muc (ma_danh_muc, ten_danh_muc, mo_ta) VALUES
(1, 'Đồ điện tử & Công nghệ', 'Điện thoại, laptop, máy tính bảng cũ...'),
(2, 'Thời trang & Phụ kiện', 'Quần áo, giày dép, túi xách đã qua sử dụng...'),
(3, 'Sách & Giáo trình', 'Sách tham khảo, giáo trình đại học, truyện tranh...'),
(4, 'Đồ gia dụng & Nội thất', 'Bàn ghế học sinh, quạt máy, tủ đồ...');

-- 2.3 Chèn 8 Sản phẩm gắn hình ảnh từ h1.webp đến h8.webp
INSERT INTO san_pham (ma_san_pham, ma_nguoi_dung, ma_danh_muc, ten_san_pham, mo_ta, hinh_anh, gia, tinh_trang, trang_thai) VALUES
(1, 2, 1, 'iPhone 11 64GB VN/A cũ', 'Máy xài kỹ mới 95%, nguyên zin chưa qua sửa chữa', 'images/h1.webp', 5500000.00, 'Mới 95%', 'con_hang'),
(2, 3, 3, 'Giáo trình Quản lý Dự án Phần mềm', 'Sách còn mới 90%, không rách nát hay gạch xóa', 'images/h2.webp', 45000.00, 'Mới 90%', 'con_hang'),
(3, 4, 4, 'Bàn học gấp gọn sinh viên', 'Bàn gỗ ép chân thép chắc chắn, dọn phòng thừa nên bán', 'images/h3.webp', 80000.00, 'Đã qua sử dụng', 'da_ban'),
(4, 2, 2, 'Áo khoác Jean Unisex', 'Áo size L rộng rãi, mặc đúng 2 lần còn rất mới', 'images/h4.webp', 120000.00, 'Mới 99%', 'con_hang'),
(5, 3, 1, 'Tai nghe Bluetooth Sony WH-1000XM4', 'Tai nghe chống ồn tốt, hoạt động bình thường, kèm hộp', 'images/h5.webp', 3200000.00, 'Mới 90%', 'con_hang'),
(6, 4, 2, 'Giày Sneaker Nike Air Force 1 Cũ', 'Size 41, đế chưa mòn nhiều, đã vệ sinh sạch sẽ', 'images/h6.webp', 650000.00, 'Mới 85%', 'con_hang'),
(7, 2, 4, 'Quạt đứng Senko có điều khiển', 'Quạt chạy êm, gió mạnh, chuyển nhà không mang theo được', 'images/h7.webp', 150000.00, 'Đã qua sử dụng', 'con_hang'),
(8, 3, 3, 'Bộ sách Đắc Nhân Tâm & Quẳng Gánh Lo Đi', 'Combo 2 cuốn sách kỹ năng sống còn rất mới', 'images/h8.webp', 70000.00, 'Mới 98%', 'con_hang');

-- 2.4 Chèn dữ liệu Smart-Checklist
INSERT INTO kiem_tra_chat_luong (ma_san_pham, tieu_chi, dat_yeu_cau) VALUES
(1, 'Màn hình không trầy xước nặng', TRUE),
(1, 'Dung lượng pin trên 80%', TRUE),
(1, 'Chưa từng qua sửa chữa (Nguyên zin)', TRUE),
(2, 'Không mất trang, rách trang', TRUE),
(3, 'Chân bàn không bị rỉ sét', TRUE),
(5, 'Pin dùng liên tục > 20 giờ', TRUE),
(6, 'Đế giày nguyên vẹn, không bục keo', TRUE);

-- 2.5 Chèn Giao dịch mẫu
INSERT INTO giao_dich (ma_giao_dich, ma_san_pham, ma_nguoi_ban, ma_nguoi_mua, loai_giao_dich, trang_thai) VALUES
(1, 3, 3, 4, 'mua', 'hoan_thanh');

-- 2.6 Chèn Đánh giá mẫu
INSERT INTO danh_gia (ma_giao_dich, ma_nguoi_danh_gia, ma_nguoi_nhan_danh_gia, diem_danh_gia, nhan_xet) VALUES
(1, 4, 3, 5, 'Người bán thân thiện, giao bàn nhanh đúng như mô tả!');