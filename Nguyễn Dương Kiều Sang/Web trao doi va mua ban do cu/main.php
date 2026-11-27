<?php include("inc/top.php"); ?>

<!-- Hero Banner Giới Thiệu -->
<div class="hero-banner text-white py-4 mb-4 shadow-sm">
    <div class="container text-center py-2">
        <h2 class="fw-bold mb-2">SÀN TRAO ĐỔI VÀ MUA BÁN ĐỒ CŨ LONG XUYÊN</h2>
        <p class="lead mb-3 fs-6 opacity-75">Kết nối người mua & người bán - Tiết kiệm chi phí, tái sử dụng giá trị</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="huong-dan.php" class="btn btn-light btn-sm text-success fw-bold px-3 shadow-sm">
                <i class="bi bi-book-half me-1"></i> Hướng dẫn mua bán & trao đổi
            </a>
            <a href="chinh-sach.php" class="btn btn-outline-light btn-sm fw-bold px-3">
                <i class="bi bi-shield-check me-1"></i> Chính sách & Quy định đăng tin
            </a>
        </div>
    </div>
</div>

<div class="container flex-grow-1">
    <!-- Thanh Tìm Kiếm & Bộ Lọc Nâng Cao -->
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

    <!-- Danh Sách Sản Phẩm -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0"><i class="bi bi-grid-fill text-success me-2"></i>Danh Sách Tin Đăng Sản Phẩm</h4>
        <span class="badge bg-secondary rounded-pill px-3 py-2"><?php echo $result ? $result->num_rows : 0; ?> sản phẩm</span>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 mb-5">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <?php 
                    $is_trao_doi = (strpos($row['mo_ta'], '[trao_doi]') !== false || $row['gia'] == 0);
                    $link_chi_tiet = "chi-tiet.php?id=" . $row['ma_san_pham'];
                ?>
                <div class="col">
                    <div class="card product-card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                        <div class="position-relative">
                            <a href="<?php echo $link_chi_tiet; ?>">
                                <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['ten_san_pham']); ?>" style="height: 190px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/300x200?text=Anh+San+Pham'">
                            </a>
                            <span class="position-absolute top-0 start-0 badge bg-success m-2 shadow-sm"><?php echo htmlspecialchars($row['ten_danh_muc']); ?></span>
                        </div>
                        
                        <div class="card-body d-flex flex-column p-3">
                            <!-- Click vào tên sản phẩm mở trang chi tiết -->
                            <h6 class="card-title text-truncate fw-bold mb-2" title="<?php echo htmlspecialchars($row['ten_san_pham']); ?>">
                                <a href="<?php echo $link_chi_tiet; ?>" class="text-dark text-decoration-none">
                                    <?php echo htmlspecialchars($row['ten_san_pham']); ?>
                                </a>
                            </h6>
                            
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

                            <!-- Khối Nút Xem Chi Tiết & Nút Báo Cáo Bài Đăng -->
                            <div class="mt-auto d-flex gap-2">
                                <a href="<?php echo $link_chi_tiet; ?>" class="btn btn-outline-success btn-sm flex-grow-1 fw-bold py-2">
                                    Xem chi tiết <i class="bi bi-arrow-right-short ms-1"></i>
                                </a>

                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $row['ma_nguoi_dung']): ?>
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm py-2 px-2" 
                                            title="Báo cáo bài đăng vi phạm"
                                            onclick="moModalBaoCao(<?php echo $row['ma_san_pham']; ?>, '<?php echo htmlspecialchars(addslashes($row['ten_san_pham'])); ?>')">
                                        <i class="bi bi-flag-fill"></i>
                                    </button>
                                <?php elseif (!isset($_SESSION['user_id'])): ?>
                                    <a href="dang-nhap.php" class="btn btn-outline-danger btn-sm py-2 px-2" title="Đăng nhập để báo cáo vi phạm">
                                        <i class="bi bi-flag-fill"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
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

    <!-- Khu Vực Tóm Tắt Chính Sách & Hướng Dẫn (Bấm vào để mở trang riêng) -->
    <div class="card border-0 shadow-sm rounded-3 mb-5 bg-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-info-circle-fill text-success me-2"></i>HƯỚNG DẪN & CHÍNH SÁCH SÀN ĐỒ CŨ
                </h5>
                <div class="d-flex gap-2">
                    <a href="huong-dan.php" class="btn btn-success btn-sm fw-bold">
                        <i class="bi bi-book-half me-1"></i> Xem trang Hướng dẫn
                    </a>
                    <a href="chinh-sach.php" class="btn btn-outline-success btn-sm fw-bold">
                        <i class="bi bi-shield-check me-1"></i> Xem trang Chính sách
                    </a>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <a href="huong-dan.php" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3 h-100 border-start border-success border-4">
                            <h6 class="fw-bold text-success mb-1"><i class="bi bi-1-circle-fill me-1"></i> Quy trình Mua & Trao đổi</h6>
                            <p class="small text-muted mb-0">Xem tiêu chí kiểm tra chất lượng sản phẩm, gửi yêu cầu mua hoặc đề nghị trao đổi đồ trực tiếp với người đăng.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="huong-dan.php" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3 h-100 border-start border-warning border-4">
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-2-circle-fill text-warning me-1"></i> Giao dịch trực tiếp an toàn</h6>
                            <p class="small text-muted mb-0">Ưu tiên gặp mặt kiểm tra hàng thực tế tại khu vực Long Xuyên trước khi thanh toán. Không chuyển khoản đặt cọc trước.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="chinh-sach.php" class="text-decoration-none">
                        <div class="p-3 bg-light rounded-3 h-100 border-start border-danger border-4">
                            <h6 class="fw-bold text-danger mb-1"><i class="bi bi-3-circle-fill me-1"></i> Quy định kiểm duyệt tin đăng</h6>
                            <p class="small text-muted mb-0">Nghiêm cấm đăng bán hàng giả, vũ khí, chất cấm. Mọi bài đăng đều được lọc từ khóa tự động và tiếp nhận báo cáo 24/7.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Báo Cáo Bài Đăng Vi Phạm -->
<div class="modal fade" id="modalBaoCao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="bao-cao.php">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-flag-fill me-2"></i>Báo Cáo Bài Đăng Vi Phạm</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ma_san_pham" id="bc_ma_san_pham">
                    <input type="hidden" name="redirect" value="index.php">
                    
                    <p class="mb-3">Sản phẩm bị báo cáo: <strong id="bc_ten_san_pham" class="text-danger"></strong></p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Lý do báo cáo <span class="text-danger">*</span></label>
                        <select name="ly_do" class="form-select" required>
                            <option value="">-- Chọn lý do vi phạm --</option>
                            <option value="Hàng cấm / Vi phạm pháp luật">Hàng cấm / Vi phạm pháp luật</option>
                            <option value="Lừa đảo / Thông tin sai sự thật">Lừa đảo / Thông tin sai sự thật</option>
                            <option value="Hàng giả, hàng nhái kém chất lượng">Hàng giả, hàng nhái kém chất lượng</option>
                            <option value="Nội dung phản cảm / Từ ngữ thô tục">Nội dung phản cảm / Từ ngữ thô tục</option>
                            <option value="Spam / Đăng trùng lặp nhiều lần">Spam / Đăng trùng lặp nhiều lần</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Mô tả chi tiết thêm (nếu có)</label>
                        <textarea name="mo_ta_them" class="form-control" rows="3" placeholder="Cung cấp thêm thông tin để Admin dễ kiểm tra..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger fw-bold">Gửi Báo Cáo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("inc/bottom.php"); ?>