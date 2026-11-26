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

// 1.3 Quản lý Người dùng & Nhân viên (Thêm, Sửa, Phân quyền, Khóa, Xóa)
if (isset($_GET['khoa_user'])) {
    $uid = intval($_GET['khoa_user']);
    if ($uid != $_SESSION['user_id']) {
        $conn->query("UPDATE nguoi_dung SET trang_thai = 'bi_khoa' WHERE ma_nguoi_dung = $uid");
        $thong_bao = "Đã khóa tài khoản #$uid!";
        $loai_tb = 'danger';
    }
}
if (isset($_GET['mo_khoa_user'])) {
    $uid = intval($_GET['mo_khoa_user']);
    $conn->query("UPDATE nguoi_dung SET trang_thai = 'hoat_dong' WHERE ma_nguoi_dung = $uid");
    $thong_bao = "Đã mở khóa tài khoản #$uid!";
}
if (isset($_GET['xoa_user'])) {
    $uid = intval($_GET['xoa_user']);
    if ($uid != $_SESSION['user_id']) {
        $conn->query("DELETE FROM nguoi_dung WHERE ma_nguoi_dung = $uid");
        $thong_bao = "Đã xóa vĩnh viễn tài khoản #$uid!";
        $loai_tb = 'danger';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_nguoi_dung'])) {
    $ho_ten = trim($_POST['ho_ten']);
    $email = trim($_POST['email']);
    $mat_khau = trim($_POST['mat_khau']);
    $sdt = trim($_POST['so_dien_thoai']);
    $dia_chi = trim($_POST['dia_chi']);
    $vai_tro = $_POST['vai_tro'];

    $check = $conn->prepare("SELECT ma_nguoi_dung FROM nguoi_dung WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $thong_bao = "Email <strong>" . htmlspecialchars($email) . "</strong> đã tồn tại trong hệ thống!";
        $loai_tb = 'danger';
    } else {
        $stmt = $conn->prepare("INSERT INTO nguoi_dung (ho_ten, email, mat_khau, so_dien_thoai, dia_chi, vai_tro, trang_thai) VALUES (?, ?, ?, ?, ?, ?, 'hoat_dong')");
        $stmt->bind_param("ssssss", $ho_ten, $email, $mat_khau, $sdt, $dia_chi, $vai_tro);
        $stmt->execute();
        $thong_bao = "Đã tạo tài khoản mới cho <strong>" . htmlspecialchars($ho_ten) . "</strong>!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sua_nguoi_dung'])) {
    $uid = intval($_POST['ma_nguoi_dung']);
    $ho_ten = trim($_POST['ho_ten']);
    $sdt = trim($_POST['so_dien_thoai']);
    $dia_chi = trim($_POST['dia_chi']);
    $vai_tro = $_POST['vai_tro'];
    $mat_khau_moi = trim($_POST['mat_khau_moi']);

    if (!empty($mat_khau_moi)) {
        $stmt = $conn->prepare("UPDATE nguoi_dung SET ho_ten=?, so_dien_thoai=?, dia_chi=?, vai_tro=?, mat_khau=? WHERE ma_nguoi_dung=?");
        $stmt->bind_param("sssssi", $ho_ten, $sdt, $dia_chi, $vai_tro, $mat_khau_moi, $uid);
    } else {
        $stmt = $conn->prepare("UPDATE nguoi_dung SET ho_ten=?, so_dien_thoai=?, dia_chi=?, vai_tro=? WHERE ma_nguoi_dung=?");
        $stmt->bind_param("ssssi", $ho_ten, $sdt, $dia_chi, $vai_tro, $uid);
    }
    $stmt->execute();
    $thong_bao = "Đã cập nhật thông tin & phân quyền cho tài khoản #$uid!";
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

// 1.5 Hỗ trợ / Cập nhật trạng thái giao dịch
if (isset($_GET['huy_gd'])) {
    $ma_gd = intval($_GET['huy_gd']);
    $gd_info = $conn->query("SELECT ma_san_pham FROM giao_dich WHERE ma_giao_dich = $ma_gd")->fetch_assoc();
    if ($gd_info) {
        $conn->query("UPDATE giao_dich SET trang_thai = 'da_huy' WHERE ma_giao_dich = $ma_gd");
        $conn->query("UPDATE san_pham SET trang_thai = 'con_hang' WHERE ma_san_pham = " . intval($gd_info['ma_san_pham']));
        $thong_bao = "Đã hủy giao dịch #$ma_gd và mở bán lại sản phẩm!";
        $loai_tb = 'warning';
    }
}

// 1.6 Quản lý Danh Mục Sản Phẩm (Thêm, Sửa, Xóa)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_danh_muc'])) {
    $ten_dm = trim($_POST['ten_danh_muc']);
    $mo_ta_dm = trim($_POST['mo_ta_dm'] ?? '');
    if (!empty($ten_dm)) {
        $stmt = $conn->prepare("INSERT INTO danh_muc (ten_danh_muc, mo_ta) VALUES (?, ?)");
        $stmt->bind_param("ss", $ten_dm, $mo_ta_dm);
        $stmt->execute();
        $thong_bao = "Đã thêm danh mục mới: <strong>" . htmlspecialchars($ten_dm) . "</strong>!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sua_danh_muc'])) {
    $ma_dm = intval($_POST['ma_danh_muc']);
    $ten_dm = trim($_POST['ten_danh_muc']);
    $mo_ta_dm = trim($_POST['mo_ta_dm'] ?? '');
    if (!empty($ten_dm)) {
        $stmt = $conn->prepare("UPDATE danh_muc SET ten_danh_muc = ?, mo_ta = ? WHERE ma_danh_muc = ?");
        $stmt->bind_param("ssi", $ten_dm, $mo_ta_dm, $ma_dm);
        $stmt->execute();
        $thong_bao = "Đã cập nhật danh mục #$ma_dm!";
    }
}

if (isset($_GET['xoa_dm'])) {
    $ma_dm = intval($_GET['xoa_dm']);
    $check_sp = $conn->query("SELECT COUNT(*) as total FROM san_pham WHERE ma_danh_muc = $ma_dm")->fetch_assoc()['total'];
    if ($check_sp > 0) {
        $thong_bao = "Không thể xóa danh mục #$ma_dm vì đang có <strong>$check_sp sản phẩm</strong> thuộc danh mục này!";
        $loai_tb = 'danger';
    } else {
        $conn->query("DELETE FROM danh_muc WHERE ma_danh_muc = $ma_dm");
        $thong_bao = "Đã xóa danh mục #$ma_dm!";
        $loai_tb = 'warning';
    }
}

// 1.7 Quản lý Smart Checklist Mẫu theo Danh Mục (Thêm, Sửa, Xóa)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_tieu_chi'])) {
    $ma_dm_tc = intval($_POST['ma_danh_muc']);
    $ten_tc = trim($_POST['ten_tieu_chi']);
    $bat_buoc = isset($_POST['bat_buoc']) ? intval($_POST['bat_buoc']) : 0;

    if ($ma_dm_tc > 0 && !empty($ten_tc)) {
        $stmt = $conn->prepare("INSERT INTO tieu_chi_mau (ma_danh_muc, ten_tieu_chi, bat_buoc) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $ma_dm_tc, $ten_tc, $bat_buoc);
        $stmt->execute();
        $thong_bao = "Đã thêm tiêu chí Smart Checklist: <strong>" . htmlspecialchars($ten_tc) . "</strong>!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sua_tieu_chi'])) {
    $ma_tc = intval($_POST['ma_tieu_chi']);
    $ma_dm_tc = intval($_POST['ma_danh_muc']);
    $ten_tc = trim($_POST['ten_tieu_chi']);
    $bat_buoc = intval($_POST['bat_buoc']);

    if (!empty($ten_tc)) {
        $stmt = $conn->prepare("UPDATE tieu_chi_mau SET ma_danh_muc = ?, ten_tieu_chi = ?, bat_buoc = ? WHERE ma_tieu_chi = ?");
        $stmt->bind_param("isii", $ma_dm_tc, $ten_tc, $bat_buoc, $ma_tc);
        $stmt->execute();
        $thong_bao = "Đã cập nhật tiêu chí #$ma_tc!";
    }
}

if (isset($_GET['xoa_tc'])) {
    $ma_tc = intval($_GET['xoa_tc']);
    $conn->query("DELETE FROM tieu_chi_mau WHERE ma_tieu_chi = $ma_tc");
    $thong_bao = "Đã xóa tiêu chí #$ma_tc khỏi danh sách Smart Checklist!";
    $loai_tb = 'warning';
}

// ==========================================
// 2. LẤY DỮ LIỆU THỐNG KÊ & DANH SÁCH
// ==========================================
$tong_nguoi_dung = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung")->fetch_assoc()['total'];
$tong_san_pham = $conn->query("SELECT COUNT(*) as total FROM san_pham")->fetch_assoc()['total'];
$tong_giao_dich = $conn->query("SELECT COUNT(*) as total FROM giao_dich")->fetch_assoc()['total'];
$tong_bao_cao_cho = $conn->query("SELECT COUNT(*) as total FROM bao_cao WHERE trang_thai = 'cho_xu_ly'")->fetch_assoc()['total'];

$ds_nguoi_dung = $conn->query("SELECT * FROM nguoi_dung ORDER BY FIELD(vai_tro, 'admin', 'staff', 'user'), ngay_tao DESC");

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

$ds_giao_dich = $conn->query("SELECT gd.*, sp.ten_san_pham, sp.hinh_anh, sp.gia,
                              nb.ho_ten as ten_nguoi_ban, nb.so_dien_thoai as sdt_ban,
                              nm.ho_ten as ten_nguoi_mua, nm.so_dien_thoai as sdt_mua
                              FROM giao_dich gd
                              JOIN san_pham sp ON gd.ma_san_pham = sp.ma_san_pham
                              JOIN nguoi_dung nb ON gd.ma_nguoi_ban = nb.ma_nguoi_dung
                              JOIN nguoi_dung nm ON gd.ma_nguoi_mua = nm.ma_nguoi_dung
                              ORDER BY gd.ngay_tao DESC");

$ds_danh_muc = $conn->query("SELECT dm.*, (SELECT COUNT(*) FROM san_pham sp WHERE sp.ma_danh_muc = dm.ma_danh_muc) as so_san_pham 
                             FROM danh_muc dm ORDER BY dm.ma_danh_muc ASC");

// Lưu danh mục vào mảng để dùng lại nhiều lần trong các form/modal
$arr_danh_muc = [];
if ($ds_danh_muc) {
    while ($row_dm = $ds_danh_muc->fetch_assoc()) {
        $arr_danh_muc[] = $row_dm;
    }
}

$ds_tieu_chi = $conn->query("SELECT tc.*, dm.ten_danh_muc 
                             FROM tieu_chi_mau tc 
                             JOIN danh_muc dm ON tc.ma_danh_muc = dm.ma_danh_muc 
                             ORDER BY tc.ma_danh_muc ASC, tc.bat_buoc DESC, tc.ma_tieu_chi ASC");
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
        <a class="navbar-brand fw-bold text-warning" href="admin.php">⚙️ ADMIN - CHỢ ĐỒ CŨ</a>
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
                <h6 class="text-uppercase opacity-75">Tổng Tài Khoản</h6>
                <h2 class="fw-bold mb-0"><?php echo $tong_nguoi_dung; ?> tài khoản</h2>
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
            <button class="nav-link text-dark" data-bs-toggle="tab" data-bs-target="#tab-danhmuc" type="button">
                🗂️ Quản Lý Danh Mục (<?php echo count($arr_danh_muc); ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link text-success" data-bs-toggle="tab" data-bs-target="#tab-checklist" type="button">
                ✅ Smart Checklist (<?php echo $ds_tieu_chi ? $ds_tieu_chi->num_rows : 0; ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link text-dark" data-bs-toggle="tab" data-bs-target="#tab-giaodich" type="button">
                🤝 Theo Dõi Giao Dịch (<?php echo $tong_giao_dich; ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link text-dark" data-bs-toggle="tab" data-bs-target="#tab-nguoidung" type="button">
                👥 Người Dùng & Nhân Viên (<?php echo $tong_nguoi_dung; ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link text-dark" data-bs-toggle="tab" data-bs-target="#tab-tukhoa" type="button">
                🚫 Từ Khóa Cấm (<?php echo $ds_tu_khoa->num_rows; ?>)
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

        <!-- TAB 3: QUẢN LÝ DANH MỤC SẢN PHẨM -->
        <div class="tab-pane fade" id="tab-danhmuc">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-success text-white fw-bold">➕ Thêm Danh Mục Mới</div>
                        <div class="card-body">
                            <form method="POST" action="admin.php">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tên danh mục *</label>
                                    <input type="text" name="ten_danh_muc" class="form-control" placeholder="Ví dụ: Xe cộ & Phương tiện" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mô tả ngắn</label>
                                    <textarea name="mo_ta_dm" class="form-control" rows="3" placeholder="Mô tả các loại đồ cũ thuộc danh mục này..."></textarea>
                                </div>
                                <button type="submit" name="them_danh_muc" class="btn btn-success w-100 fw-bold">Thêm Danh Mục</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white fw-bold">🗂️ Danh Sách Danh Mục Hiện Có</div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên Danh Mục</th>
                                        <th>Mô Tả</th>
                                        <th>Số Bài Đăng</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($arr_danh_muc)): ?>
                                        <?php foreach($arr_danh_muc as $dm): ?>
                                            <tr>
                                                <td class="fw-bold">#<?php echo $dm['ma_danh_muc']; ?></td>
                                                <td class="fw-bold text-success"><?php echo htmlspecialchars($dm['ten_danh_muc']); ?></td>
                                                <td class="small text-muted"><?php echo htmlspecialchars($dm['mo_ta'] ?? ''); ?></td>
                                                <td>
                                                    <span class="badge bg-secondary rounded-pill"><?php echo $dm['so_san_pham']; ?> sản phẩm</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalSuaDM<?php echo $dm['ma_danh_muc']; ?>">
                                                            Sửa
                                                        </button>
                                                        <a href="admin.php?xoa_dm=<?php echo $dm['ma_danh_muc']; ?>" class="btn btn-danger fw-bold" onclick="return confirm('Xóa danh mục này?')">
                                                            Xóa
                                                        </a>
                                                    </div>

                                                    <!-- Modal Sửa Danh Mục -->
                                                    <div class="modal fade" id="modalSuaDM<?php echo $dm['ma_danh_muc']; ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <form method="POST" action="admin.php" class="modal-content">
                                                                <div class="modal-header bg-dark text-white">
                                                                    <h5 class="modal-title">Cập Nhật Danh Mục #<?php echo $dm['ma_danh_muc']; ?></h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="ma_danh_muc" value="<?php echo $dm['ma_danh_muc']; ?>">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Tên danh mục *</label>
                                                                        <input type="text" name="ten_danh_muc" class="form-control" value="<?php echo htmlspecialchars($dm['ten_danh_muc']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label fw-bold">Mô tả ngắn</label>
                                                                        <textarea name="mo_ta_dm" class="form-control" rows="3"><?php echo htmlspecialchars($dm['mo_ta'] ?? ''); ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                                    <button type="submit" name="sua_danh_muc" class="btn btn-primary fw-bold">Lưu Thay Đổi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có danh mục nào.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: QUẢN LÝ SMART CHECKLIST THEO DANH MỤC -->
        <div class="tab-pane fade" id="tab-checklist">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-success text-white fw-bold">➕ Thêm Tiêu Chí Smart Checklist</div>
                        <div class="card-body">
                            <form method="POST" action="admin.php">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Thuộc danh mục sản phẩm *</label>
                                    <select name="ma_danh_muc" class="form-select" required>
                                        <option value="">-- Chọn danh mục áp dụng --</option>
                                        <?php foreach($arr_danh_muc as $dm): ?>
                                            <option value="<?php echo $dm['ma_danh_muc']; ?>"><?php echo htmlspecialchars($dm['ten_danh_muc']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nội dung tiêu chí kiểm tra *</label>
                                    <input type="text" name="ten_tieu_chi" class="form-control" placeholder="Ví dụ: Pin trên 80%, không chai phồng" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Loại tiêu chí *</label>
                                    <select name="bat_buoc" class="form-select">
                                        <option value="1">🔴 Bắt buộc xác nhận khi đăng bài</option>
                                        <option value="0" selected>🔵 Gợi ý cam kết thêm (Tùy chọn)</option>
                                    </select>
                                </div>
                                <button type="submit" name="them_tieu_chi" class="btn btn-success w-100 fw-bold">Thêm Tiêu Chí</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white fw-bold d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <span>✅ BỘ TIÊU CHÍ SMART CHECKLIST TƯƠNG ỨNG TỪNG DANH MỤC</span>
                            <select id="locDanhMucTC" class="form-select form-select-sm w-auto" onchange="locTieuChi()">
                                <option value="">-- Lọc theo tất cả danh mục --</option>
                                <?php foreach($arr_danh_muc as $dm): ?>
                                    <option value="<?php echo htmlspecialchars($dm['ten_danh_muc']); ?>"><?php echo htmlspecialchars($dm['ten_danh_muc']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0" id="bangTieuChi">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Danh Mục Áp Dụng</th>
                                        <th>Nội Dung Tiêu Chí</th>
                                        <th>Mức Độ</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ds_tieu_chi && $ds_tieu_chi->num_rows > 0): ?>
                                        <?php while($tc = $ds_tieu_chi->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?php echo $tc['ma_tieu_chi']; ?></td>
                                                <td class="fw-bold text-success"><?php echo htmlspecialchars($tc['ten_danh_muc']); ?></td>
                                                <td><?php echo htmlspecialchars($tc['ten_tieu_chi']); ?></td>
                                                <td>
                                                    <?php if ($tc['bat_buoc'] == 1): ?>
                                                        <span class="badge bg-danger">Bắt buộc *</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary">Gợi ý</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalSuaTC<?php echo $tc['ma_tieu_chi']; ?>">
                                                            Sửa
                                                        </button>
                                                        <a href="admin.php?xoa_tc=<?php echo $tc['ma_tieu_chi']; ?>" class="btn btn-danger fw-bold" onclick="return confirm('Xóa tiêu chí kiểm tra này?')">
                                                            Xóa
                                                        </a>
                                                    </div>

                                                    <!-- Modal Sửa Tiêu Chí -->
                                                    <div class="modal fade" id="modalSuaTC<?php echo $tc['ma_tieu_chi']; ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <form method="POST" action="admin.php" class="modal-content">
                                                                <div class="modal-header bg-dark text-white">
                                                                    <h5 class="modal-title">Sửa Tiêu Chí #<?php echo $tc['ma_tieu_chi']; ?></h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="ma_tieu_chi" value="<?php echo $tc['ma_tieu_chi']; ?>">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Danh mục áp dụng *</label>
                                                                        <select name="ma_danh_muc" class="form-select" required>
                                                                            <?php foreach($arr_danh_muc as $dm): ?>
                                                                                <option value="<?php echo $dm['ma_danh_muc']; ?>" <?php if($dm['ma_danh_muc'] == $tc['ma_danh_muc']) echo 'selected'; ?>>
                                                                                    <?php echo htmlspecialchars($dm['ten_danh_muc']); ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Nội dung tiêu chí *</label>
                                                                        <input type="text" name="ten_tieu_chi" class="form-control" value="<?php echo htmlspecialchars($tc['ten_tieu_chi']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label fw-bold">Loại tiêu chí</label>
                                                                        <select name="bat_buoc" class="form-select">
                                                                            <option value="1" <?php if($tc['bat_buoc'] == 1) echo 'selected'; ?>>🔴 Bắt buộc xác nhận</option>
                                                                            <option value="0" <?php if($tc['bat_buoc'] == 0) echo 'selected'; ?>>🔵 Gợi ý cam kết thêm</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                                    <button type="submit" name="sua_tieu_chi" class="btn btn-primary fw-bold">Lưu Thay Đổi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted">Chưa thiết lập tiêu chí Smart Checklist nào.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: THEO DÕI & HỖ TRỢ GIAO DỊCH -->
        <div class="tab-pane fade" id="tab-giaodich">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span>🤝 LỊCH SỬ & TRẠNG THÁI GIAO DỊCH MUA BÁN / TRAO ĐỔI</span>
                    <div class="d-flex gap-2">
                        <input type="text" id="timGDInput" class="form-control form-control-sm" placeholder="🔍 Tìm tên SP, người mua/bán..." onkeyup="locGiaoDich()">
                        <select id="locTrangThaiGD" class="form-select form-select-sm" onchange="locGiaoDich()">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="Chờ xác nhận">Chờ xác nhận</option>
                            <option value="Đã xác nhận">Đã xác nhận</option>
                            <option value="Hoàn thành">Hoàn thành</option>
                            <option value="Đã hủy">Đã hủy</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0" id="bangGiaoDich">
                        <thead class="table-light">
                            <tr>
                                <th>Mã GD</th>
                                <th>Sản Phẩm</th>
                                <th>Hình Thức</th>
                                <th>Người Bán</th>
                                <th>Người Mua / Đề Nghị</th>
                                <th>Ngày Tạo</th>
                                <th>Trạng Thái</th>
                                <th>Hỗ Trợ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($ds_giao_dich && $ds_giao_dich->num_rows > 0): ?>
                                <?php while($gd = $ds_giao_dich->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold">#<?php echo $gd['ma_giao_dich']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?php echo htmlspecialchars($gd['hinh_anh']); ?>" style="width: 45px; height: 45px; object-fit: cover;" class="rounded" onerror="this.src='https://via.placeholder.com/45'">
                                                <div>
                                                    <a href="chi-tiet.php?id=<?php echo $gd['ma_san_pham']; ?>" target="_blank" class="fw-bold text-dark text-decoration-none">
                                                        <?php echo htmlspecialchars($gd['ten_san_pham']); ?>
                                                    </a>
                                                    <br><small class="text-danger fw-semibold"><?php echo $gd['gia'] > 0 ? number_format($gd['gia'], 0, ',', '.').'đ' : 'Trao đổi'; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($gd['loai_giao_dich'] === 'trao_doi'): ?>
                                                <span class="badge bg-info text-dark"><i class="bi bi-arrow-left-right"></i> Trao đổi</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary"><i class="bi bi-cart-check"></i> Mua bán</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($gd['ten_nguoi_ban']); ?></strong>
                                            <br><small class="text-muted"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($gd['sdt_ban'] ?? 'Chưa có SĐT'); ?></small>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($gd['ten_nguoi_mua']); ?></strong>
                                            <br><small class="text-muted"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($gd['sdt_mua'] ?? 'Chưa có SĐT'); ?></small>
                                        </td>
                                        <td class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($gd['ngay_tao'])); ?></td>
                                        <td>
                                            <?php if ($gd['trang_thai'] === 'cho_xac_nhan'): ?>
                                                <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                            <?php elseif ($gd['trang_thai'] === 'da_xac_nhan'): ?>
                                                <span class="badge bg-primary">Đã xác nhận</span>
                                            <?php elseif ($gd['trang_thai'] === 'hoan_thanh'): ?>
                                                <span class="badge bg-success">Hoàn thành</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Đã hủy</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($gd['trang_thai'] === 'cho_xac_nhan' || $gd['trang_thai'] === 'da_xac_nhan'): ?>
                                                <a href="admin.php?huy_gd=<?php echo $gd['ma_giao_dich']; ?>" 
                                                   class="btn btn-outline-danger btn-sm fw-bold" 
                                                   onclick="return confirm('Hủy giao dịch này và mở bán lại sản phẩm?')">
                                                    Hủy GD
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center py-4 text-muted">Chưa có giao dịch nào phát sinh.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 6: QUẢN LÝ NGƯỜI DÙNG & NHÂN VIÊN -->
        <div class="tab-pane fade" id="tab-nguoidung">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span>👥 QUẢN LÝ TÀI KHOẢN NGƯỜI DÙNG & NHÂN VIÊN</span>
                    <button class="btn btn-warning btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalThemUser">
                        <i class="bi bi-person-plus-fill"></i> Thêm Tài Khoản Mới
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <input type="text" id="timUserInput" class="form-control form-control-sm" placeholder="🔍 Tìm theo họ tên, email hoặc số điện thoại..." onkeyup="locNguoiDung()">
                        </div>
                        <div class="col-md-3">
                            <select id="locVaiTroSelect" class="form-select form-select-sm" onchange="locNguoiDung()">
                                <option value="">-- Tất cả vai trò --</option>
                                <option value="USER">Người dùng (User)</option>
                                <option value="STAFF">Nhân viên (Staff)</option>
                                <option value="ADMIN">Quản trị viên (Admin)</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0" id="bangNguoiDung">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Họ và Tên</th>
                                    <th>Email</th>
                                    <th>Số Điện Thoại</th>
                                    <th>Địa Chỉ</th>
                                    <th>Vai Trò</th>
                                    <th>Trạng Thái</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($user = $ds_nguoi_dung->fetch_assoc()): ?>
                                    <?php 
                                        $tt_user = isset($user['trang_thai']) ? $user['trang_thai'] : 'hoat_dong'; 
                                        $badge_role = 'info text-dark';
                                        if ($user['vai_tro'] === 'admin') $badge_role = 'danger';
                                        elseif ($user['vai_tro'] === 'staff') $badge_role = 'warning text-dark';
                                    ?>
                                    <tr>
                                        <td><?php echo $user['ma_nguoi_dung']; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($user['ho_ten']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['so_dien_thoai']); ?></td>
                                        <td class="small text-muted" style="max-width: 180px;"><?php echo htmlspecialchars($user['dia_chi'] ?? ''); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $badge_role; ?>">
                                                <?php echo strtoupper($user['vai_tro']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $tt_user === 'bi_khoa' ? 'danger' : 'success'; ?>">
                                                <?php echo $tt_user === 'bi_khoa' ? 'Đã khóa' : 'Hoạt động'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-primary fw-bold" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalSuaUser<?php echo $user['ma_nguoi_dung']; ?>">
                                                    Sửa
                                                </button>

                                                <?php if ($user['ma_nguoi_dung'] != $_SESSION['user_id']): ?>
                                                    <?php if ($tt_user === 'bi_khoa'): ?>
                                                        <a href="admin.php?mo_khoa_user=<?php echo $user['ma_nguoi_dung']; ?>" class="btn btn-success fw-bold">Mở khóa</a>
                                                    <?php else: ?>
                                                        <a href="admin.php?khoa_user=<?php echo $user['ma_nguoi_dung']; ?>" class="btn btn-warning fw-bold" onclick="return confirm('Khóa tài khoản này?')">Khóa</a>
                                                    <?php endif; ?>
                                                    <a href="admin.php?xoa_user=<?php echo $user['ma_nguoi_dung']; ?>" class="btn btn-danger fw-bold" onclick="return confirm('Xóa vĩnh viễn tài khoản và toàn bộ bài đăng của người này?')">Xóa</a>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Modal Sửa & Phân Quyền cho từng User -->
                                            <div class="modal fade" id="modalSuaUser<?php echo $user['ma_nguoi_dung']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <form method="POST" action="admin.php" class="modal-content">
                                                        <div class="modal-header bg-dark text-white">
                                                            <h5 class="modal-title">Cập Nhật Tài Khoản #<?php echo $user['ma_nguoi_dung']; ?></h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="ma_nguoi_dung" value="<?php echo $user['ma_nguoi_dung']; ?>">
                                                            <div class="mb-2">
                                                                <label class="form-label fw-bold">Họ và tên</label>
                                                                <input type="text" name="ho_ten" class="form-control" value="<?php echo htmlspecialchars($user['ho_ten']); ?>" required>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label fw-bold">Email (Không đổi)</label>
                                                                <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label fw-bold">Số điện thoại</label>
                                                                <input type="text" name="so_dien_thoai" class="form-control" value="<?php echo htmlspecialchars($user['so_dien_thoai']); ?>">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label fw-bold">Địa chỉ</label>
                                                                <input type="text" name="dia_chi" class="form-control" value="<?php echo htmlspecialchars($user['dia_chi'] ?? ''); ?>">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label fw-bold">Phân quyền vai trò</label>
                                                                <select name="vai_tro" class="form-select">
                                                                    <option value="user" <?php if($user['vai_tro']=='user') echo 'selected'; ?>>Người dùng (User)</option>
                                                                    <option value="staff" <?php if($user['vai_tro']=='staff') echo 'selected'; ?>>Nhân viên kiểm duyệt (Staff)</option>
                                                                    <option value="admin" <?php if($user['vai_tro']=='admin') echo 'selected'; ?>>Quản trị viên (Admin)</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label fw-bold">Đặt lại mật khẩu mới</label>
                                                                <input type="text" name="mat_khau_moi" class="form-control" placeholder="Bỏ trống nếu giữ nguyên mật khẩu cũ">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" name="sua_nguoi_dung" class="btn btn-primary fw-bold">Lưu Thay Đổi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 7: QUẢN LÝ TỪ KHÓA BỊ CẤM -->
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

<!-- Modal Thêm Tài Khoản Mới -->
<div class="modal fade" id="modalThemUser" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="admin.php" class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">➕ Thêm Tài Khoản Người Dùng / Nhân Viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label fw-bold">Họ và tên *</label>
                    <input type="text" name="ho_ten" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Email đăng nhập *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Mật khẩu *</label>
                    <input type="text" name="mat_khau" class="form-control" value="123456" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Số điện thoại</label>
                    <input type="text" name="so_dien_thoai" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Địa chỉ</label>
                    <input type="text" name="dia_chi" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Vai trò hệ thống *</label>
                    <select name="vai_tro" class="form-select">
                        <option value="user">Người dùng (User)</option>
                        <option value="staff">Nhân viên (Staff)</option>
                        <option value="admin">Quản trị viên (Admin)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" name="them_nguoi_dung" class="btn btn-warning fw-bold">Tạo Tài Khoản</button>
            </div>
        </form>
    </div>
</div>

<script>
function locNguoiDung() {
    let tuKhoa = document.getElementById("timUserInput").value.toLowerCase();
    let vaiTro = document.getElementById("locVaiTroSelect").value.toUpperCase();
    let rows = document.querySelectorAll("#bangNguoiDung tbody tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        let roleCell = row.cells[5].innerText.trim().toUpperCase();
        let matchText = text.includes(tuKhoa);
        let matchRole = (vaiTro === "" || roleCell === vaiTro);
        row.style.display = (matchText && matchRole) ? "" : "none";
    });
}

function locGiaoDich() {
    let tuKhoa = document.getElementById("timGDInput").value.toLowerCase();
    let trangThai = document.getElementById("locTrangThaiGD").value.toLowerCase();
    let rows = document.querySelectorAll("#bangGiaoDich tbody tr");

    rows.forEach(row => {
        if (row.cells.length < 7) return;
        let text = row.innerText.toLowerCase();
        let statusCell = row.cells[6].innerText.trim().toLowerCase();
        let matchText = text.includes(tuKhoa);
        let matchStatus = (trangThai === "" || statusCell === trangThai);
        row.style.display = (matchText && matchStatus) ? "" : "none";
    });
}

function locTieuChi() {
    let danhMuc = document.getElementById("locDanhMucTC").value.toLowerCase();
    let rows = document.querySelectorAll("#bangTieuChi tbody tr");

    rows.forEach(row => {
        if (row.cells.length < 3) return;
        let dmCell = row.cells[1].innerText.trim().toLowerCase();
        row.style.display = (danhMuc === "" || dmCell === danhMuc) ? "" : "none";
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>