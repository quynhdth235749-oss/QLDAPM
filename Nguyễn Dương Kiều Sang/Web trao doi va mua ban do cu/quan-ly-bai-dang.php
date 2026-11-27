<?php
session_start();
include 'ket-noi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách tin đăng do mình bán
$sql_ban = "SELECT sp.*, dm.ten_danh_muc, 
            (SELECT COUNT(*) FROM giao_dich WHERE ma_san_pham = sp.ma_san_pham) as tong_yeu_cau
            FROM san_pham sp 
            JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
            WHERE sp.ma_nguoi_dung = $user_id 
            ORDER BY sp.ngay_tao DESC";
$result_ban = $conn->query($sql_ban);

// Lấy danh sách giao dịch mà mình đi MUA / TRAO ĐỔI
$sql_mua = "SELECT gd.*, sp.ten_san_pham, sp.hinh_anh, sp.gia, nd.ho_ten as ten_nguoi_ban, nd.so_dien_thoai
            FROM giao_dich gd 
            JOIN san_pham sp ON gd.ma_san_pham = sp.ma_san_pham 
            JOIN nguoi_dung nd ON gd.ma_nguoi_ban = nd.ma_nguoi_dung 
            WHERE gd.ma_nguoi_mua = $user_id 
            ORDER BY gd.ngay_tao DESC";
$result_mua = $conn->query($sql_mua);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Bài Đăng & Giao Dịch - Chợ Đồ Cũ Long Xuyên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php">🔄 CHỢ ĐỒ CŨ</a>
        <div class="d-flex align-items-center text-white">
            <span class="me-3"><i class="bi bi-person-circle me-1"></i> <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></span>
            <a class="btn btn-outline-light btn-sm me-2" href="index.php"><i class="bi bi-house"></i> Trang chủ</a>
            <a class="btn btn-warning btn-sm text-dark fw-bold me-2" href="dang-bai.php"><i class="bi bi-plus-lg"></i> Đăng tin mới</a>
            <a class="btn btn-light btn-sm text-danger fw-bold" href="dang-xuat.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container flex-grow-1 py-4">
    <!-- Thanh Chuyển Tab -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded shadow-sm" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="ban-tab" data-bs-toggle="tab" data-bs-target="#ban-tab-pane" type="button" role="tab">
                📦 Bài đăng người bán của tôi (<?php echo $result_ban->num_rows; ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="mua-tab" data-bs-toggle="tab" data-bs-target="#mua-tab-pane" type="button" role="tab">
                🛒 Giao dịch / Yêu cầu mua của tôi (<?php echo $result_mua->num_rows; ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- TAB 1: BÀI ĐĂNG CỦA MÌNH (NGƯỜI BÁN) -->
        <div class="tab-pane fade show active" id="ban-tab-pane" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-0">
                    <?php if ($result_ban && $result_ban->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 80px;">Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Danh mục</th>
                                        <th>Giá / Hình thức</th>
                                        <th>Trạng thái</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $result_ban->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" class="rounded" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/60'">
                                            </td>
                                            <td>
                                                <a href="chi-tiet.php?id=<?php echo $row['ma_san_pham']; ?>" class="fw-bold text-decoration-none text-dark">
                                                    <?php echo htmlspecialchars($row['ten_san_pham']); ?>
                                                </a>
                                                <br><small class="text-muted">Ngày đăng: <?php echo date('d/m/Y', strtotime($row['ngay_tao'])); ?></small>
                                            </td>
                                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['ten_danh_muc']); ?></span></td>
                                            <td class="text-danger fw-bold">
                                                <?php echo $row['gia'] > 0 ? number_format($row['gia'], 0, ',', '.').'đ' : '<span class="badge bg-warning text-dark">Trao đổi đồ</span>'; ?>
                                            </td>
                                            <td>
                                                <?php if ($row['trang_thai'] === 'con_hang'): ?>
                                                    <span class="badge bg-success">Đang hiển thị</span>
                                                <?php elseif ($row['trang_thai'] === 'an'): ?>
                                                    <span class="badge bg-secondary">Đã ẩn</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Đã giao dịch/Đã bán</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="sua-bai.php?id=<?php echo $row['ma_san_pham']; ?>" class="btn btn-warning fw-bold">Sửa</a>
                                                    <a href="an-bai.php?id=<?php echo $row['ma_san_pham']; ?>" class="btn btn-secondary fw-bold"><?php echo $row['trang_thai'] === 'an' ? 'Hiện' : 'Ẩn'; ?></a>
                                                    <a href="xoa-bai.php?id=<?php echo $row['ma_san_pham']; ?>" class="btn btn-danger fw-bold" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">Bạn chưa đăng tin bán đồ cũ nào.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- TAB 2: ĐƠN HÀNG / GIAO DỊCH CỦA MÌNH (NGƯỜI MUA) -->
        <div class="tab-pane fade" id="mua-tab-pane" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-0">
                    <?php if ($result_mua && $result_mua->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 80px;">Ảnh</th>
                                        <th>Sản phẩm đã chọn</th>
                                        <th>Người bán</th>
                                        <th>Nhu cầu</th>
                                        <th>Trạng thái giao dịch</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($m = $result_mua->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($m['hinh_anh']); ?>" class="rounded" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/60'">
                                            </td>
                                            <td>
                                                <a href="chi-tiet.php?id=<?php echo $m['ma_san_pham']; ?>" class="fw-bold text-dark text-decoration-none">
                                                    <?php echo htmlspecialchars($m['ten_san_pham']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($m['ten_nguoi_ban']); ?></strong>
                                                <br><small class="text-muted">SĐT: <?php echo htmlspecialchars($m['so_dien_thoai']); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $m['loai_giao_dich'] === 'mua' ? 'primary' : 'warning text-dark'; ?>">
                                                    <?php echo strtoupper($m['loai_giao_dich']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($m['trang_thai'] === 'cho_xac_nhan'): ?>
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Chờ người bán xác nhận</span>
                                                <?php elseif ($m['trang_thai'] === 'hoan_thanh'): ?>
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Giao dịch thành công</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Đã hủy</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="chi-tiet.php?id=<?php echo $m['ma_san_pham']; ?>" class="btn btn-outline-success btn-sm fw-bold">
                                                    Xem bài đăng & Đánh giá
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">Bạn chưa gửi yêu cầu mua hoặc trao đổi sản phẩm nào.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto small text-muted">
    © 2026 Chợ Đồ Cũ Long Xuyên. Đường Ung Văn Khiêm, Phường Đông Xuyên, TP. Long Xuyên, An Giang.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>