<?php
session_start();
include 'ket-noi.php';
include("inc/top.php");
?>

<div class="container my-5 flex-grow-1">
    <div class="mb-3">
        <a href="index.php" class="btn btn-outline-success btn-sm fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Quay lại trang chủ
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-success text-white py-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-book-half me-2"></i>HƯỚNG DẪN MUA BÁN & TRAO ĐỔI ĐỒ CŨ</h4>
        </div>
        <div class="card-body p-4 p-md-5">
            <h5 class="fw-bold text-success mb-3">1. Hướng dẫn dành cho Người Mua / Người Muốn Trao Đổi</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100 border-start border-success border-4">
                        <h6 class="fw-bold">Bước 1: Tìm kiếm & Lọc sản phẩm</h6>
                        <p class="small text-muted mb-0">Nhập từ khóa món đồ cần tìm hoặc chọn danh mục tương ứng (Đồ điện tử, Thời trang, Sách, Gia dụng...) ngay trên trang chủ.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100 border-start border-success border-4">
                        <h6 class="fw-bold">Bước 2: Kiểm tra chất lượng & Uy tín</h6>
                        <p class="small text-muted mb-0">Bấm vào tên hoặc ảnh sản phẩm để xem bảng <strong>Tiêu chí kiểm tra chất lượng</strong> thực tế và điểm đánh giá sao của người bán.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100 border-start border-success border-4">
                        <h6 class="fw-bold">Bước 3: Gửi yêu cầu Mua hoặc Trao đổi</h6>
                        <p class="small text-muted mb-0">Đăng nhập tài khoản, nhấn <strong>Đặt mua</strong> hoặc <strong>Đề nghị trao đổi</strong>, sau đó liên hệ số điện thoại người bán để hẹn gặp mặt kiểm tra hàng.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100 border-start border-success border-4">
                        <h6 class="fw-bold">Bước 4: Hoàn tất & Đánh giá</h6>
                        <p class="small text-muted mb-0">Sau khi nhận hàng và hoàn thành giao dịch, vào mục <em>Bài đăng & Giao dịch</em> để chấm điểm (1–5 sao) và để lại nhận xét cho đối tác.</p>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold text-success mb-3">2. Hướng dẫn dành cho Người Đăng Bán / Trao Đổi</h5>
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item px-0">
                    <strong><i class="bi bi-check-circle-fill text-success me-2"></i>Đăng tin chi tiết:</strong> Bấm nút <em>Đăng tin mới</em> trên thanh menu, nhập tên sản phẩm rõ ràng, tải hình ảnh chụp thật và chọn hình thức <strong>Bán</strong> (kèm giá) hoặc <strong>Trao đổi</strong>.
                </li>
                <li class="list-group-item px-0">
                    <strong><i class="bi bi-check-circle-fill text-success me-2"></i>Cam kết chất lượng:</strong> Điền trung thực các tiêu chí kiểm tra tình trạng sản phẩm (ví dụ: pin, màn hình, độ mới...) để người mua yên tâm giao dịch.
                </li>
                <li class="list-group-item px-0">
                    <strong><i class="bi bi-check-circle-fill text-success me-2"></i>Xác nhận đơn giao dịch:</strong> Theo dõi yêu cầu từ người mua trong mục <em>Bài đăng & Giao dịch</em> để xác nhận và cập nhật trạng thái khi hoàn tất.
                </li>
            </ul>

            <div class="alert alert-warning border-0 shadow-sm mb-0">
                <h6 class="fw-bold text-dark"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Nguyên tắc giao dịch an toàn tại Long Xuyên</h6>
                <p class="small mb-0">Ưu tiên hẹn gặp trực tiếp ở nơi công cộng (quán cà phê, khuôn viên trường học, khu vực đông người) để kiểm tra kỹ sản phẩm trước khi thanh toán. Tuyệt đối không chuyển khoản đặt cọc trước khi chưa xem hàng thực tế.</p>
            </div>
        </div>
    </div>
</div>

<?php include("inc/bottom.php"); ?>