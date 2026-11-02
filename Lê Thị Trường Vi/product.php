<?php
require_once 'config.php';

// Dang tin san pham moi kem Smart-Checklist
function dangTinSanPham($ma_nguoi_dung, $ma_danh_muc, $ten_san_pham, $mo_ta, $hinh_anh, $gia, $tinh_trang, $list_tieu_chi = []) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();

        $sql_sp = "INSERT INTO san_pham (ma_nguoi_dung, ma_danh_muc, ten_san_pham, mo_ta, hinh_anh, gia, tinh_trang) 
                   VALUES (:ma_nguoi_dung, :ma_danh_muc, :ten_san_pham, :mo_ta, :hinh_anh, :gia, :tinh_trang)";
        $stmt_sp = $pdo->prepare($sql_sp);
        $stmt_sp->execute([
            'ma_nguoi_dung' => $ma_nguoi_dung,
            'ma_danh_muc' => $ma_danh_muc,
            'ten_san_pham' => $ten_san_pham,
            'mo_ta' => $mo_ta,
            'hinh_anh' => $hinh_anh,
            'gia' => $gia,
            'tinh_trang' => $tinh_trang
        ]);
        
        $ma_san_pham = $pdo->lastInsertId();

        if (!empty($list_tieu_chi)) {
            $sql_chk = "INSERT INTO kiem_tra_chat_luong (ma_san_pham, tieu_chi, dat_yeu_cau) VALUES (:ma_san_pham, :tieu_chi, :dat_yeu_cau)";
            $stmt_chk = $pdo->prepare($sql_chk);
            foreach ($list_tieu_chi as $chk) {
                $stmt_chk->execute([
                    'ma_san_pham' => $ma_san_pham,
                    'tieu_chi' => $chk['tieu_chi'],
                    'dat_yeu_cau' => $chk['dat_yeu_cau'] ? 1 : 0
                ]);
            }
        }

        $pdo->commit();
        return $ma_san_pham;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

// Lay chi tiet san pham + Smart-Checklist
function getChiTietSanPham($ma_san_pham) {
    global $pdo;
    
    $sql_sp = "SELECT sp.*, dm.ten_danh_muc, nd.ho_ten, nd.so_dien_thoai, nd.email 
               FROM san_pham sp
               JOIN danh_muc dm ON sp.ma_danh_muc = dm.ma_danh_muc
               JOIN nguoi_dung nd ON sp.ma_nguoi_dung = nd.ma_nguoi_dung
               WHERE sp.ma_san_pham = :ma_san_pham";
    $stmt_sp = $pdo->prepare($sql_sp);
    $stmt_sp->execute(['ma_san_pham' => $ma_san_pham]);
    $san_pham = $stmt_sp->fetch();

    if ($san_pham) {
        $sql_chk = "SELECT * FROM kiem_tra_chat_luong WHERE ma_san_pham = :ma_san_pham";
        $stmt_chk = $pdo->prepare($sql_chk);
        $stmt_chk->execute(['ma_san_pham' => $ma_san_pham]);
        $san_pham['checklist'] = $stmt_chk->fetchAll();
    }

    return $san_pham;
}
?>