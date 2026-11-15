<?php
session_start();
include 'ket-noi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dang-nhap.php");
    exit();
}

$thong_bao = '';
$loai_thong_bao = '';
$danh_muc_result = $conn->query("SELECT * FROM danh_muc");

// Hàm kiểm tra từ khóa bị cấm
function kiemTraTuKhoaCam($conn, $noi_dung) {
    $noi_dung_lower = mb_strtolower($noi_dung, 'UTF-8');
    $res = $conn->query("SELECT tu_khoa FROM tu_khoa_cam");
    $vi_pham = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $kw = mb_strtolower(trim($row['tu_khoa']), 'UTF-8');
            if ($kw !== '' && mb_strpos($noi_dung_lower, $kw) !== false) {
                $vi_pham[] = $row['tu_khoa'];
            }
        }
    }
    return $vi_pham;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_san_pham = trim($_POST['ten_san_pham']);
    $ma_danh_muc = intval($_POST['ma_danh_muc']);
    $hinh_thuc = $_POST['hinh_thuc'];
    $gia = ($hinh_thuc === 'trao_doi') ? 0 : floatval($_POST['gia']);
    $tinh_trang = trim($_POST['tinh_trang']);
    $mo_ta = trim($_POST['mo_ta']);
    $ma_nguoi_dung = $_SESSION['user_id'];

    // 1. Kiểm tra từ khóa bị cấm
    $tu_vi_pham = kiemTraTuKhoaCam($conn, $ten_san_pham . ' ' . $mo_ta);
    if (!empty($tu_vi_pham)) {
        $danh_sach_tu = htmlspecialchars(implode(', ', $tu_vi_pham));
        $thong_bao = "Bài đăng bị từ chối vì chứa từ khóa bị cấm: <strong>$danh_sach_tu</strong>!";
        $loai_thong_bao = 'danger';
    }

    $hinh_anh = '';

    // 2. Xử lý upload ảnh (tên file ngắn gọn tránh bị cắt chuỗi trong DB)
    if (empty($thong_bao)) {
        if (isset($_FILES['hinh_anh_file']) && $_FILES['hinh_anh_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['hinh_anh_file']['tmp_name'];
            $file_name = $_FILES['hinh_anh_file']['name'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allow_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'jfif'];

            if (in_array($ext, $allow_ext)) {
                $target_dir = "images/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                // Tên file ngắn gọn ~17 ký tự (ví dụ: images/673a9f12.jpg)
                $target_file = $target_dir . uniqid() . '.' . $ext;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    $hinh_anh = $target_file;
                } else {
                    $thong_bao = 'Không thể lưu file ảnh vào thư mục images/!';
                    $loai_thong_bao = 'danger';
                }
            } else {
                $thong_bao = 'Chỉ chấp nhận file ảnh (.jpg, .jpeg, .png, .webp, .gif, .jfif)!';
                $loai_thong_bao = 'danger';
            }
        } else {
            $thong_bao = 'Vui lòng chọn ảnh sản phẩm (hoặc ảnh vượt quá 2MB)!';
            $loai_thong_bao = 'danger';
        }
    }

    // 3. Lưu vào CSDL và quay về trang chủ index.php
    if (empty($thong_bao)) {
        if (empty($ten_san_pham) || empty($ma_danh_muc) || empty($hinh_anh) || ($hinh_thuc === 'ban' && $gia <= 0)) {
            $thong_bao = 'Vui lòng nhập đầy đủ thông tin và chọn ảnh hợp lệ!';
            $loai_thong_bao = 'danger';
        } else {
            $mo_ta_day_du = "[$hinh_thuc] " . $mo_ta;
            $stmt = $conn->prepare("INSERT INTO san_pham (ma_nguoi_dung, ma_danh_muc, ten_san_pham, mo_ta, hinh_anh, gia, tinh_trang, trang_thai) VALUES (?, ?, ?, ?, ?, ?, ?, 'con_hang')");
           $stmt->bind_param("iisssds", $ma_nguoi_dung, $ma_danh_muc, $ten_san_pham, $mo_ta_day_du, $hinh_anh, $gia, $tinh_trang);

            if ($stmt->execute()) {
                echo "<script>alert('Đăng tin sản phẩm thành công!'); window.location.href='index.php';</script>";
                exit();
            } else {
                $thong_bao = 'Lỗi lưu CSDL: ' . $conn->error;
                $loai_thong_bao = 'danger';
            }
        }
    }
}
?>
<!-- Phần HTML bên dưới giữ nguyên như file dang-bai.php của bạn -->

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Tin Mới - Chợ Đồ Cũ Long Xuyên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script>
        function toggleGiaInput() {
            var hinhThuc = document.getElementById("hinh_thuc").value;
            var khungGia = document.getElementById("khung_gia");
            var inputGia = document.getElementById("input_gia");
            
            if (hinhThuc === "trao_doi") {
                khungGia.style.display = "none";
                inputGia.value = 0;
                inputGia.removeAttribute("required");
            } else {
                khungGia.style.display = "block";
                inputGia.setAttribute("required", "required");
            }
        }

        function previewImage(event) {
            if (event.target.files && event.target.files[0]) {
                var reader = new FileReader();
                reader.onload = function() {
                    var output = document.getElementById('img_preview');
                    output.src = reader.result;
                    output.style.display = 'block';
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-dark bg-success mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php">🔄 CHỢ ĐỒ CŨ</a>
        <div class="d-flex align-items-center text-white">
            <span class="me-3"><i class="bi bi-person-circle me-1"></i> <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></span>
            <a class="btn btn-outline-light btn-sm me-2" href="index.php"><i class="bi bi-house"></i> Trang chủ</a>
            <a class="btn btn-light btn-sm text-danger fw-bold" href="dang-xuat.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container my-auto py-3" style="max-width: 700px;">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-success text-white text-center py-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>ĐĂNG TIN BÁN / TRAO ĐỔI ĐỒ CŨ</h4>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($thong_bao)): ?>
                <div class="alert alert-<?php echo $loai_thong_bao; ?> alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $thong_bao; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="dang-bai.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" name="ten_san_pham" class="form-control" placeholder="Nhập tên sản phẩm..." required value="<?php echo isset($_POST['ten_san_pham']) ? htmlspecialchars($_POST['ten_san_pham']) : ''; ?>">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nhu cầu bài đăng <span class="text-danger">*</span></label>
                        <select name="hinh_thuc" id="hinh_thuc" class="form-select fw-bold text-success" onchange="toggleGiaInput()" required>
                            <option value="ban" selected>💰 Đăng để bán</option>
                            <option value="trao_doi">🔄 Chỉ nhận trao đổi đồ</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                        <select name="ma_danh_muc" class="form-select" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php while($dm = $danh_muc_result->fetch_assoc()): ?>
                                <option value="<?php echo $dm['ma_danh_muc']; ?>"><?php echo htmlspecialchars($dm['ten_danh_muc']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3" id="khung_gia">
                    <label class="form-label fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" name="gia" id="input_gia" class="form-control" placeholder="Ví dụ: 150000" step="1000" min="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tình trạng đồ cũ</label>
                    <select name="tinh_trang" class="form-select">
                        <option value="Mới 99%">Mới 99% (Hầu như chưa dùng)</option>
                        <option value="Mới 95%">Mới 95% (Còn rất tốt)</option>
                        <option value="Mới 90%">Mới 90% (Xài lướt, còn mới)</option>
                        <option value="Đã qua sử dụng" selected>Đã qua sử dụng</option>
                    </select>
                </div>

                <div class="mb-3 p-3 bg-light rounded border">
                    <label class="form-label fw-bold text-success"><i class="bi bi-upload me-1"></i> Chọn ảnh sản phẩm từ máy tính <span class="text-danger">*</span></label>
                    <input type="file" name="hinh_anh_file" class="form-control" accept="image/*" onchange="previewImage(event)" required>
                    <small class="text-muted d-block mt-1">Chấp nhận định dạng: .jpg, .png, .webp, .jpeg</small>
                    
                    <div class="mt-3 text-center">
                        <img id="img_preview" src="#" alt="Xem trước ảnh" class="img-thumbnail rounded shadow-sm" style="max-height: 180px; display: none; margin: 0 auto;">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Mô tả chi tiết sản phẩm</label>
                    <textarea name="mo_ta" class="form-control" rows="4" placeholder="Nhập mô tả chi tiết sản phẩm..."><?php echo isset($_POST['mo_ta']) ? htmlspecialchars($_POST['mo_ta']) : ''; ?></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success py-2 fw-bold fs-5">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i>ĐĂNG TIN NGAY
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-auto small text-muted">
    © 2026 Chợ Đồ Cũ Long Xuyên. Đường Ung Văn Khiêm, Phường Đông Xuyên, TP. Long Xuyên, An Giang.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>