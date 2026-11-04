<?php
include 'ket-noi.php';

$thong_bao = '';
$loai_thong_bao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = trim($_POST['ho_ten']);
    $email = trim($_POST['email']);
    $mat_khau = trim($_POST['mat_khau']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $dia_chi = trim($_POST['dia_chi']);

    if (empty($ho_ten) || empty($email) || empty($mat_khau)) {
        $thong_bao = 'Vui lòng điền đầy đủ các thông tin bắt buộc!';
        $loai_thong_bao = 'danger';
    } else {
        // Kiểm tra email trùng lặp
        $check_stmt = $conn->prepare("SELECT ma_nguoi_dung FROM nguoi_dung WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $thong_bao = 'Email này đã được đăng ký trên hệ thống!';
            $loai_thong_bao = 'danger';
        } else {
            // Thêm người dùng mới
            $stmt = $conn->prepare("INSERT INTO nguoi_dung (ho_ten, email, mat_khau, so_dien_thoai, dia_chi, vai_tro) VALUES (?, ?, ?, ?, ?, 'user')");
            $stmt->bind_param("sssss", $ho_ten, $email, $mat_khau, $so_dien_thoai, $dia_chi);

            if ($stmt->execute()) {
                $thong_bao = 'Đăng ký tài khoản thành công! Bạn có thể đăng nhập ngay.';
                $loai_thong_bao = 'success';
            } else {
                $thong_bao = 'Có lỗi xảy ra khi tạo tài khoản!';
                $loai_thong_bao = 'danger';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản - Chợ Đồ Cũ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🔄 CHỢ ĐỒ CŨ</a>
        <a class="btn btn-outline-light btn-sm" href="index.php">Quay lại Trang Chủ</a>
    </div>
</nav>

<div class="container" style="max-width: 550px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white text-center py-3">
            <h4 class="mb-0 fw-bold">TẠO TÀI KHOẢN MỚI</h4>
            <small>Module Quản lý tài khoản người dùng</small>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($thong_bao)): ?>
                <div class="alert alert-<?php echo $loai_thong_bao; ?> alert-dismissible fade show" role="alert">
                    <?php echo $thong_bao; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="dang-ky.php">
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="ho_ten" class="form-control" placeholder="Nhập họ và tên đầy đủ" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" name="mat_khau" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="so_dien_thoai" class="form-control" placeholder="Ví dụ: 0912345678">
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ giao hàng / Giao dịch</label>
                    <textarea name="dia_chi" class="form-control" rows="2" placeholder="Quận/Huyện, Tỉnh/TP"></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold">ĐĂNG KÝ TÀI KHOẢN</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>