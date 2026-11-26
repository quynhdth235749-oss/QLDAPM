<?php
session_start();
include 'ket-noi.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Tính điểm trung bình & tổng số đánh giá nhận được
$stat_query = $conn->query("SELECT COUNT(*) as tong_danh_gia, AVG(diem_danh_gia) as diem_tb 
                            FROM danh_gia 
                            WHERE ma_nguoi_nhan_danh_gia = $user_id");
$stat = $stat_query->fetch_assoc();
$tong_dg = $stat['tong_danh_gia'];
$diem_tb = round($stat['diem_tb'], 1);

// Lấy danh sách tất cả nhận xét mà người khác đánh giá mình
$sql = "SELECT dg.*, nd.ho_ten as ten_nguoi_danh_gia, sp.ten_san_pham, sp.ma_san_pham
        FROM danh_gia dg 
        JOIN nguoi_dung nd ON dg.ma_nguoi_danh_gia = nd.ma_nguoi_dung 
        JOIN giao_dich gd ON dg.ma_giao_dich = gd.ma_giao_dich
        JOIN san_pham sp ON gd.ma_san_pham = sp.ma_san_pham
        WHERE dg.ma_nguoi_nhan_danh_gia = $user_id 
        ORDER BY dg.ngay_tao DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh Giá Nhận Được - Chợ Đồ Cũ Long Xuyên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<!-- Header Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php">🔄 CHỢ ĐỒ CŨ</a>
        <div class="d-flex align-items-center text-white">
            <span class="me-3"><i class="bi bi-person-circle me-1"></i> <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></span>
            <a class="btn btn-outline-light btn-sm me-2" href="index.php"><i class="bi bi-house"></i> Trang chủ</a>
            <a class="btn btn-light btn-sm text-danger fw-bold" href="dang-xuat.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container flex-grow-1 py-4" style="max-width: 900px;">
    <!-- Thanh điều hướng tab -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0"><i class="bi bi-star-fill text-warning me-2"></i>Đánh Giá Từ Người Dùng Khác</h3>
        <div>
            <a href="quan-ly-bai-dang.php" class="btn btn-outline-success fw-bold me-2"><i class="bi bi-list-stars"></i> Bài đăng của tôi</a>
            <a href="xem-danh-gia.php" class="btn btn-primary fw-bold"><i class="bi bi-star-fill text-warning"></i> Đánh giá nhận được</a>
        </div>
    </div>

    <!-- Thẻ thống kê tổng quan -->
    <div class="card shadow-sm border-0 rounded-3 mb-4 bg-white">
        <div class="card-body p-4 text-center">
            <div class="row align-items-center">
                <div class="col-md-6 border-end">
                    <h1 class="display-3 fw-bold text-warning mb-0"><?php echo $diem_tb > 0 ? $diem_tb : '0.0'; ?></h1>
                    <div class="fs-4 text-warning mb-1">
                        <?php 
                            $stars = round($diem_tb);
                            for($i=1; $i<=5; $i++) {
                                echo $i <= $stars ? '★' : '☆';
                            }
                        ?>
                    </div>
                    <p class="text-muted mb-0">Điểm uy tín trung bình</p>
                </div>
                <div class="col-md-6">
                    <h2 class="fw-bold text-dark mb-1"><?php echo $tong_dg; ?></h2>
                    <p class="text-muted mb-0">Lượt đánh giá từ người mua & trao đổi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách nhận xét -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white fw-bold py-3">
            <i class="bi bi-chat-left-text-fill text-primary me-2"></i> NHẬT KÝ ĐÁNH GIÁ VÀ NHẬN XÉT
        </div>
        <div class="card-body p-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($dg = $result->fetch_assoc()): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong class="text-primary fs-6"><i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($dg['ten_nguoi_danh_gia']); ?></strong>
                                <span class="text-muted small ms-2">đã đánh giá khi mua sản phẩm:</span>
                                <a href="chi-tiet.php?id=<?php echo $dg['ma_san_pham']; ?>" class="text-decoration-none fw-bold text-dark small ms-1">
                                    "<?php echo htmlspecialchars($dg['ten_san_pham']); ?>"
                                </a>
                            </div>
                            <span class="text-warning fs-5">
                                <?php for($i=0; $i<$dg['diem_danh_gia']; $i++) echo '★'; ?>
                            </span>
                        </div>
                        <p class="card-text text-dark mb-1 bg-light p-3 rounded"><?php echo htmlspecialchars($dg['nhan_xet']); ?></p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>Ngày đánh giá: <?php echo date('d/m/Y H:i', strtotime($dg['ngay_tao'])); ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-chat-square-quote fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-0">Bạn chưa nhận được đánh giá nào từ người mua.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto small text-muted">
    © 2026 Chợ Đồ Cũ Long Xuyên. Đường Ung Văn Khiêm, Phường Đông Xuyên, TP. Long Xuyên, An Giang.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>