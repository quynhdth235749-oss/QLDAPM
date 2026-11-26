<?php
session_start();
include 'ket-noi.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$thong_bao = '';
$loai_thong_bao = '';

// Lấy thông tin tài khoản hiện tại trước khi xử lý để lấy đường dẫn ảnh cũ
$user_query = $conn->query("SELECT * FROM nguoi_dung WHERE ma_nguoi_dung = $user_id");
$user = $user_query->fetch_assoc();

// 1. Xử lý Cập nhật thông tin cá nhân, Ảnh đại diện & Đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = trim($_POST['ho_ten']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $dia_chi = trim($_POST['dia_chi']);
    $mat_khau_moi = trim($_POST['mat_khau_moi']);
    $anh_dai_dien = $user['anh_dai_dien'] ?? '';

    if (empty($ho_ten)) {
        $thong_bao = "Họ và tên không được để trống!";
        $loai_thong_bao = "danger";
    } else {
        $upload_ok = true;

        // Xử lý upload ảnh đại diện nếu người dùng có chọn file mới
        if (isset($_FILES['anh_dai_dien']) && $_FILES['anh_dai_dien']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['anh_dai_dien']['tmp_name'];
            $file_name = $_FILES['anh_dai_dien']['name'];
            $file_size = $_FILES['anh_dai_dien']['size'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (!in_array($ext, $allowed)) {
                $thong_bao = "Chỉ chấp nhận file ảnh định dạng JPG, JPEG, PNG, WEBP hoặc GIF!";
                $loai_thong_bao = "danger";
                $upload_ok = false;
            } elseif ($file_size > 5 * 1024 * 1024) {
                $thong_bao = "Dung lượng ảnh đại diện không được vượt quá 5MB!";
                $loai_thong_bao = "danger";
                $upload_ok = false;
            } else {
                $upload_dir = 'uploads/avatars/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $new_file_name = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
                $target_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $target_path)) {
                    // Xóa ảnh đại diện cũ trên server (nếu có và nằm trong thư mục uploads)
                    if (!empty($anh_dai_dien) && strpos($anh_dai_dien, 'uploads/') === 0 && file_exists($anh_dai_dien)) {
                        unlink($anh_dai_dien);
                    }
                    $anh_dai_dien = $target_path;
                } else {
                    $thong_bao = "Không thể lưu file ảnh lên máy chủ!";
                    $loai_thong_bao = "danger";
                    $upload_ok = false;
                }
            }
        }

        // Cập nhật vào CSDL nếu upload hợp lệ
        if ($upload_ok) {
            if (!empty($mat_khau_moi)) {
                $stmt = $conn->prepare("UPDATE nguoi_dung SET ho_ten = ?, so_dien_thoai = ?, dia_chi = ?, anh_dai_dien = ?, mat_khau = ? WHERE ma_nguoi_dung = ?");
                $stmt->bind_param("sssssi", $ho_ten, $so_dien_thoai, $dia_chi, $anh_dai_dien, $mat_khau_moi, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE nguoi_dung SET ho_ten = ?, so_dien_thoai = ?, dia_chi = ?, anh_dai_dien = ? WHERE ma_nguoi_dung = ?");
                $stmt->bind_param("ssssi", $ho_ten, $so_dien_thoai, $dia_chi, $anh_dai_dien, $user_id);
            }

            if ($stmt->execute()) {
                $_SESSION['user'] = $ho_ten;
                $_SESSION['avatar'] = $anh_dai_dien;
                $thong_bao = "Cập nhật thông tin tài khoản và ảnh đại diện thành công!";
                $loai_thong_bao = "success";
            } else {
                $thong_bao = "Có lỗi xảy ra khi lưu thông tin!";
                $loai_thong_bao = "danger";
            }
        }
    }
}

// 2. Lấy lại thông tin mới nhất từ CSDL sau khi cập nhật
$user_query = $conn->query("SELECT * FROM nguoi_dung WHERE ma_nguoi_dung = $user_id");
$user = $user_query->fetch_assoc();

// Ảnh mặc định nếu người dùng chưa tải lên ảnh đại diện
$avatar_hien_tai = !empty($user['anh_dai_dien']) && file_exists($user['anh_dai_dien']) 
    ? $user['anh_dai_dien'] 
    : 'https://ui-avatars.com/api/?name=' . urlencode($user['ho_ten']) . '&background=198754&color=fff&size=150';

// 3. Tính điểm đánh giá uy tín trung bình từ người khác
$eval_stat = $conn->query("SELECT COUNT(*) as tong_luot, AVG(diem_danh_gia) as diem_tb FROM danh_gia WHERE ma_nguoi_nhan_danh_gia = $user_id")->fetch_assoc();
$diem_tb = round($eval_stat['diem_tb'] ?? 0, 1);
$tong_luot = $eval_stat['tong_luot'] ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Tài Khoản - Chợ Đồ Cũ Long Xuyên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<!-- Header Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php">🔄 CHỢ ĐỒ CŨ</a>
        <div class="d-flex align-items-center text-white">
            <span class="me-3 d-flex align-items-center">
                <img src="<?php echo htmlspecialchars($avatar_hien_tai); ?>" alt="Avatar" class="rounded-circle me-2 border border-light" style="width: 30px; height: 30px; object-fit: cover;">
                <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong>
            </span>
            <a class="btn btn-outline-light btn-sm me-2" href="index.php"><i class="bi bi-house"></i> Trang chủ</a>
            <a class="btn btn-warning btn-sm text-dark fw-bold me-2" href="quan-ly-bai-dang.php"><i class="bi bi-list-task"></i> Quản lý bài đăng</a>
            <a class="btn btn-light btn-sm text-danger fw-bold" href="dang-xuat.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container flex-grow-1 py-4" style="max-width: 750px;">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-success text-white text-center py-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i>THÔNG TIN TÀI KHOẢN CÁ NHÂN</h4>
        </div>
        
        <div class="card-body p-4">
            <?php if (!empty($thong_bao)): ?>
                <div class="alert alert-<?php echo $loai_thong_bao; ?> alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i><?php echo $thong_bao; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Form Cập Nhật Thông Tin (Thêm enctype="multipart/form-data" để upload ảnh) -->
            <form method="POST" action="tai-khoan.php" enctype="multipart/form-data">
                
                <!-- Khu vực Ảnh Đại Diện -->
                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <img id="previewAvatar" 
                             src="<?php echo htmlspecialchars($avatar_hien_tai); ?>" 
                             alt="Ảnh đại diện" 
                             class="rounded-circle border border-3 border-success shadow-sm" 
                             style="width: 130px; height: 130px; object-fit: cover;">
                        <label for="inputAvatar" class="btn btn-success btn-sm rounded-circle position-absolute bottom-0 end-0 shadow" title="Chọn ảnh đại diện mới" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>
                    <input type="file" name="anh_dai_dien" id="inputAvatar" class="d-none" accept="image/*" onchange="xemTruocAnh(this)">
                    <div class="mt-2">
                        <label for="inputAvatar" class="btn btn-outline-success btn-sm fw-bold">
                            <i class="bi bi-upload me-1"></i> Thay đổi ảnh đại diện
                        </label>
                        <div class="small text-muted mt-1">Định dạng JPG, PNG, WEBP (Tối đa 5MB)</div>
                    </div>
                </div>

                <!-- Thẻ Điểm Uy Tín -->
                <div class="bg-light p-3 rounded mb-4 border d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold text-dark d-block mb-1"><i class="bi bi-award-fill text-warning me-1"></i> Đánh giá uy tín tài khoản:</span>
                        <small class="text-muted">Tính từ lượt đánh giá của các giao dịch hoàn thành</small>
                    </div>
                    <div>
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                            ⭐ <?php echo $diem_tb > 0 ? $diem_tb : 'Chưa có'; ?> / 5.0 (<?php echo $tong_luot; ?> đánh giá)
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Địa chỉ Email (Tên đăng nhập)</label>
                    <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <small class="text-muted">Email tài khoản không thể thay đổi.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="ho_ten" class="form-control" value="<?php echo htmlspecialchars($user['ho_ten']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Số điện thoại liên hệ</label>
                    <input type="text" name="so_dien_thoai" class="form-control" placeholder="Ví dụ: 0912345678" value="<?php echo htmlspecialchars($user['so_dien_thoai']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Địa chỉ / Khu vực giao dịch</label>
                    <textarea name="dia_chi" class="form-control" rows="2" placeholder="Ví dụ: Đường Ung Văn Khiêm, Phường Đông Xuyên, TP. Long Xuyên..."><?php echo htmlspecialchars($user['dia_chi']); ?></textarea>
                </div>

                <div class="mb-4 p-3 border rounded bg-light">
                    <label class="form-label fw-bold text-danger"><i class="bi bi-key-fill me-1"></i> Đổi mật khẩu mới (Nếu muốn):</label>
                    <input type="password" name="mat_khau_moi" class="form-control" placeholder="Để trống nếu không muốn thay đổi mật khẩu">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success py-2 fw-bold fs-5">
                        <i class="bi bi-check-circle-fill me-1"></i> LƯU THAY ĐỔI
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary">Quay lại trang chủ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto small text-muted">
    © 2026 Chợ Đồ Cũ Long Xuyên. Đường Ung Văn Khiêm, Phường Đông Xuyên, TP. Long Xuyên, An Giang.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function xemTruocAnh(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewAvatar').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>