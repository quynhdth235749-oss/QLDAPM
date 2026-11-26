<!-- Chân Trang Chuyên Nghiệp (Footer) -->
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
<script>
function moModalBaoCao(maSP, tenSP) {
    document.getElementById('bc_ma_san_pham').value = maSP;
    document.getElementById('bc_ten_san_pham').innerText = tenSP;
    var myModal = new bootstrap.Modal(document.getElementById('modalBaoCao'));
    myModal.show();
}
</script>
</body>
</html>