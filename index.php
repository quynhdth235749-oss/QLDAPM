<?php
// Nhúng các file xử lý backend (chuẩn tên không dấu)
require_once 'config.php';
require_once 'user.php';
require_once 'search.php';
require_once 'product.php';
require_once 'gdichvadgia.php';

// Lấy từ khóa tìm kiếm và mã danh mục an toàn
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$ma_danh_muc = (isset($_GET['ma_danh_muc']) && $_GET['ma_danh_muc'] !== '') ? $_GET['ma_danh_muc'] : null;

// Gọi hàm lấy dữ liệu từ database
$danhSachSanPham = timKiemSanPham($keyword, $ma_danh_muc);
$danhMucList = getDanhMuc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Trao Đổi Và Mua Bán Đồ Cũ</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; color: #334155; }
        header { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 20px; font-weight: bold; color: #2563eb; text-decoration: none; }
        .search-form { display: flex; gap: 10px; width: 50%; }
        .search-form input, .search-form select { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; }
        .search-form input { flex: 1; }
        .search-form button { background: #2563eb; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500; }
        .search-form button:hover { background: #1d4ed8; }
        .btn-post { background: #f97316; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-weight: bold; }
        .btn-post:hover { background: #ea580c; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; margin-top: 20px; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; }
        .card:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .card img { width: 100%; height: 180px; object-fit: cover; background: #f1f5f9; }
        .card-content { padding: 15px; }
        .badge { display: inline-block; background: #e0f2fe; color: #0369a1; font-size: 11px; font-weight: bold; padding: 3px 8px; border-radius: 12px; margin-bottom: 8px; }
        .card-title { font-size: 16px; font-weight: bold; margin-bottom: 5px; color: #1e293b; }
        .card-desc { font-size: 13px; color: #64748b; margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .price { color: #dc2626; font-size: 16px; font-weight: bold; }
        .seller { font-size: 12px; color: #94a3b8; margin-top: 10px; border-top: 1px solid #f1f5f9; padding-top: 8px; }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="logo">Chợ Đồ Cũ</a>
        <form class="search-form" action="index.php" method="GET">
            <input type="text" name="keyword" placeholder="Tìm kiếm đồ cũ..." value="<?= htmlspecialchars($keyword) ?>">
            <select name="ma_danh_muc">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($danhMucList as $dm): ?>
                    <option value="<?= $dm['ma_danh_muc'] ?>" <?= $ma_danh_muc == $dm['ma_danh_muc'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Tìm kiếm</button>
        </form>
        <a href="#" class="btn-post">+ Đăng tin</a>
    </header>

    <div class="container">
        <h2>Bài đăng mới nhất (<?= count($danhSachSanPham) ?>)</h2>
        
        <div class="product-grid">
            <?php if (!empty($danhSachSanPham)): ?>
                <?php foreach ($danhSachSanPham as $sp): ?>
                    <div class="card">
                        <img src="<?= !empty($sp['hinh_anh']) ? htmlspecialchars($sp['hinh_anh']) : 'https://via.placeholder.com/300x200' ?>" alt="Sản phẩm">
                        <div class="card-content">
                            <span class="badge"><?= htmlspecialchars($sp['tinh_trang']) ?></span>
                            <h3 class="card-title"><?= htmlspecialchars($sp['ten_san_pham']) ?></h3>
                            <p class="card-desc"><?= htmlspecialchars($sp['mo_ta']) ?></p>
                            <div class="price"><?= number_format($sp['gia']) ?> VNĐ</div>
                            <div class="seller">Người đăng: <?= htmlspecialchars($sp['ten_nguoi_ban']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align: center; padding: 40px; color: #94a3b8;">
                    Không tìm thấy sản phẩm nào!
                </p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>