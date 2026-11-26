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
                    
                    <?php if (isset($_SESSION['vai_tro']) &&$_SESSION['vai_tro'] === 'admin'): ?>
                        <li class="nav-item me-2">
                            <a class="btn btn-dark btn-sm text-warning fw-bold" href="admin.php">
                                <i class="bi bi-gear-fill me-1"></i> Trang Quản Trị
                            </a>
                        </li>
                    <?php endif; ?>

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