<?php
session_start();
include 'ket-noi.php';

// Xử lý bộ lọc tìm kiếm và danh mục
$tu_khoa = isset($_GET['tu_khoa']) ? trim($_GET['tu_khoa']) : '';
$ma_danh_muc = isset($_GET['ma_danh_muc']) ? intval($_GET['ma_danh_muc']) : 0;

// Chỉ lấy các sản phẩm có trạng thái 'con_hang'
$sql = "SELECT sp.*, dm.ten_danh_muc, nd.dia_chi FROM san_pham sp 
        JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
        JOIN nguoi_dung nd ON sp.ma_nguoi_dung = nd.ma_nguoi_dung
        WHERE sp.trang_thai = 'con_hang'";

if (!empty($tu_khoa)) {
    $tu_khoa_safe = $conn->real_escape_string($tu_khoa);
    $sql .= " AND (sp.ten_san_pham LIKE '%$tu_khoa_safe%' OR sp.mo_ta LIKE '%$tu_khoa_safe%')";
}
if ($ma_danh_muc > 0) {
    $sql .= " AND sp.ma_danh_muc = '$ma_danh_muc'";
}
$sql .= " ORDER BY sp.ngay_tao DESC";

$result = $conn->query($sql);
$danh_muc_result = $conn->query("SELECT * FROM danh_muc");

// Gọi giao diện trang chủ
include("main.php");
?>