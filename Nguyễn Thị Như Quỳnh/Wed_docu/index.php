<?php
session_start();
include 'ket-noi.php';

// Xử lý bộ lọc tìm kiếm và danh mục
$tu_khoa = isset($_GET['tu_khoa']) ? trim($_GET['tu_khoa']) : '';
$ma_danh_muc = isset($_GET['ma_danh_muc']) ? intval($_GET['ma_danh_muc']) : 0;

// Chỉ lấy các sản phẩm có trạng thái 'con_hang'
$sql = "SELECT sp.*, dm.ten_danh_muc, nd.dia_chi FROM san_pham sp 
        JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc 
        JOIN nguoi_dung nd ON sp.ma_nguoi_dung = nd.ma_nguoi_dung
        WHERE sp.trang_thai = 'con_hang'";

if (!empty($tu_khoa)) {
    $sql .= " AND (sp.ten_san_pham LIKE '%$tu_khoa%' OR sp.mo_ta LIKE '%$tu_khoa%')";
}
if ($ma_danh_muc > 0) {
    $sql .= " AND sp.ma_danh_muc = '$ma_danh_muc'";
}
$sql .= " ORDER BY sp.ngay_tao DESC";

$result = $conn->query($sql);
$danh_muc_result = $conn->query("SELECT * FROM danh_muc");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chợ Đồ Cũ Long Xuyên - Trao Đổi & Mua Bán Đồ Cũ</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .product-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        }
        .hero-banner {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        }
        .footer-link {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-link:hover {
            color: #20c997;
        }
    </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<!-- 1. Thanh Công Cụ Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="index.php">
            <i class="bi bi-arrow-repeat me-2 fs-3"></i> CHỢ ĐỒ CŨ
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['user'])): ?>
                    <li class="nav-item me-2">
                        <a class="nav-link text-white fw-bold" href="tai-khoan.php" title="Xem thông tin tài khoản">
                            <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user']); ?>
                        </a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-light btn-sm fw-bold" href="quan-ly-bai-dang.php">
                            <i class="bi bi-list-task me-1"></i> Bài đăng & Giao dịch
                        </a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="btn btn-warning btn-sm text-dark fw-bold px-3 shadow-sm" href="dang-bai.php">
                            <i class="bi bi-plus-circle-fill me-1"></i> Đăng tin mới
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm px-3" href="dang-xuat.php">
                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a class="btn btn-light btn-sm text-success fw-bold px-3 shadow-sm" href="dang-ky.php">
                            <i class="bi bi-person-plus-fill me-1"></i> Đăng ký
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm px-3" href="dang-nhap.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- 2. Hero Banner Giới Thiệu -->
<div class="hero-banner text-white py-4 mb-4 shadow-sm">
    <div class="container text-center py-2">
        <h2 class="fw-bold mb-2">SÀN TRAO ĐỔI VÀ MUA BÁN ĐỒ CŨ LONG XUYÊN</h2>
        <p class="lead mb-0 fs-6 opacity-75">Kết nối người mua & người bán - Tiết kiệm chi phí, tái sử dụng giá trị</p>
    </div>
</div>

<div class="container flex-grow-1">
    <!-- 3. Thanh Tìm Kiếm & Bộ Lọc Nâng Cao -->
    <div class="card shadow-sm mb-4 border-0 rounded-3">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="index.php" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="tu_khoa" class="form-control border-start-0" placeholder="Nhập tên sản phẩm (iPhone, Sách, Áo khoác...)" value="<?php echo htmlspecialchars($tu_khoa); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="ma_danh_muc" class="form-select">
                        <option value="0">-- Tất cả danh mục --</option>
                        <?php while($dm = $danh_muc_result->fetch_assoc()): ?>
                            <option value="<?php echo $dm['ma_danh_muc']; ?>" <?php if($ma_danh_muc == $dm['ma_danh_muc']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($dm['ten_danh_muc']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100 fw-bold">
                        <i class="bi bi-funnel me-1"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. Danh Sách Sản Phẩm -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0"><i class="bi bi-grid-fill text-success me-2"></i>Danh Sách Tin Đăng Sản Phẩm</h4>
        <span class="badge bg-secondary rounded-pill px-3 py-2"><?php echo $result ? $result->num_rows : 0; ?> sản phẩm</span>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 mb-5">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <?php 
                    // Kiểm tra bài đăng là Bán hay Trao Đổi
                    $is_trao_doi = (strpos($row['mo_ta'], '[trao_doi]') !== false || $row['gia'] == 0);
                ?>
                <div class="col">
                    <div class="card product-card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['ten_san_pham']); ?>" style="height: 190px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/300x200?text=Anh+San+Pham'">
                            <span class="position-absolute top-0 start-0 badge bg-success m-2 shadow-sm"><?php echo htmlspecialchars($row['ten_danh_muc']); ?></span>
                        </div>
                        
                        <div class="card-body d-flex flex-column p-3">
                            <h6 class="card-title text-truncate fw-bold mb-2 text-dark" title="<?php echo htmlspecialchars($row['ten_san_pham']); ?>">
                                <?php echo htmlspecialchars($row['ten_san_pham']); ?>
                            </h6>
                            
                            <!-- Hiển thị Giá hoặc Thẻ Trao Đổi -->
                            <?php if ($is_trao_doi): ?>
                                <p class="card-text text-warning fw-bold fs-6 mb-1">
                                    <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-arrow-left-right me-1"></i> Chỉ trao đổi đồ</span>
                                </p>
                            <?php else: ?>
                                <p class="card-text text-danger fw-bold fs-5 mb-1"><?php echo number_format($row['gia'], 0, ',', '.'); ?> VNĐ</p>
                            <?php endif; ?>

                            <p class="card-text text-muted small mb-1">
                                <i class="bi bi-shield-check text-success me-1"></i> Tình trạng: <strong><?php echo htmlspecialchars($row['tinh_trang']); ?></strong>
                            </p>

                            <p class="card-text text-secondary small mb-3 text-truncate">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> Nơi đăng: <strong><?php echo !empty($row['dia_chi']) ? htmlspecialchars($row['dia_chi']) : 'Long Xuyên, An Giang'; ?></strong>
                            </p>

                            <a href="chi-tiet.php?id=<?php echo $row['ma_san_pham']; ?>" class="btn btn-outline-success btn-sm mt-auto fw-bold py-2">
                                Xem chi tiết sản phẩm <i class="bi bi-arrow-right-short ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center py-4 rounded-3 shadow-sm">
                    <i class="bi bi-exclamation-circle fs-3 d-block mb-2 text-warning"></i>
                    Không tìm thấy sản phẩm nào phù hợp với yêu cầu tìm kiếm của bạn.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 5. Chân Trang Chuyên Nghiệp (Footer) -->
<footer class="bg-dark text-white pt-5 pb-3 mt-auto border-top border-success border-3">
    <div class="container">
        <div class="row g-4">
            <!-- Cột 1: Thông tin thương hiệu & Địa chỉ -->
            <div class="col-lg-5 col-md-6">
                <h5 class="fw-bold text-success mb-3 d-flex align-items-center">
                    <i class="bi bi-arrow-repeat me-2"></i> CHỢ ĐỒ CŨ LONG XUYÊN
                </h5>
                <p class="small text-secondary mb-3">
                    Nền tảng thương mại điện tử chuyên cung cấp dịch vụ trao đổi, mua bán đồ cũ uy tín hàng đầu. Cam kết mang lại giải pháp tiết kiệm chi phí và tiện lợi nhất cho cộng đồng.
                </p>
                <div class="small text-light">
                    <p class="mb-2"><i class="bi bi-geo-alt-fill text-danger me-2"></i> <strong>Địa chỉ:</strong> Đường Ung Văn Khiêm, Phường Đông Xuyên, TP. Long Xuyên, An Giang</p>
                    <p class="mb-2"><i class="bi bi-telephone-fill text-success me-2"></i> <strong>Điện thoại:</strong> (0296) 3841 234 - 0901 234 567</p>
                    <p class="mb-0"><i class="bi bi-envelope-fill text-warning me-2"></i> <strong>Email:</strong> lienhe@chodocu.vn</p>
                </div>
            </div>

            <!-- Cột 2: Danh mục & Liên kết nhanh -->
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-warning mb-3">DANH MỤC SẢN PHẨM</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="index.php?ma_danh_muc=1" class="footer-link"><i class="bi bi-chevron-right me-1"></i> Đồ điện tử & Công nghệ</a></li>
                    <li class="mb-2"><a href="index.php?ma_danh_muc=2" class="footer-link"><i class="bi bi-chevron-right me-1"></i> Thời trang & Phụ kiện</a></li>
                    <li class="mb-2"><a href="index.php?ma_danh_muc=3" class="footer-link"><i class="bi bi-chevron-right me-1"></i> Sách & Giáo trình học tập</a></li>
                    <li class="mb-2"><a href="index.php?ma_danh_muc=4" class="footer-link"><i class="bi bi-chevron-right me-1"></i> Đồ gia dụng & Nội thất</a></li>
                </ul>
            </div>

            <!-- Cột 3: Hỗ trợ & Chính sách -->
            <div class="col-lg-4 col-md-12">
                <h6 class="fw-bold text-warning mb-3">HỖ TRỢ KHÁCH HÀNG</h6>
                <ul class="list-unstyled small text-secondary mb-3">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Quyền lợi khi đăng tin bán đồ cũ</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Quy trình giao dịch & An toàn mua bán</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Thời gian làm việc: 7:30 - 18:00 (Thứ 2 - Chủ Nhật)</li>
                </ul>
                <div>
                    <span class="small me-2 text-secondary">Kết nối với chúng tôi:</span>
                    <a href="#" class="text-white me-2 fs-5"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white me-2 fs-5"><i class="bi bi-zalo"></i></a>
                    <a href="#" class="text-white fs-5"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="row align-items-center small text-secondary">
            <div class="col-md-6 text-center text-md-start">
                © 2026 <strong>Chợ Đồ Cũ Long Xuyên</strong>. Tất cả các quyền được bảo lưu.
            </div>
            <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                <span>Thiết kế & Phát triển hệ thống</span>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>