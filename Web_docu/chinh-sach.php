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
        <div class="card-header bg-dark text-warning py-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2"></i>CHÍNH SÁCH HOẠT ĐỘNG & QUY ĐỊNH CỘNG ĐỒNG</h4>
        </div>
        <div class="card-body p-4 p-md-5">
            <h5 class="fw-bold text-dark mb-2">1. Mục đích & Nguyên tắc hoạt động</h5>
            <p class="text-muted mb-4">
                <strong>Chợ Đồ Cũ Long Xuyên</strong> là nền tảng kết nối trực tiếp giúp sinh viên và người dân địa phương thanh lý, trao đổi các vật dụng đã qua sử dụng còn giá trị. Hệ thống hoạt động phi lợi nhuận, <strong>không thu phí hoa hồng trung gian</strong> trên các giao dịch giữa người dùng.
            </p>

            <h5 class="fw-bold text-danger mb-2">2. Danh mục hàng hóa & Nội dung nghiêm cấm</h5>
            <div class="alert alert-danger bg-opacity-10 border-danger mb-4">
                <ul class="mb-0 small text-dark">
                    <li class="mb-2"><strong>Hàng cấm theo quy định pháp luật:</strong> Ma túy, chất kích thích, vũ khí, dao găm, súng, pháo nổ và các công cụ hỗ trợ nguy hiểm.</li>
                    <li class="mb-2"><strong>Nội dung vi phạm thuần phong mỹ tục:</strong> Văn hóa phẩm đồi trụy, từ ngữ thô tục, kích động hoặc nội dung liên quan đến cờ bạc, cá độ.</li>
                    <li class="mb-0"><strong>Gian lận thương mại:</strong> Đăng bán hàng giả, hàng nhái kém chất lượng, lừa đảo chiếm đoạt tài sản hoặc mô tả sai sự thật so với tình trạng thực tế của sản phẩm.</li>
                </ul>
            </div>

            <h5 class="fw-bold text-dark mb-3">3. Cơ chế kiểm duyệt & Xử lý vi phạm</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <h6 class="fw-bold text-success"><i class="bi bi-funnel-fill me-1"></i> Bộ lọc từ khóa tự động</h6>
                        <p class="small text-muted mb-0">Hệ thống tự động đối chiếu tiêu đề và mô tả với danh sách từ khóa cấm; mọi tin đăng chứa từ khóa vi phạm sẽ bị chặn ngay lập tức.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <h6 class="fw-bold text-danger"><i class="bi bi-flag-fill me-1"></i> Tiếp nhận báo cáo 24/7</h6>
                        <p class="small text-muted mb-0">Thành viên có thể nhấn nút báo cáo (<i class="bi bi-flag-fill text-danger"></i>) trên từng sản phẩm để gửi phản ánh kèm lý do cụ thể tới ban quản trị.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <h6 class="fw-bold text-dark"><i class="bi bi-person-x-fill me-1"></i> Chế tài xử lý tài khoản</h6>
                        <p class="small text-muted mb-0">Quản trị viên và Nhân viên kiểm duyệt có quyền ẩn, xóa vĩnh viễn tin vi phạm hoặc <strong>khóa tài khoản</strong> đối với người dùng cố tình tái phạm.</p>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-2">4. Trách nhiệm của người tham gia</h5>
            <p class="small text-muted mb-0">
                Người đăng tin chịu hoàn toàn trách nhiệm về tính trung thực của thông tin, hình ảnh và nguồn gốc hợp pháp của sản phẩm. Người mua có trách nhiệm kiểm tra kỹ hàng hóa trước khi xác nhận hoàn thành giao dịch.
            </p>
        </div>
    </div>
</div>

<?php include("inc/bottom.php"); ?>