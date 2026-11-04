<?php
include 'ket-noi.php';

// Xử lý Xóa sản phẩm nếu có yêu cầu từ Admin
if (isset($_GET['xoa_sp'])) {
    $ma_sp = intval($_GET['xoa_sp']);
    $conn->query("DELETE FROM san_pham WHERE ma_san_pham = $ma_sp");
    header("Location: admin.php");
    exit();
}

// Lấy thống kê tổng quan
$tong_nguoi_dung = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung")->fetch_assoc()['total'];
$tong_san_pham = $conn->query("SELECT COUNT(*) as total FROM san_pham")->fetch_assoc()['total'];
$tong_giao_dich = $conn->query("SELECT COUNT(*) as total FROM giao_dich")->fetch_assoc()['total'];

// Danh sách người dùng
$ds_nguoi_dung = $conn->query("SELECT * FROM nguoi_dung ORDER BY ngay_tao DESC");

// Danh sách sản phẩm
$ds_san_pham = $conn->query("SELECT sp.*, nd.ho_ten, dm.ten_danh_muc FROM san_pham sp 
                             JOIN nguoi_dung nd ON sp.ma_nguoi_dung = nd.ma_nguoi_dung 
                             JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                             ORDER BY sp.ngay_tao DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị Admin - Chợ Đồ Cũ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar Admin -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-warning" href="admin.php">⚙️ ADMIN PANEL - CHỢ ĐỒ CŨ</a>
        <a class="btn btn-outline-light btn-sm" href="index.php">Xem Trang Web Client</a>
    </div>
</nav>

<div class="container-fluid my-4">
    <h3 class="mb-4">Bảng Thống Kê Tổng Quan (Phụ trách: Tường Vi)</h3>

    <!-- 3 Thẻ Thống Kê -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white p-3 shadow-sm">
                <h5>Tổng Số Người Dùng</h5>
                <h2><?php echo $tong_nguoi_dung; ?> thành viên</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white p-3 shadow-sm">
                <h5>Tổng Tin Đăng Sản Phẩm</h5>
                <h2><?php echo $tong_san_pham; ?> bài đăng</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark p-3 shadow-sm">
                <h5>Tổng Giao Dịch Đã Tạo</h5>
                <h2><?php echo $tong_giao_dich; ?> giao dịch</h2>
            </div>
        </div>
    </div>

    <!-- Bảng 1: Quản lý Người Dùng -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white fw-bold">
            👥 QUẢN LÝ TÀI KHOẢN NGƯỜI DÙNG
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Họ và Tên</th>
                        <th>Email</th>
                        <th>Số Điện Thoại</th>
                        <th>Vai Trò</th>
                        <th>Ngày Đăng Ký</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $ds_nguoi_dung->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['ma_nguoi_dung']; ?></td>
                            <td class="fw-bold"><?php echo $user['ho_ten']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['so_dien_thoai']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['vai_tro'] === 'admin' ? 'danger' : 'info'; ?>">
                                    <?php echo strtoupper($user['vai_tro']); ?>
                                </span>
                            </td>
                            <td><?php echo $user['ngay_tao']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bảng 2: Quản lý Sản Phẩm -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white fw-bold">
            📦 QUẢN LÝ TIN ĐĂNG SẢN PHẨM & DUYỆT BÀI
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Hình Ảnh</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Danh Mục</th>
                        <th>Giá Bán</th>
                        <th>Người Đăng</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($sp = $ds_san_pham->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $sp['ma_san_pham']; ?></td>
                            <td>
                                <img src="<?php echo $sp['hinh_anh']; ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded" onerror="this.src='https://via.placeholder.com/50'">
                            </td>
                            <td class="fw-bold"><?php echo $sp['ten_san_pham']; ?></td>
                            <td><?php echo $sp['ten_danh_muc']; ?></td>
                            <td class="text-danger fw-bold"><?php echo number_format($sp['gia'], 0, ',', '.'); ?>đ</td>
                            <td><?php echo $sp['ho_ten']; ?></td>
                            <td>
                                <span class="badge bg-success"><?php echo $sp['trang_thai']; ?></span>
                            </td>
                            <td>
                                <a href="admin.php?xoa_sp=<?php echo $sp['ma_san_pham']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">Xóa bài</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>