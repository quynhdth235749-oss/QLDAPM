<?php
session_start();
include 'ket-noi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$vai_tro = isset($_SESSION['vai_tro']) ? $_SESSION['vai_tro'] : 'user';

// Lấy thông tin bài đăng
$sp_query = $conn->query("SELECT * FROM san_pham WHERE ma_san_pham = $id");
$sp = $sp_query->fetch_assoc();

// Kiểm tra quyền (chỉ chủ bài đăng hoặc Admin mới được sửa)
if (!$sp || ($sp['ma_nguoi_dung'] != $user_id && $vai_tro !== 'admin')) {
    echo "<script>alert('Bạn không có quyền sửa bài đăng này!'); window.location.href='index.php';</script>";
    exit();
}

$thong_bao = '';
$danh_muc_result = $conn->query("SELECT * FROM danh_muc");

// Xử lý Cập nhật bài đăng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_san_pham = trim($_POST['ten_san_pham']);
    $ma_danh_muc = intval($_POST['ma_danh_muc']);
    $gia = floatval($_POST['gia']);
    $tinh_trang = trim($_POST['tinh_trang']);
    $mo_ta = trim($_POST['mo_ta']);
    $trang_thai = $_POST['trang_thai'];
    $hinh_anh = $sp['hinh_anh']; // Giữ ảnh cũ mặc định

    // Xử lý upload ảnh mới nếu chọn
    if (isset($_FILES['hinh_anh_file']) && $_FILES['hinh_anh_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['hinh_anh_file']['tmp_name'];
        $file_name = $_FILES['hinh_anh_file']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allow_ext = array('jpg', 'jpeg', 'png', 'webp', 'gif');

        if (in_array($ext, $allow_ext)) {
            $target_dir = "images/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $new_file_name = time() . '_' . rand(100, 999) . '.' . $ext;
            $target_file = $target_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $target_file)) {
                $hinh_anh = $target_file;
            }
        }
    }

    $stmt = $conn->prepare("UPDATE san_pham SET ten_san_pham=?, ma_danh_muc=?, gia=?, tinh_trang=?, mo_ta=?, hinh_anh=?, trang_thai=? WHERE ma_san_pham=?");
    $stmt->bind_param("siddsssi", $ten_san_pham, $ma_danh_muc, $gia, $tinh_trang, $mo_ta, $hinh_anh, $trang_thai, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Cập nhật tin đăng thành công!'); window.location.href='chi-tiet.php?id=$id';</script>";
        exit();
    } else {
        $thong_bao = 'Có lỗi xảy ra khi cập nhật!';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Bài Đăng - Chợ Đồ Cũ Long Xuyên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-dark bg-success mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🔄 CHỢ ĐỒ CŨ</a>
        <a class="btn btn-outline-light btn-sm" href="chi-tiet.php?id=<?php echo $id; ?>"><i class="bi bi-arrow-left"></i> Quay lại chi tiết</a>
    </div>
</nav>

<div class="container my-auto py-3" style="max-width: 700px;">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-warning text-dark text-center py-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>CHỈNH SỬA TIN ĐĂNG</h4>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($thong_bao)): ?>
                <div class="alert alert-danger mb-3"><?php echo $thong_bao; ?></div>
            <?php endif; ?>

            <form method="POST" action="sua-bai.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên sản phẩm</label>
                    <input type="text" name="ten_san_pham" class="form-control" value="<?php echo htmlspecialchars($sp['ten_san_pham']); ?>" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Danh mục</label>
                        <select name="ma_danh_muc" class="form-select" required>
                            <?php while($dm = $danh_muc_result->fetch_assoc()): ?>
                                <option value="<?php echo $dm['ma_danh_muc']; ?>" <?php if($sp['ma_danh_muc'] == $dm['ma_danh_muc']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($dm['ten_danh_muc']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Giá bán (VNĐ)</label>
                        <input type="number" name="gia" class="form-control" value="<?php echo $sp['gia']; ?>" step="1000" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tình trạng đồ cũ</label>
                        <input type="text" name="tinh_trang" class="form-control" value="<?php echo htmlspecialchars($sp['tinh_trang']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái bài đăng</label>
                        <select name="trang_thai" class="form-select">
                            <option value="con_hang" <?php if($sp['trang_thai'] == 'con_hang') echo 'selected'; ?>>Còn hàng (Hiển thị)</option>
                            <option value="an" <?php if($sp['trang_thai'] == 'an') echo 'selected'; ?>>Ẩn tin (Không ai thấy)</option>
                            <option value="da_ban" <?php if($sp['trang_thai'] == 'da_ban') echo 'selected'; ?>>Đã bán</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 p-3 bg-light rounded border">
                    <label class="form-label fw-bold text-success"><i class="bi bi-image me-1"></i> Ảnh hiện tại:</label><br>
                    <img src="<?php echo htmlspecialchars($sp['hinh_anh']); ?>" class="img-thumbnail mb-2" style="max-height: 120px;"><br>
                    <small class="text-muted">Tải ảnh mới để thay thế (để trống nếu muốn giữ ảnh cũ):</small>
                    <input type="file" name="hinh_anh_file" class="form-control mt-1" accept="image/*">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Mô tả chi tiết</label>
                    <textarea name="mo_ta" class="form-control" rows="4"><?php echo htmlspecialchars($sp['mo_ta']); ?></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning py-2 fw-bold fs-5">
                        <i class="bi bi-check-circle-fill me-1"></i> LƯU THAY ĐỔI
                    </button>
                    <a href="chi-tiet.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>