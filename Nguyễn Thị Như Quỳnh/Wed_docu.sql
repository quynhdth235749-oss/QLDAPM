-- ============================================================
-- DỰ ÁN: WEBSITE TRAO ĐỔI VÀ MUA BÁN ĐỒ CŨ
-- THÀNH VIÊN PHỤ TRÁCH CSDL: QUỲNH
-- ============================================================

CREATE DATABASE IF NOT EXISTS web_do_cu;
USE web_do_cu;

-- 1. BẢNG NGƯỜI DÙNG (Phụ trách chính: Vi - Module Tài khoản & Admin)
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

-- 2. BẢNG DANH MỤC SẢN PHẨM (Phụ trách chính: Quỳnh - Module Lọc/Tìm kiếm)
CREATE TABLE danh_muc (
    ma_danh_muc INT PRIMARY KEY AUTO_INCREMENT,
    ten_danh_muc VARCHAR(100) NOT NULL,
    mo_ta TEXT
);

-- 3. BẢNG SẢN PHẨM & TIN ĐĂNG (Phụ trách chính: Sang - Module Tin đăng & Sản phẩm)
CREATE TABLE san_pham (
    ma_san_pham INT PRIMARY KEY AUTO_INCREMENT,
    ma_nguoi_dung INT NOT NULL,
    ma_danh_muc INT NOT NULL,
    ten_san_pham VARCHAR(255) NOT NULL,
    mo_ta TEXT,
    hinh_anh VARCHAR(255), -- Ảnh sản phẩm đồ cũ
    gia DECIMAL(10, 2) NOT NULL,
    tinh_trang VARCHAR(50) DEFAULT 'da_qua_su_dung', -- Độ mới 90%, 95%, cũ...
    trang_thai ENUM('con_hang', 'da_dat', 'da_ban') DEFAULT 'con_hang',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ma_nguoi_dung) REFERENCES nguoi_dung(ma_nguoi_dung) ON DELETE CASCADE,
    FOREIGN KEY (ma_danh_muc) REFERENCES danh_muc(ma_danh_muc)
);

-- 4. BẢNG SMART-CHECKLIST (Phụ trách chính: Sang - Module Kiểm định chất lượng đồ cũ)
CREATE TABLE kiem_tra_chat_luong (
    ma_kiem_tra INT PRIMARY KEY AUTO_INCREMENT,
    ma_san_pham INT NOT NULL,
    tieu_chi VARCHAR(255) NOT NULL, -- Ví dụ: "Chưa qua sửa chữa", "Pin còn trên 80%"
    dat_yeu_cau BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (ma_san_pham) REFERENCES san_pham(ma_san_pham) ON DELETE CASCADE
);

-- 5. BẢNG GIAO DỊCH (Phụ trách chính: Quỳnh - Module Xác nhận giao dịch)
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

-- 6. BẢNG ĐÁNH GIÁ (Phụ trách chính: Quỳnh - Module Đánh giá uy tín)
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

-- ============================================================
-- CHỈ MỤC TỐI ƯU HÓA TỐC ĐỘ TÌM KIẾM (INDEXING)
-- ============================================================
CREATE INDEX idx_san_pham_tim_kiem ON san_pham(ten_san_pham, ma_danh_muc, gia);
CREATE INDEX idx_giao_dich_trang_thai ON giao_dich(trang_thai);