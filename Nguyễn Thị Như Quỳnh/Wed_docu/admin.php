<?php
session_start();
include 'ket-noi.php';

// Bảo vệ trang Admin: Chỉ cho phép tài khoản có vai_tro = 'admin'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
    echo "<script>alert('Bạn không có quyền truy cập trang Quản trị!'); window.location.href='index.php';</script>";
    exit();
}

$thong_bao = '';
$loai_tb = 'success';

// ==========================================
// 1. XỬ LÝ HÀNH ĐỘNG CỦA ADMIN
// ==========================================

// 1.1 Duyệt / Ẩn / Xóa sản phẩm
if (isset($_GET['duyet_sp'])) {
    $ma_sp = intval($_GET['duyet_sp']);
    $conn->query("UPDATE san_pham SET trang_thai = 'con_hang' WHERE ma_san_pham = $ma_sp");
    $thong_bao = "Đã duyệt hiển thị bài đăng #$ma_sp!";
}
if (isset($_GET['an_sp'])) {
    $ma_sp = intval($_GET['an_sp']);
    $conn->query("UPDATE san_pham SET trang_thai = 'an' WHERE ma_san_pham = $ma_sp");
    $thong_bao = "Đã ẩn bài đăng #$ma_sp khỏi trang chủ!";
    $loai_tb = 'warning';
}
if (isset($_GET['xoa_sp'])) {
    $ma_sp = intval($_GET['xoa_sp']);
    $conn->query("DELETE FROM san_pham WHERE ma_san_pham = $ma_sp");
    $thong_bao = "Đã xóa vĩnh viễn bài đăng #$ma_sp!";
    $loai_tb = 'danger';
}

// 1.2 Xử lý Báo cáo vi phạm
if (isset($_GET['xu_ly_bc']) && isset($_GET['hanh_dong'])) {
    $ma_bc = intval($_GET['xu_ly_bc']);
    $hanh_dong = $_GET['hanh_dong'];
    $bc_info = $conn->query("SELECT ma_san_pham FROM bao_cao WHERE ma_bao_cao = $ma_bc")->fetch_assoc();

    if ($bc_info) {
        $ma_sp_bc = $bc_info['ma_san_pham'];
        if ($hanh_dong === 'an_bai') {
            $conn->query("UPDATE san_pham SET trang_thai = 'an' WHERE ma_san_pham = $ma_sp_bc");
            $conn->query("UPDATE bao_cao SET trang_thai = 'da_xu_ly' WHERE ma_san_pham = $ma_sp_bc");
            $thong_bao = "Đã ẩn bài đăng vi phạm #$ma_sp_bc và đóng các báo cáo liên quan!";
        } elseif ($hanh_dong === 'xoa_bai') {
            $conn->query("DELETE FROM san_pham WHERE ma_san_pham = $ma_sp_bc");
            $thong_bao = "Đã xóa vĩnh viễn bài đăng vi phạm #$ma_sp_bc!";
            $loai_tb = 'danger';
        } elseif ($hanh_dong === 'bo_qua') {
            $conn->query("UPDATE bao_cao SET trang_thai = 'bo_qua' WHERE ma_bao_cao = $ma_bc");
            $thong_bao = "Đã đánh dấu bỏ qua báo cáo #$ma_bc!";
            $loai_tb = 'info';
        }
    }
}

// 1.3 Khóa / Mở khóa người dùng
if (isset($_GET['khoa_user'])) {
    $uid = intval($_GET['khoa_user']);
    if ($uid != $_SESSION['user_id']) {
        $conn->query("UPDATE nguoi_dung SET trang_thai = 'bi_khoa' WHERE ma_nguoi_dung = $uid");
        $thong_bao = "Đã khóa tài khoản người dùng #$uid!";
        $loai_tb = 'danger';
    }
}
if (isset($_GET['mo_khoa_user'])) {
    $uid = intval($_GET['mo_khoa_user']);
    $conn->query("UPDATE nguoi_dung SET trang_thai = 'hoat_dong' WHERE ma_nguoi_dung = $uid");
    $thong_bao = "Đã mở khóa tài khoản người dùng #$uid!";
}

// 1.4 Thêm / Xóa từ khóa cấm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_tu_khoa'])) {
    $tu_khoa_moi = trim($_POST['tu_khoa_moi']);
    if (!empty($tu_khoa_moi)) {
        $stmt = $conn->prepare("INSERT IGNORE INTO tu_khoa_cam (tu_khoa) VALUES (?)");
        $stmt->bind_param("s", $tu_khoa_moi);
        $stmt->execute();
        $thong_bao = "Đã thêm từ khóa cấm: <strong>" . htmlspecialchars($tu_khoa_moi) . "</strong>";
    }
}
if (isset($_GET['xoa_tu_khoa'])) {
    $ma_tk = intval($_GET['xoa_tu_khoa']);
    $conn->query("DELETE FROM tu_khoa_cam WHERE ma_tu_khoa = $ma_tk");
    $thong_bao = "Đã xóa từ khóa khỏi danh sách cấm!";
    $loai_tb = 'warning';
}

// ==========================================
// 2. LẤY DỮ LIỆU THỐNG KÊ & DANH SÁCH
// ==========================================
$tong_nguoi_dung = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung")->fetch_assoc()['total'];
$tong_san_pham = $conn->query("SELECT COUNT(*) as total FROM san_pham")->fetch_assoc()['total'];
$tong_giao_dich = $conn->query("SELECT COUNT(*) as total FROM giao_dich")->fetch_assoc()['total'];
$tong_bao_cao_cho = $conn->query("SELECT COUNT(*) as total FROM bao_cao WHERE trang_thai = 'cho_xu_ly'")->fetch_assoc()['total'];

$ds_nguoi_dung = $conn->query("SELECT * FROM nguoi_dung ORDER BY ngay_tao DESC");

$ds_san_pham = $conn->query("SELECT sp.*, nd.ho_ten, dm.ten_danh_muc,
                             (SELECT COUNT(*) FROM bao_cao bc WHERE bc.ma_san_pham = sp.ma_san_pham AND bc.trang_thai = 'cho_xu_ly') as so_bao_cao
                             FROM san_pham sp 
                             JOIN nguoi_dung nd ON sp.ma_nguoi_dung = nd.ma_nguoi_dung 
                             JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                             ORDER BY so_bao_cao DESC, sp.ngay_tao DESC");

$ds_bao_cao = $conn->query("SELECT bc.*, sp.ten_san_pham, sp.hinh_anh, sp.trang_thai as trang_thai_sp, nd.ho_ten as nguoi_bao_cao 
                            FROM bao_cao bc
                            JOIN san_pham sp ON bc.ma_san_pham = sp.ma_san_pham
                            JOIN nguoi_dung nd ON bc.ma_nguoi_bao_cao = nd.ma_nguoi_dung
                            ORDER BY FIELD(bc.trang_thai, 'cho_xu_ly', 'da_xu_ly', 'bo_qua'), bc.ngay_bao_cao DESC");

$ds_tu_khoa = $conn->query("SELECT * FROM tu_khoa_cam ORDER BY ma_tu_khoa DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị Admin - Chợ Đồ Cũ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<!-- Navbar Admin -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-warning" href="admin.php">⚙️ ADMIN PANEL - CHỢ ĐỒ CŨ</a>
        <div class="d-flex align-items-center gap-2">
            <span class="text-light small me-2">Xin chào, <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></span>
            <a class="btn btn-outline-light btn-sm" href="index.php"><i class="bi bi-globe"></i> Xem Web Client</a>
            <a class="btn btn-danger btn-sm" href="dang-xuat.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container-fluid my-4">
    <h3 class="mb-3 fw-bold">Bảng Điều Khiển Quản Trị Hệ Thống</h3>

    <?php if (!empty($thong_bao)): ?>
        <div class="alert alert-<?php echo $loai_tb; ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i><?php echo $thong_bao; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- 4 Thẻ Thống Kê -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white p-3 shadow-sm border-0">
                <h6 class="text-uppercase opacity-75">Tổng Người Dùng</h6>
                <h2 class="fw-bold mb-0"><?php echo $tong_nguoi_dung; ?> thành viên</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white p-3 shadow-sm border-0">
                <h6 class="text-uppercase opacity-75">Tổng Tin Đăng</h6>
                <h2 class="fw-bold mb-0"><?php echo $tong_san_pham; ?> bài đăng</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark p-3 shadow-sm border-0">
                <h6 class="text-uppercase opacity-75">Tổng Giao Dịch</h6>
                <h2 class="fw-bold mb-0"><?php echo $tong_giao_dich; ?> giao dịch</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white p-3 shadow-sm border-0">
                <h6 class="text-uppercase opacity-75">Báo Cáo Chờ Xử Lý</h6>
                <h2 class="fw-bold mb-0"><?php echo $tong_bao_cao_cho; ?> báo cáo</h2>
            </div>
        </div>
    </div>

    <!-- Thanh Tab Điều Hướng -->
    <ul class="nav nav-tabs mb-4 fw-bold" id="adminTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active text-danger" data-bs-toggle="tab" data-bs-target="#tab-baocao" type="button">
                🚩 Báo Cáo Vi Phạm 
                <?php if($tong_bao_cao_cho > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?php echo $tong_bao_cao_cho; ?></span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link text-dark" data-bs-toggle="tab" data-bs-target="#tab-sanpham" type="button">
                📦 Duyệt & Quản Lý Bài Đăng (<?php echo $tong_san_pham; ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link text-dark" data-bs-toggle="tab" data-bs-target="#tab-nguoidung" type="button">
                👥 Quản Lý Người Dùng (<?php echo $tong_nguoi_dung; ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link text-dark" data-bs-toggle="tab" data-bs-target="#tab-tukhoa" type="button">
                🚫 Từ Khóa Bị Cấm (<?php echo $ds_tu_khoa->num_rows; ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- TAB 1: XỬ LÝ BÁO CÁO VI PHẠM -->
        <div class="tab-pane fade show active" id="tab-baocao">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white fw-bold">
                    🚩 DANH SÁCH BÀI ĐĂNG BỊ NGƯỜI DÙNG BÁO CÁO
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Sản Phẩm Bị Báo Cáo</th>
                                <th>Người Báo Cáo</th>
                                <th>Lý Do & Chi Tiết</th>
                                <th>Trạng Thái BC</th>
                                <th>Xử Lý Vi Phạm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($ds_bao_cao && $ds_bao_cao->num_rows > 0): ?>
                                <?php while($bc = $ds_bao_cao->fetch_assoc()): ?>
                                    <tr class="<?php echo $bc['trang_thai'] === 'cho_xu_ly' ? 'table-warning' : ''; ?>">
                                        <td>#<?php echo $bc['ma_bao_cao']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?php echo htmlspecialchars($bc['hinh_anh']); ?>" style="width: 45px; height: 45px; object-fit: cover;" class="rounded" onerror="this.src='https://via.placeholder.com/45'">
                                                <div>
                                                    <a href="chi-tiet.php?id=<?php echo $bc['ma_san_pham']; ?>" target="_blank" class="fw-bold text-decoration-none">
                                                        <?php echo htmlspecialchars($bc['ten_san_pham']); ?>
                                                    </a>
                                                    <br><small class="text-muted">TT bài: <?php echo $bc['trang_thai_sp']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($bc['nguoi_bao_cao']); ?><br><small class="text-muted"><?php echo $bc['ngay_bao_cao']; ?></small></td>
                                        <td>
                                            <span class="badge bg-danger"><?php echo htmlspecialchars($bc['ly_do']); ?></span>
                                            <?php if(!empty($bc['mo_ta_them'])): ?>
                                                <div class="small text-dark mt-1"><em>"<?php echo htmlspecialchars($bc['mo_ta_them']); ?>"</em></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($bc['trang_thai'] === 'cho_xu_ly'): ?>
                                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                            <?php elseif($bc['trang_thai'] === 'da_xu_ly'): ?>
                                                <span class="badge bg-success">Đã xử lý</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Đã bỏ qua</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($bc['trang_thai'] === 'cho_xu_ly'): ?>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="admin.php?xu_ly_bc=<?php echo $bc['ma_bao_cao']; ?>&hanh_dong=an_bai" class="btn btn-warning fw-bold" onclick="return confirm('Ẩn bài đăng này khỏi trang chủ?')">Ẩn bài</a>
                                                    <a href="admin.php?xu_ly_bc=<?php echo $bc['ma_bao_cao']; ?>&hanh_dong=xoa_bai" class="btn btn-danger fw-bold" onclick="return confirm('Xóa vĩnh viễn bài đăng này?')">Xóa bài</a>
                                                    <a href="admin.php?xu_ly_bc=<?php echo $bc['ma_bao_cao']; ?>&hanh_dong=bo_qua" class="btn btn-outline-secondary">Bỏ qua</a>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">Hoàn tất</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">Chưa có báo cáo vi phạm nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: QUẢN LÝ & DUYỆT TIN ĐĂNG -->
        <div class="tab-pane fade" id="tab-sanpham">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">
                    📦 QUẢN LÝ TIN ĐĂNG SẢN PHẨM & KIỂM DUYỆT VI PHẠM
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Hình Ảnh</th>
                                <th>Tên Sản Phẩm</th>
                                <th>Danh Mục</th>
                                <th>Giá Bán</th>
                                <th>Người Đăng</th>
                                <th>Trạng Thái & Cảnh Báo</th>
                                <th>Hành Động Duyệt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($sp = $ds_san_pham->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $sp['ma_san_pham']; ?></td>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($sp['hinh_anh']); ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded" onerror="this.src='https://via.placeholder.com/50'">
                                    </td>
                                    <td>
                                        <a href="chi-tiet.php?id=<?php echo $sp['ma_san_pham']; ?>" target="_blank" class="fw-bold text-dark text-decoration-none">
                                            <?php echo htmlspecialchars($sp['ten_san_pham']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($sp['ten_danh_muc']); ?></td>
                                    <td class="text-danger fw-bold"><?php echo $sp['gia'] > 0 ? number_format($sp['gia'], 0, ',', '.').'đ' : 'Trao đổi'; ?></td>
                                    <td><?php echo htmlspecialchars($sp['ho_ten']); ?></td>
                                    <td>
                                        <?php if ($sp['trang_thai'] === 'con_hang'): ?>
                                            <span class="badge bg-success">Đang hiển thị</span>
                                        <?php elseif ($sp['trang_thai'] === 'cho_duyet'): ?>
                                            <span class="badge bg-warning text-dark">⚠️ Chờ duyệt vi phạm</span>
                                        <?php elseif ($sp['trang_thai'] === 'an'): ?>
                                            <span class="badge bg-secondary">Đã ẩn</span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark"><?php echo $sp['trang_thai']; ?></span>
                                        <?php endif; ?>

                                        <?php if ($sp['so_bao_cao'] > 0): ?>
                                            <br><span class="badge bg-danger mt-1">🚩 <?php echo $sp['so_bao_cao']; ?> báo cáo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <?php if ($sp['trang_thai'] !== 'con_hang'): ?>
                                                <a href="admin.php?duyet_sp=<?php echo $sp['ma_san_pham']; ?>" class="btn btn-success fw-bold">✓ Duyệt hiện</a>
                                            <?php endif; ?>
                                            <?php if ($sp['trang_thai'] === 'con_hang'): ?>
                                                <a href="admin.php?an_sp=<?php echo $sp['ma_san_pham']; ?>" class="btn btn-warning fw-bold">Ẩn tin</a>
                                            <?php endif; ?>
                                            <a href="admin.php?xoa_sp=<?php echo $sp['ma_san_pham']; ?>" class="btn btn-danger fw-bold" onclick="return confirm('Xóa vĩnh viễn bài đăng này?')">Xóa</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: QUẢN LÝ NGƯỜI DÙNG -->
        <div class="tab-pane fade" id="tab-nguoidung">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">
                    👥 QUẢN LÝ TÀI KHOẢN NGƯỜI DÙNG
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Họ và Tên</th>
                                <th>Email</th>
                                <th>Số Điện Thoại</th>
                                <th>Vai Trò</th>
                                <th>Trạng Thái</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = $ds_nguoi_dung->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $user['ma_nguoi_dung']; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($user['ho_ten']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['so_dien_thoai']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['vai_tro'] === 'admin' ? 'danger' : 'info text-dark'; ?>">
                                            <?php echo strtoupper($user['vai_tro']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php $tt_user = isset($user['trang_thai']) ? $user['trang_thai'] : 'hoat_dong'; ?>
                                        <span class="badge bg-<?php echo $tt_user === 'bi_khoa' ? 'danger' : 'success'; ?>">
                                            <?php echo $tt_user === 'bi_khoa' ? 'Đã khóa' : 'Hoạt động'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['vai_tro'] !== 'admin'): ?>
                                            <?php if ($tt_user === 'bi_khoa'): ?>
                                                <a href="admin.php?mo_khoa_user=<?php echo $user['ma_nguoi_dung']; ?>" class="btn btn-success btn-sm fw-bold">Mở khóa</a>
                                            <?php else: ?>
                                                <a href="admin.php?khoa_user=<?php echo $user['ma_nguoi_dung']; ?>" class="btn btn-outline-danger btn-sm fw-bold" onclick="return confirm('Khóa tài khoản người dùng này?')">Khóa tài khoản</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">Quản trị viên</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 4: QUẢN LÝ TỪ KHÓA BỊ CẤM -->
        <div class="tab-pane fade" id="tab-tukhoa">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-danger text-white fw-bold">➕ Thêm Từ Khóa Cấm Mới</div>
                        <div class="card-body">
                            <form method="POST" action="admin.php">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nhập từ / cụm từ cấm đăng</label>
                                    <input type="text" name="tu_khoa_moi" class="form-control" placeholder="Ví dụ: hàng cấm, lừa đảo..." required>
                                    <small class="text-muted">Người dùng đăng bài chứa từ này sẽ bị hệ thống chặn ngay lập tức.</small>
                                </div>
                                <button type="submit" name="them_tu_khoa" class="btn btn-danger w-100 fw-bold">Thêm vào danh sách cấm</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white fw-bold">🚫 Danh Sách Từ Khóa Đang Bị Chặn</div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                <?php while($tk = $ds_tu_khoa->fetch_assoc()): ?>
                                    <span class="badge bg-light text-danger border border-danger fs-6 py-2 px-3 d-flex align-items-center gap-2">
                                        <?php echo htmlspecialchars($tk['tu_khoa']); ?>
                                        <a href="admin.php?xoa_tu_khoa=<?php echo $tk['ma_tu_khoa']; ?>" class="text-dark text-decoration-none fw-bold" title="Xóa từ khóa này" onclick="return confirm('Xóa từ khóa này khỏi danh sách cấm?')">×</a>
                                    </span>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>