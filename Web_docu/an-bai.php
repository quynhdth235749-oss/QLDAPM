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

$sp_query = $conn->query("SELECT * FROM san_pham WHERE ma_san_pham = $id");
$sp = $sp_query->fetch_assoc();

if ($sp && ($sp['ma_nguoi_dung'] == $user_id || $vai_tro === 'admin')) {
    // Đảo trạng thái giữa 'an' và 'con_hang'
    $trang_thai_moi = ($sp['trang_thai'] === 'an') ? 'con_hang' : 'an';
    $conn->query("UPDATE san_pham SET trang_thai = '$trang_thai_moi' WHERE ma_san_pham = $id");
    
    $thong_bao = ($trang_thai_moi === 'an') ? 'Đã ẩn tin đăng khỏi trang chủ!' : 'Đã hiện tin đăng lên trang chủ!';
    echo "<script>alert('$thong_bao'); window.location.href='chi-tiet.php?id=$id';</script>";
} else {
    echo "<script>alert('Bạn không có quyền ẩn tin đăng này!'); window.location.href='index.php';</script>";
}
exit();
?>