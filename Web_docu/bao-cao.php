<?php
session_start();
include 'ket-noi.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để báo cáo bài đăng!'); window.location.href='dang-nhap.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_san_pham = intval($_POST['ma_san_pham']);
    $ma_nguoi_bao_cao = $_SESSION['user_id'];
    $ly_do = trim($_POST['ly_do']);
    $mo_ta_them = trim($_POST['mo_ta_them']);
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';

    // Kiểm tra xem người dùng đã báo cáo bài này và đang chờ xử lý chưa
    $check = $conn->prepare("SELECT ma_bao_cao FROM bao_cao WHERE ma_san_pham = ? AND ma_nguoi_bao_cao = ? AND trang_thai = 'cho_xu_ly'");
    $check->bind_param("ii", $ma_san_pham, $ma_nguoi_bao_cao);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        echo "<script>alert('Bạn đã gửi báo cáo cho bài đăng này rồi. Quản trị viên đang xem xét!'); window.location.href='$redirect';</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO bao_cao (ma_san_pham, ma_nguoi_bao_cao, ly_do, mo_ta_them) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $ma_san_pham, $ma_nguoi_bao_cao, $ly_do, $mo_ta_them);

    if ($stmt->execute()) {
        // Nếu bài đăng bị từ 3 người khác nhau báo cáo -> tự động chuyển sang trạng thái 'cho_duyet' (tạm ẩn chờ Admin duyệt)
        $count_res = $conn->query("SELECT COUNT(DISTINCT ma_nguoi_bao_cao) as total FROM bao_cao WHERE ma_san_pham = $ma_san_pham AND trang_thai = 'cho_xu_ly'")->fetch_assoc();
        if ($count_res['total'] >= 3) {
            $conn->query("UPDATE san_pham SET trang_thai = 'cho_duyet' WHERE ma_san_pham = $ma_san_pham");
        }

        echo "<script>alert('Cảm ơn bạn! Báo cáo vi phạm đã được gửi tới Quản trị viên.'); window.location.href='$redirect';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi gửi báo cáo!'); window.location.href='$redirect';</script>";
    }
    exit();
}
?>