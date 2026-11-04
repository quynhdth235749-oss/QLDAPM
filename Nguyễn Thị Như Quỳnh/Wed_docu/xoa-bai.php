<?php
session_start();
include 'ket-noi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$vai_tro = isset($_SESSION['vai_tro']) ? $_SESSION['vai_tro'] : 'user';

// Kiểm tra bài đăng
$sp_query = $conn->query("SELECT * FROM san_pham WHERE ma_san_pham = $id");
$sp = $sp_query->fetch_assoc();

if ($sp && ($sp['ma_nguoi_dung'] == $user_id || $vai_tro === 'admin')) {
    // Xóa sản phẩm (các bảng khóa ngoại liên quan như kiem_tra_chat_luong sẽ tự động xóa nếu có cascade)
    $conn->query("DELETE FROM kiem_tra_chat_luong WHERE ma_san_pham = $id");
    $conn->query("DELETE FROM giao_dich WHERE ma_san_pham = $id");
    $conn->query("DELETE FROM san_pham WHERE ma_san_pham = $id");
    
    echo "<script>alert('Xóa tin đăng thành công!'); window.location.href='index.php';</script>";
} else {
    echo "<script>alert('Bạn không có quyền xóa tin đăng này!'); window.location.href='index.php';</script>";
}
exit();
?>