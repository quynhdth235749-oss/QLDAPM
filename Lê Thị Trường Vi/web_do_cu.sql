-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th10 02, 2026 lúc 05:03 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `web_do_cu`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_gia`
--

CREATE TABLE `danh_gia` (
  `ma_danh_gia` int(11) NOT NULL,
  `ma_giao_dich` int(11) NOT NULL,
  `ma_nguoi_danh_gia` int(11) NOT NULL,
  `ma_nguoi_nhan_danh_gia` int(11) NOT NULL,
  `diem_danh_gia` int(11) DEFAULT NULL CHECK (`diem_danh_gia` >= 1 and `diem_danh_gia` <= 5),
  `nhan_xet` text DEFAULT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_gia`
--

INSERT INTO `danh_gia` (`ma_danh_gia`, `ma_giao_dich`, `ma_nguoi_danh_gia`, `ma_nguoi_nhan_danh_gia`, `diem_danh_gia`, `nhan_xet`, `ngay_tao`) VALUES
(1, 1, 1, 2, 5, 'Máy iPhone 12 Pro Max đẹp như mới, pin trâu!', '2026-11-02 15:54:34'),
(2, 2, 2, 1, 4, 'Nồi chiên dùng tốt, chủ nhà dễ tính.', '2026-11-02 15:54:34'),
(3, 3, 1, 2, 5, 'Laptop Dell mượt mà, đúng mô tả.', '2026-11-02 15:54:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE `danh_muc` (
  `ma_danh_muc` int(11) NOT NULL,
  `ten_danh_muc` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`ma_danh_muc`, `ten_danh_muc`, `mo_ta`) VALUES
(1, 'Đồ điện tử', 'Điện thoại, Laptop, Máy ảnh, Tai nghe cũ'),
(2, 'Thời trang & Phụ kiện', 'Quần áo, Giày dép, Túi xách cũ'),
(3, 'Đồ gia dụng', 'Bàn ghế, Tủ, Nồi chiên, Đồ dùng bếp'),
(4, 'Sách & Giải trí', 'Sách cũ, Truyện tranh, Đồ chơi trẻ em');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giao_dich`
--

CREATE TABLE `giao_dich` (
  `ma_giao_dich` int(11) NOT NULL,
  `ma_san_pham` int(11) NOT NULL,
  `ma_nguoi_ban` int(11) NOT NULL,
  `ma_nguoi_mua` int(11) NOT NULL,
  `loai_giao_dich` enum('mua','trao_doi') DEFAULT 'mua',
  `trang_thai` enum('cho_xac_nhan','da_xac_nhan','hoan_thanh','da_huy') DEFAULT 'cho_xac_nhan',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `giao_dich`
--

INSERT INTO `giao_dich` (`ma_giao_dich`, `ma_san_pham`, `ma_nguoi_ban`, `ma_nguoi_mua`, `loai_giao_dich`, `trang_thai`, `ngay_tao`) VALUES
(1, 1, 2, 1, 'mua', 'hoan_thanh', '2026-11-02 15:54:34'),
(2, 2, 1, 2, 'mua', 'hoan_thanh', '2026-11-02 15:54:34'),
(3, 3, 2, 1, 'mua', 'hoan_thanh', '2026-11-02 15:54:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kiem_tra_chat_luong`
--

CREATE TABLE `kiem_tra_chat_luong` (
  `ma_kiem_tra` int(11) NOT NULL,
  `ma_san_pham` int(11) NOT NULL,
  `tieu_chi` varchar(255) NOT NULL,
  `dat_yeu_cau` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `kiem_tra_chat_luong`
--

INSERT INTO `kiem_tra_chat_luong` (`ma_kiem_tra`, `ma_san_pham`, `tieu_chi`, `dat_yeu_cau`) VALUES
(1, 3, 'Màn hình không điểm chết, không ám màu', 1),
(2, 3, 'Bàn phím và Touchpad hoạt động nhạy', 1),
(3, 3, 'Pin cầm trên 3 tiếng', 1),
(4, 4, 'Vải không sờn rách, đường may chắc chắn', 1),
(5, 4, 'Nút bấm và khóa kéo hoạt động mượt', 1),
(6, 5, 'Piston nâng hạ chiều cao mượt mà', 1),
(7, 5, 'Bánh xe di chuyển bình thường', 1),
(8, 6, 'Đầy đủ trang, không bung gáy', 1),
(9, 6, 'Giấy sạch đẹp không ố vàng nặng', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `ma_nguoi_dung` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `dia_chi` text DEFAULT NULL,
  `vai_tro` enum('user','admin') DEFAULT 'user',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`ma_nguoi_dung`, `ho_ten`, `email`, `mat_khau`, `so_dien_thoai`, `dia_chi`, `vai_tro`, `ngay_tao`) VALUES
(1, 'Nguyễn Thị Như Quỳnh', 'quynh@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.0q/sL/4H6gYwGfO1F4S5Q/e8/6', '0901234567', 'TP. Hồ Chí Minh', 'user', '2026-11-02 14:46:51'),
(2, 'Nguyễn Dương Kiều Sang', 'sang@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.0q/sL/4H6gYwGfO1F4S5Q/e8/6', '0912345678', 'Hà Nội', 'user', '2026-11-02 14:46:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham`
--

CREATE TABLE `san_pham` (
  `ma_san_pham` int(11) NOT NULL,
  `ma_nguoi_dung` int(11) NOT NULL,
  `ma_danh_muc` int(11) NOT NULL,
  `ten_san_pham` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `gia` decimal(10,2) NOT NULL,
  `tinh_trang` varchar(50) DEFAULT 'da_qua_su_dung',
  `trang_thai` enum('con_hang','da_dat','da_ban') DEFAULT 'con_hang',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`ma_san_pham`, `ma_nguoi_dung`, `ma_danh_muc`, `ten_san_pham`, `mo_ta`, `hinh_anh`, `gia`, `tinh_trang`, `trang_thai`, `ngay_tao`) VALUES
(1, 2, 1, 'iPhone 12 Pro Max 128GB', 'Máy dùng lướt, chưa qua sửa chữa, pin 88%, máy đẹp không trầy.', '../images/product/iphone12.jpg', 12500000.00, 'Đã qua sử dụng (95%)', 'con_hang', '2026-11-02 14:46:51'),
(2, 1, 3, 'Nồi chiên không dầu Lock&Lock 5.2L', 'Dùng tốt, lòng nồi chống dính còn rất mới, cần đổi nồi to hơn.', '../images/product/noichien.jpg', 850000.00, 'Đã qua sử dụng (90%)', 'con_hang', '2026-11-02 14:46:51'),
(3, 1, 1, 'Laptop Dell XPS 13 9305', 'Máy chính chủ dùng làm văn phòng, còn mới 95%, ram 16GB, SSD 512GB, kèm sạc zin.', '../images/product/laptop.jpg', 11200000.00, 'Đã qua sử dụng (95%)', 'con_hang', '2026-11-02 15:12:33'),
(4, 2, 2, 'Áo khoác Jean Vintage Unisex', 'Size L, chất jean dày dặn, mặc được vài lần còn rất mới không sờn vải.', '../images/product/aokhoac.jpg', 180000.00, 'Đã qua sử dụng (90%)', 'con_hang', '2026-11-02 15:12:33'),
(5, 1, 3, 'Ghế xoay công sở lưới thoáng khí', 'Ghế nâng hạ chiều cao tốt, đệm ngồi êm ái, thích hợp ngồi làm việc dài hạn.', '../images/product/ghexoay.jpg', 350000.00, 'Đã qua sử dụng (85%)', 'con_hang', '2026-11-02 15:12:33'),
(6, 2, 4, 'Bộ truyện tranh Thám tử lừng danh Conan (Tập 1-50)', 'Sách giữ gìn cẩn thận, giấy đẹp không rách nát, thích hợp cho bạn nào sưu tầm.', '../images/product/truyenconan.jpg', 650000.00, 'Đã qua sử dụng (90%)', 'con_hang', '2026-11-02 15:12:33');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD PRIMARY KEY (`ma_danh_gia`),
  ADD KEY `ma_giao_dich` (`ma_giao_dich`),
  ADD KEY `ma_nguoi_danh_gia` (`ma_nguoi_danh_gia`),
  ADD KEY `ma_nguoi_nhan_danh_gia` (`ma_nguoi_nhan_danh_gia`);

--
-- Chỉ mục cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`ma_danh_muc`);

--
-- Chỉ mục cho bảng `giao_dich`
--
ALTER TABLE `giao_dich`
  ADD PRIMARY KEY (`ma_giao_dich`),
  ADD KEY `ma_san_pham` (`ma_san_pham`),
  ADD KEY `ma_nguoi_ban` (`ma_nguoi_ban`),
  ADD KEY `ma_nguoi_mua` (`ma_nguoi_mua`),
  ADD KEY `idx_giao_dich_trang_thai` (`trang_thai`);

--
-- Chỉ mục cho bảng `kiem_tra_chat_luong`
--
ALTER TABLE `kiem_tra_chat_luong`
  ADD PRIMARY KEY (`ma_kiem_tra`),
  ADD KEY `ma_san_pham` (`ma_san_pham`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`ma_nguoi_dung`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD PRIMARY KEY (`ma_san_pham`),
  ADD KEY `ma_nguoi_dung` (`ma_nguoi_dung`),
  ADD KEY `ma_danh_muc` (`ma_danh_muc`),
  ADD KEY `idx_san_pham_tim_kiem` (`ten_san_pham`,`ma_danh_muc`,`gia`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `ma_danh_gia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `ma_danh_muc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `giao_dich`
--
ALTER TABLE `giao_dich`
  MODIFY `ma_giao_dich` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `kiem_tra_chat_luong`
--
ALTER TABLE `kiem_tra_chat_luong`
  MODIFY `ma_kiem_tra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `ma_nguoi_dung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  MODIFY `ma_san_pham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD CONSTRAINT `danh_gia_ibfk_1` FOREIGN KEY (`ma_giao_dich`) REFERENCES `giao_dich` (`ma_giao_dich`),
  ADD CONSTRAINT `danh_gia_ibfk_2` FOREIGN KEY (`ma_nguoi_danh_gia`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`),
  ADD CONSTRAINT `danh_gia_ibfk_3` FOREIGN KEY (`ma_nguoi_nhan_danh_gia`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`);

--
-- Các ràng buộc cho bảng `giao_dich`
--
ALTER TABLE `giao_dich`
  ADD CONSTRAINT `giao_dich_ibfk_1` FOREIGN KEY (`ma_san_pham`) REFERENCES `san_pham` (`ma_san_pham`),
  ADD CONSTRAINT `giao_dich_ibfk_2` FOREIGN KEY (`ma_nguoi_ban`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`),
  ADD CONSTRAINT `giao_dich_ibfk_3` FOREIGN KEY (`ma_nguoi_mua`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`);

--
-- Các ràng buộc cho bảng `kiem_tra_chat_luong`
--
ALTER TABLE `kiem_tra_chat_luong`
  ADD CONSTRAINT `kiem_tra_chat_luong_ibfk_1` FOREIGN KEY (`ma_san_pham`) REFERENCES `san_pham` (`ma_san_pham`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `san_pham_ibfk_1` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`) ON DELETE CASCADE,
  ADD CONSTRAINT `san_pham_ibfk_2` FOREIGN KEY (`ma_danh_muc`) REFERENCES `danh_muc` (`ma_danh_muc`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
