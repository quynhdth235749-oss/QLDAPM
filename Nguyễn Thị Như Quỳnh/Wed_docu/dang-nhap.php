<?php
session_start();
include 'ket-noi.php';

$thong_bao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mat_khau = trim($_POST['mat_khau']);

    if (empty($email) || empty($mat_khau)) {
        $thong_bao = 'Vui lòng nhập đầy đủ Email và Mật khẩu!';
    } else {
        // Kiểm tra thông tin tài khoản
        $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE email = ? AND mat_khau = ?");
        $stmt->bind_param("ss", $email, $mat_khau);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Lưu thông tin vào Session
            $_SESSION['user_id'] = $user['ma_nguoi_dung'];
            $_SESSION['user'] = $user['ho_ten'];
            $_SESSION['vai_tro'] = $user['vai_tro'];

            // Nếu là Admin thì chuyển hướng tới trang Admin, ngược lại về Trang chủ
            if ($user['vai_tro'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $thong_bao = 'Email hoặc mật khẩu không chính xác!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Chợ Đồ Cũ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-dark bg-success mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php">🔄 CHỢ ĐỒ CŨ</a>
        <a class="btn btn-outline-light btn-sm" href="index.php"><i class="bi bi-arrow-left"></i> Quay lại Trang chủ</a>
    </div>
</nav>

<div class="container my-auto" style="max-width: 450px;">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-success text-white text-center py-3">
            <h4 class="mb-0 fw-bold">ĐĂNG NHẬP HỆ THỐNG</h4>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($thong_bao)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $thong_bao; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="dang-nhap.php">
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="hungnv@gmail.com" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Mật khẩu</label>
                    <input type="password" name="mat_khau" class="form-control" placeholder="Nhập mật khẩu (Mặc định: 123456)" required>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold mb-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> ĐĂNG NHẬP
                </button>
            </form>

            <div class="text-center small text-muted">
                Chưa có tài khoản? <a href="dang-ky.php" class="text-success fw-bold">Đăng ký ngay</a>
            </div>
            
            <hr>           
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto small text-muted">
    © 2026 Chợ Đồ Cũ. Tất cả các quyền được bảo lưu.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>