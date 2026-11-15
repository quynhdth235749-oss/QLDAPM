-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 15, 2026 lúc 10:08 AM
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
-- Cấu trúc bảng cho bảng `bao_cao`
--

CREATE TABLE `bao_cao` (
  `ma_bao_cao` int(11) NOT NULL,
  `ma_san_pham` int(11) NOT NULL,
  `ma_nguoi_bao_cao` int(11) NOT NULL,
  `ly_do` varchar(255) NOT NULL,
  `mo_ta_them` text DEFAULT NULL,
  `trang_thai` varchar(30) NOT NULL DEFAULT 'cho_xu_ly',
  `ngay_bao_cao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_gia`
--

INSERT INTO `danh_gia` (`ma_danh_gia`, `ma_giao_dich`, `ma_nguoi_danh_gia`, `ma_nguoi_nhan_danh_gia`, `diem_danh_gia`, `nhan_xet`, `ngay_tao`) VALUES
(1, 1, 2, 4, 5, 'Người bán thân thiện, giao bàn nhanh đúng như mô tả!', '2026-11-04 07:01:47'),
(2, 2, 5, 2, 4, 'Khá ổn', '2026-11-04 07:44:47'),
(3, 1, 2, 4, 4, 'Ổn', '2026-11-04 07:45:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE `danh_muc` (
  `ma_danh_muc` int(11) NOT NULL,
  `ten_danh_muc` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`ma_danh_muc`, `ten_danh_muc`, `mo_ta`) VALUES
(1, 'Đồ điện tử & Công nghệ', 'Điện thoại, laptop, máy tính bảng cũ...'),
(2, 'Thời trang & Phụ kiện', 'Quần áo, giày dép, túi xách đã qua sử dụng...'),
(3, 'Sách & Giáo trình', 'Sách tham khảo, giáo trình đại học, truyện tranh...'),
(4, 'Đồ gia dụng & Nội thất', 'Bàn ghế học sinh, quạt máy, tủ đồ...');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `giao_dich`
--

INSERT INTO `giao_dich` (`ma_giao_dich`, `ma_san_pham`, `ma_nguoi_ban`, `ma_nguoi_mua`, `loai_giao_dich`, `trang_thai`, `ngay_tao`) VALUES
(1, 3, 4, 2, 'mua', 'hoan_thanh', '2026-11-04 07:01:47'),
(2, 9, 2, 5, 'trao_doi', 'hoan_thanh', '2026-11-04 07:30:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kiem_tra_chat_luong`
--

CREATE TABLE `kiem_tra_chat_luong` (
  `ma_kiem_tra` int(11) NOT NULL,
  `ma_san_pham` int(11) NOT NULL,
  `tieu_chi` varchar(255) NOT NULL,
  `dat_yeu_cau` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kiem_tra_chat_luong`
--

INSERT INTO `kiem_tra_chat_luong` (`ma_kiem_tra`, `ma_san_pham`, `tieu_chi`, `dat_yeu_cau`) VALUES
(1, 1, 'Màn hình không trầy xước nặng', 1),
(2, 1, 'Dung lượng pin trên 80%', 1),
(3, 1, 'Chưa từng qua sửa chữa (Nguyên zin)', 1),
(4, 2, 'Không mất trang, rách trang', 1),
(5, 3, 'Chân bàn không bị rỉ sét', 1),
(6, 5, 'Pin dùng liên tục > 20 giờ', 1),
(7, 6, 'Đế giày nguyên vẹn, không bục keo', 1);

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
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp(),
  `trang_thai` varchar(20) NOT NULL DEFAULT 'hoat_dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`ma_nguoi_dung`, `ho_ten`, `email`, `mat_khau`, `so_dien_thoai`, `dia_chi`, `vai_tro`, `ngay_tao`, `trang_thai`) VALUES
(1, 'Quản Trị Viên (Admin)', 'admin@weddocu.com', '123456', '0901234567', 'Phường Đông Xuyên, TP. Long Xuyên, An Giang', 'admin', '2026-11-04 07:01:47', 'hoat_dong'),
(2, 'Nguyễn Văn Hùng', 'hungnv@gmail.com', '123456', '0912345678', 'Phường Mỹ Xuyên, TP. Long Xuyên, An Giang', 'user', '2026-11-04 07:01:47', 'hoat_dong'),
(3, 'Trần Thị Mai', 'maitt@gmail.com', '123456', '0923456789', 'Phường Mỹ Long, TP. Long Xuyên, An Giang', 'user', '2026-11-04 07:01:47', 'hoat_dong'),
(4, 'Lê Hoàng Nam', 'namlh@gmail.com', '123456', '0934567890', 'Phường Bình Khánh, TP. Long Xuyên, An Giang', 'user', '2026-11-04 07:01:47', 'hoat_dong'),
(5, 'Phạm Minh Tuấn', 'tuanpm@gmail.com', '123456', '0945678901', 'Phường Mỹ Phước, TP. Long Xuyên, An Giang', 'user', '2026-11-04 07:01:47', 'hoat_dong'),
(6, 'Trần Hạnh', 'hanh@gmail.com', '123', '09852617281', 'Phường Bình Đức, TP. Long Xuyên, An Giang', 'user', '2026-11-15 09:00:34', 'hoat_dong'),
(7, 'Lý Hai', 'hai@gmail.com', '123', '03827428374', 'Phường Mỹ Phước, TP. Long Xuyên, An Giang', 'user', '2026-11-15 09:02:12', 'hoat_dong');

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
  `trang_thai` enum('con_hang','da_dat','da_ban','an') DEFAULT 'con_hang',
  `ngay_tao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`ma_san_pham`, `ma_nguoi_dung`, `ma_danh_muc`, `ten_san_pham`, `mo_ta`, `hinh_anh`, `gia`, `tinh_trang`, `trang_thai`, `ngay_tao`) VALUES
(1, 2, 1, 'iPhone 11 64GB VN/A cũ', 'Máy xài kỹ mới 95%, nguyên zin chưa qua sửa chữa', 'images/h1.webp', 5500000.00, 'Mới 95%', 'con_hang', '2026-11-04 07:01:47'),
(2, 3, 3, 'Giáo trình Quản lý Dự án Phần mềm', 'Sách còn mới 90%, không rách nát hay gạch xóa', 'images/h2.webp', 45000.00, 'Mới 90%', 'con_hang', '2026-11-04 07:01:47'),
(3, 4, 4, 'Bàn học gấp gọn sinh viên', 'Bàn gỗ ép chân thép chắc chắn, dọn phòng thừa nên bán', 'images/h3.webp', 80000.00, 'Đã qua sử dụng', 'con_hang', '2026-11-04 07:01:47'),
(4, 2, 2, 'Áo khoác Jean Unisex', 'Áo size L rộng rãi, mặc đúng 2 lần còn rất mới', 'images/h4.webp', 120000.00, 'Mới 99%', 'con_hang', '2026-11-04 07:01:47'),
(5, 5, 1, 'Tai nghe Bluetooth Sony WH-1000XM4', 'Tai nghe chống ồn tốt, hoạt động bình thường, kèm hộp', 'images/h5.webp', 3200000.00, 'Mới 90%', 'con_hang', '2026-11-04 07:01:47'),
(6, 4, 2, 'Giày Sneaker Nike Air Force 1 Cũ', 'Size 41, đế chưa mòn nhiều, đã vệ sinh sạch sẽ', 'images/h6.webp', 650000.00, 'Mới 85%', 'con_hang', '2026-11-04 07:01:47'),
(7, 5, 4, 'Quạt đứng Senko có điều khiển', 'Quạt chạy êm, gió mạnh, chuyển nhà không mang theo được', 'images/h7.webp', 150000.00, 'Đã qua sử dụng', 'con_hang', '2026-11-04 07:01:47'),
(8, 3, 3, 'Bộ sách Đắc Nhân Tâm & Quẳng Gánh Lo Đi', 'Combo 2 cuốn sách kỹ năng sống còn rất mới', 'images/h8.webp', 70000.00, 'Mới 98%', 'con_hang', '2026-11-04 07:01:47'),
(9, 2, 2, 'Áo sơ mi kẻ sọc caro', 'Đã mặc qua 2 lần và còn rất mới. Áo size L. Phong cách trẻ trung.', 'images/1793777296_809.webp', 90000.00, '0', 'con_hang', '2026-11-04 07:16:09'),
(10, 2, 2, 'Vòng tay titan', '[trao_doi] Vòng tay titan cỏ 4 lá may mắn. \r\nCòn rất mới, không bị rỉ theo thời gian.', 'images/1793777932_101.webp', 0.00, '0', 'con_hang', '2026-11-04 07:38:28'),
(11, 5, 2, 'Áo cưới', '[ban] Áo cưới thiết kế riêng chỉ mặc 1 lần khi cưới.\r\nNên còn rất mới nheee.', 'images/1793779067_318.webp', 3000000.00, '0', 'con_hang', '2026-11-04 07:57:24'),
(12, 2, 2, 'Áo phao giữ nhiệt', '[ban] Hầu như chưa sử dụng', 'images/1794732468_362.jpg', 150000.00, '0', 'con_hang', '2026-11-15 08:47:29'),
(15, 7, 2, 'Nón bảo hiểm', '[ban] Không có nhu cầu nữa. Còn rất mới', 'images/6af97536923a2.webp', 145000.00, 'Đã qua sử dụng', 'con_hang', '2026-11-15 09:02:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tu_khoa_cam`
--

CREATE TABLE `tu_khoa_cam` (
  `ma_tu_khoa` int(11) NOT NULL,
  `tu_khoa` varchar(100) NOT NULL,
  `ngay_them` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tu_khoa_cam`
--

INSERT INTO `tu_khoa_cam` (`ma_tu_khoa`, `tu_khoa`, `ngay_them`) VALUES
(1, 'ma túy', '2026-11-15 08:39:47'),
(2, 'vũ khí', '2026-11-15 08:39:47'),
(3, 'súng', '2026-11-15 08:39:47'),
(4, 'dao găm', '2026-11-15 08:39:47'),
(5, 'cờ bạc', '2026-11-15 08:39:47'),
(6, 'cá độ', '2026-11-15 08:39:47'),
(7, 'lừa đảo', '2026-11-15 08:39:47'),
(8, 'hàng giả', '2026-11-15 08:39:47'),
(9, 'hàng nhái', '2026-11-15 08:39:47'),
(10, 'kích dục', '2026-11-15 08:39:47');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bao_cao`
--
ALTER TABLE `bao_cao`
  ADD PRIMARY KEY (`ma_bao_cao`),
  ADD KEY `ma_san_pham` (`ma_san_pham`),
  ADD KEY `ma_nguoi_bao_cao` (`ma_nguoi_bao_cao`);

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
  ADD KEY `ma_nguoi_mua` (`ma_nguoi_mua`);

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
-- Chỉ mục cho bảng `tu_khoa_cam`
--
ALTER TABLE `tu_khoa_cam`
  ADD PRIMARY KEY (`ma_tu_khoa`),
  ADD UNIQUE KEY `tu_khoa` (`tu_khoa`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bao_cao`
--
ALTER TABLE `bao_cao`
  MODIFY `ma_bao_cao` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `ma_giao_dich` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `kiem_tra_chat_luong`
--
ALTER TABLE `kiem_tra_chat_luong`
  MODIFY `ma_kiem_tra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `ma_nguoi_dung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  MODIFY `ma_san_pham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `tu_khoa_cam`
--
ALTER TABLE `tu_khoa_cam`
  MODIFY `ma_tu_khoa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bao_cao`
--
ALTER TABLE `bao_cao`
  ADD CONSTRAINT `bao_cao_ibfk_1` FOREIGN KEY (`ma_san_pham`) REFERENCES `san_pham` (`ma_san_pham`) ON DELETE CASCADE,
  ADD CONSTRAINT `bao_cao_ibfk_2` FOREIGN KEY (`ma_nguoi_bao_cao`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`) ON DELETE CASCADE;

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
