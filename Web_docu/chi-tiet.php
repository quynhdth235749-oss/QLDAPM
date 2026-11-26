<?php
session_start();
include 'ket-noi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$vai_tro = isset($_SESSION['vai_tro']) ? $_SESSION['vai_tro'] : 'user';
$thong_bao = '';
$loai_thong_bao = '';

// 1. Gửi Yêu cầu mua / trao đổi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gui_yeu_cau'])) {
    if ($user_id == 0) {
        header("Location: dang-nhap.php");
        exit();
    }
    $loai_giao_dich = $_POST['loai_giao_dich'];
    $ma_nguoi_ban = intval($_POST['ma_nguoi_ban']);

    $check_gd = $conn->query("SELECT * FROM giao_dich WHERE ma_san_pham = $id AND ma_nguoi_mua = $user_id AND trang_thai = 'cho_xac_nhan'");
    if ($check_gd->num_rows > 0) {
        $thong_bao = "Bạn đã gửi yêu cầu giao dịch cho sản phẩm này rồi!";
        $loai_thong_bao = "warning";
    } else {
        $stmt = $conn->prepare("INSERT INTO giao_dich (ma_san_pham, ma_nguoi_ban, ma_nguoi_mua, loai_giao_dich, trang_thai) VALUES (?, ?, ?, ?, 'cho_xac_nhan')");
        $stmt->bind_param("iiis", $id, $ma_nguoi_ban, $user_id, $loai_giao_dich);
        if ($stmt->execute()) {
            $thong_bao = "Gửi yêu cầu thành công! Đã chuyển tới danh sách quản lý giao dịch.";
            $loai_thong_bao = "success";
        }
    }
}

// 2. Người bán cập nhật trạng thái giao dịch
if (isset($_POST['cap_nhat_giao_dich'])) {
    $ma_gd = intval($_POST['ma_giao_dich']);
    $trang_thai_moi = $_POST['trang_thai_moi'];

    $conn->query("UPDATE giao_dich SET trang_thai = '$trang_thai_moi' WHERE ma_giao_dich = $ma_gd");
    if ($trang_thai_moi === 'hoan_thanh') {
        $conn->query("UPDATE san_pham SET trang_thai = 'da_ban' WHERE ma_san_pham = $id");
    }
    $thong_bao = "Đã cập nhật trạng thái giao dịch!";
    $loai_thong_bao = "info";
}

// 3. Người mua Gửi đánh giá sao
if (isset($_POST['gui_danh_gia'])) {
    $ma_giao_dich = intval($_POST['ma_giao_dich']);
    $ma_nguoi_nhan = intval($_POST['ma_nguoi_nhan']);
    $diem = intval($_POST['diem_danh_gia']);
    $nhan_xet = trim($_POST['nhan_xet']);

    $stmt = $conn->prepare("INSERT INTO danh_gia (ma_giao_dich, ma_nguoi_danh_gia, ma_nguoi_nhan_danh_gia, diem_danh_gia, nhan_xet) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiis", $ma_giao_dich, $user_id, $ma_nguoi_nhan, $diem, $nhan_xet);
    if ($stmt->execute()) {
        $thong_bao = "Cảm ơn bạn đã đánh giá người bán!";
        $loai_thong_bao = "success";
    }
}

// Lấy thông tin bài đăng
$sp_query = $conn->query("SELECT sp.*, dm.ten_danh_muc, nd.ho_ten, nd.so_dien_thoai, nd.dia_chi FROM san_pham sp 
                          JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
                          JOIN nguoi_dung nd ON sp.ma_nguoi_dung = nd.ma_nguoi_dung 
                          WHERE sp.ma_san_pham = $id");
$sp = $sp_query->fetch_assoc();

// Tính tổng điểm đánh giá của người bán này
$ma_ban = $sp ? $sp['ma_nguoi_dung'] : 0;
$eval_stat = $conn->query("SELECT COUNT(*) as tong_luot, AVG(diem_danh_gia) as diem_tb FROM danh_gia WHERE ma_nguoi_nhan_danh_gia = $ma_ban")->fetch_assoc();
$diem_tb = round($eval_stat['diem_tb'], 1);
$tong_luot = $eval_stat['tong_luot'];

// Kiểm tra xem người đang xem có giao dịch HOÀN THÀNH với sản phẩm này hay không
$da_giao_dich_xong = false;
$ma_gd_hoan_thanh = 0;
if ($user_id > 0) {
    $check_success = $conn->query("SELECT * FROM giao_dich WHERE ma_san_pham = $id AND ma_nguoi_mua = $user_id AND trang_thai = 'hoan_thanh'");
    if ($check_success && $check_success->num_rows > 0) {
        $da_giao_dich_xong = true;
        $gd_data = $check_success->fetch_assoc();
        $ma_gd_hoan_thanh = $gd_data['ma_giao_dich'];
    }
}

// Lấy nhật ký giao dịch
$giao_dich_query = $conn->query("SELECT gd.*, nd_mua.ho_ten as ten_nguoi_mua FROM giao_dich gd 
                                JOIN nguoi_dung nd_mua ON gd.ma_nguoi_mua = nd_mua.ma_nguoi_dung 
                                WHERE gd.ma_san_pham = $id ORDER BY gd.ngay_tao DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sp ? htmlspecialchars($sp['ten_san_pham']) : 'Sản phẩm'; ?> - Chợ Đồ Cũ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-dark bg-success mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-arrow-left me-1"></i> Quay lại trang chủ</a>
        <?php if (isset($_SESSION['user'])): ?>
            <span class="navbar-text text-white"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user']); ?></span>
        <?php endif; ?>
    </div>
</nav>

<div class="container flex-grow-1 pb-5" style="max-width: 950px;">
    <?php if (!empty($thong_bao)): ?>
        <div class="alert alert-<?php echo $loai_thong_bao; ?> alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i><?php echo $thong_bao; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($sp): ?>
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
            <div class="row g-0">
                <div class="col-md-5 bg-dark d-flex align-items-center justify-content-center p-2">
                    <?php $img_path = !empty($sp['hinh_anh']) ? $sp['hinh_anh'] : 'images/h1.webp'; ?>
                    <img src="<?php echo htmlspecialchars($img_path); ?>" class="img-fluid rounded" alt="Ảnh sản phẩm" style="max-height: 380px; object-fit: contain;" onerror="this.src='https://via.placeholder.com/400x300'">
                </div>

                <div class="col-md-7">
                    <div class="card-body p-4">
                        <span class="badge bg-success mb-2"><?php echo htmlspecialchars($sp['ten_danh_muc']); ?></span>
                        
                        <h3 class="fw-bold text-dark"><?php echo htmlspecialchars($sp['ten_san_pham']); ?></h3>
                        
                        <!-- Giá hoặc nhãn Trao đổi -->
                        <?php if ($sp['gia'] > 0): ?>
                            <h3 class="text-danger fw-bold my-2"><?php echo number_format($sp['gia'], 0, ',', '.'); ?> VNĐ</h3>
                        <?php else: ?>
                            <h4 class="text-warning fw-bold my-2"><span class="badge bg-warning text-dark"><i class="bi bi-arrow-left-right me-1"></i> Chỉ nhận trao đổi đồ</span></h4>
                        <?php endif; ?>

                        <div class="border-top border-bottom py-2 my-3 small text-secondary">
                            <p class="mb-1"><i class="bi bi-person-fill text-primary me-2"></i><strong>Người bán:</strong> <?php echo htmlspecialchars($sp['ho_ten']); ?> (SĐT: <?php echo htmlspecialchars($sp['so_dien_thoai']); ?>)</p>
                            
                            <!-- BỔ SUNG: HIỂN THỊ TỔNG ĐIỂM ĐÁNH GIÁ CỦA NGƯỜI BÁN -->
                            <p class="mb-1">
                                <i class="bi bi-star-fill text-warning me-2"></i><strong>Uy tín người bán:</strong> 
                                <span class="badge bg-warning text-dark fw-bold">
                                    ⭐ <?php echo $diem_tb > 0 ? $diem_tb : 'Chưa có'; ?> / 5.0 (<?php echo $tong_luot; ?> đánh giá)
                                </span>
                            </p>

                            <p class="mb-1"><i class="bi bi-geo-alt-fill text-danger me-2"></i><strong>Nơi đăng:</strong> <?php echo !empty($sp['dia_chi']) ? htmlspecialchars($sp['dia_chi']) : 'Long Xuyên, An Giang'; ?></p>
                            <p class="mb-0"><i class="bi bi-shield-check text-success me-2"></i><strong>Tình trạng:</strong> <?php echo htmlspecialchars($sp['tinh_trang']); ?></p>
                        </div>

                        <p class="card-text"><strong>Mô tả chi tiết:</strong><br><?php echo nl2br(htmlspecialchars($sp['mo_ta'])); ?></p>

                        <!-- Khối Nút Bấm -->
                        <div class="mt-4">
                            <?php if ($user_id > 0 && ($user_id == $sp['ma_nguoi_dung'] || $vai_tro === 'admin')): ?>
                                <div class="card bg-light border-warning p-3">
                                    <div class="fw-bold text-dark mb-2"><i class="bi bi-gear-fill text-warning me-1"></i> Quản lý bài đăng của bạn:</div>
                                    <div class="row g-2">
                                        <div class="col-4"><a href="sua-bai.php?id=<?php echo $id; ?>" class="btn btn-warning w-100 fw-bold">Sửa bài</a></div>
                                        <div class="col-4"><a href="an-bai.php?id=<?php echo $id; ?>" class="btn btn-secondary w-100 fw-bold"><?php echo $sp['trang_thai'] === 'an' ? 'Hiện tin' : 'Ẩn tin'; ?></a></div>
                                        <div class="col-4"><a href="xoa-bai.php?id=<?php echo $id; ?>" class="btn btn-danger w-100 fw-bold" onclick="return confirm('Bạn có chắc chắn xóa?')">Xóa bài</a></div>
                                    </div>
                                </div>
                            <?php elseif ($sp['trang_thai'] === 'da_ban'): ?>
                                <button class="btn btn-secondary btn-lg w-100" disabled>Sản phẩm đã hoàn thành giao dịch</button>
                            <?php else: ?>
                                <form method="POST" action="chi-tiet.php?id=<?php echo $id; ?>" class="row g-2">
                                    <input type="hidden" name="ma_nguoi_ban" value="<?php echo $sp['ma_nguoi_dung']; ?>">
                                    <input type="hidden" name="gui_yeu_cau" value="1">
                                    <div class="col-6">
                                        <button type="submit" name="loai_giao_dich" value="mua" class="btn btn-primary btn-lg w-100 fw-bold">
                                            <i class="bi bi-cart-check me-1"></i> Đặt mua ngay
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" name="loai_giao_dich" value="trao_doi" class="btn btn-warning btn-lg w-100 fw-bold text-dark">
                                            <i class="bi bi-arrow-left-right me-1"></i> Yêu cầu trao đổi
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ô ĐÁNH GIÁ CHỈ HIỂN THỊ KHI ĐÃ GIAO DỊCH / TRAO ĐỔI THÀNH CÔNG -->
        <?php if ($da_giao_dich_xong): ?>
            <div class="card shadow-sm border-0 rounded-3 mb-4 border-start border-warning border-4">
                <div class="card-header bg-white fw-bold py-3 text-success">
                    <i class="bi bi-check-circle-fill me-2"></i> BẠN ĐÃ GIAO DỊCH THÀNH CÔNG SẢN PHẨM NÀY
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="chi-tiet.php?id=<?php echo $id; ?>">
                        <h6 class="fw-bold mb-3">Đánh giá độ uy tín người bán cho lần giao dịch này:</h6>
                        <input type="hidden" name="ma_nguoi_nhan" value="<?php echo $sp['ma_nguoi_dung']; ?>">
                        <input type="hidden" name="ma_giao_dich" value="<?php echo $ma_gd_hoan_thanh; ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="diem_danh_gia" class="form-select" required>
                                    <option value="5">⭐⭐⭐⭐⭐ (5 sao)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 sao)</option>
                                    <option value="3">⭐⭐⭐ (3 sao)</option>
                                    <option value="2">⭐⭐ (2 sao)</option>
                                    <option value="1">⭐ (1 sao)</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input type="text" name="nhan_xet" class="form-control" placeholder="Viết cảm nhận giao dịch..." required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="gui_danh_gia" class="btn btn-warning w-100 fw-bold">Gửi đánh giá</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- NHẬT KÝ XÁC NHẬN GIAO DỊCH 2 BÊN -->
        <?php if ($giao_dich_query && $giao_dich_query->num_rows > 0): ?>
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="bi bi-arrow-repeat me-2"></i> TRẠNG THÁI XÁC NHẬN GIAO DỊCH 2 BÊN
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php while($gd = $giao_dich_query->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <strong><?php echo htmlspecialchars($gd['ten_nguoi_mua']); ?></strong> yêu cầu: 
                                    <span class="badge bg-<?php echo $gd['loai_giao_dich'] === 'mua' ? 'primary' : 'warning text-dark'; ?>">
                                        <?php echo strtoupper($gd['loai_giao_dich']); ?>
                                    </span>
                                    <br><small class="text-muted">Thời gian: <?php echo $gd['ngay_tao']; ?></small>
                                </div>

                                <div>
                                    <?php if ($gd['trang_thai'] === 'cho_xac_nhan'): ?>
                                        <?php if ($user_id == $sp['ma_nguoi_dung']): ?>
                                            <form method="POST" action="chi-tiet.php?id=<?php echo $id; ?>" class="d-inline">
                                                <input type="hidden" name="ma_giao_dich" value="<?php echo $gd['ma_giao_dich']; ?>">
                                                <input type="hidden" name="cap_nhat_giao_dich" value="1">
                                                <button type="submit" name="trang_thai_moi" value="hoan_thanh" class="btn btn-success btn-sm me-1 fw-bold">
                                                    ✓ Đồng ý bán/trao đổi
                                                </button>
                                                <button type="submit" name="trang_thai_moi" value="da_huy" class="btn btn-outline-danger btn-sm">
                                                    ✕ Từ chối
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Đã gửi yêu cầu - Chờ người bán xác nhận</span>
                                        <?php endif; ?>
                                    <?php elseif ($gd['trang_thai'] === 'hoan_thanh'): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Giao dịch thành công</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Đã hủy</span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-danger text-center">Sản phẩm không tồn tại!</div>
    <?php endif; ?>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto small text-muted">
    © 2026 Chợ Đồ Cũ Long Xuyên. Đường Ung Văn Khiêm, Phường Đông Xuyên, TP. Long Xuyên, An Giang.
</footer>

</body>
</html>