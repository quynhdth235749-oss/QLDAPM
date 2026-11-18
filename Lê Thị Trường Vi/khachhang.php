<?php
require_once("database.php");

if (!class_exists('KHACHHANG')) {
    class KHACHHANG {
        public function themkhachhang($email, $sodt, $hoten) {
            $db = DATABASE::connect();
            try {
                $sql = "INSERT INTO nguoidung(email,matkhau,sodienthoai,hoten,loai,trangthai) VALUES(:email,:matkhau,:sodt,:hoten,3,1)";
                $cmd = $db->prepare($sql);
                $cmd->bindValue(':email', $email);
                $cmd->bindValue(':matkhau', md5($sodt));
                $cmd->bindValue(':sodt', $sodt);
                $cmd->bindValue(':hoten', $hoten);
                $cmd->execute();
                return $db->lastInsertId();
            } catch (PDOException $e) {
                echo "<p>Lỗi truy vấn: " . $e->getMessage() . "</p>";
                exit();
            }
        }

        public function kiemtrakhachhanghople($email, $matkhau) {
            $db = DATABASE::connect();
            try {
                $sql = "SELECT * FROM nguoidung WHERE email=:email AND matkhau=:matkhau AND trangthai=1 AND loai=3";
                $cmd = $db->prepare($sql);
                $cmd->bindValue(":email", $email);
                $cmd->bindValue(":matkhau", md5($matkhau));
                $cmd->execute();
                $valid = ($cmd->rowCount() == 1);
                $cmd->closeCursor();
                return $valid;
            } catch (PDOException $e) {
                echo "<p>Lỗi truy vấn: " . $e->getMessage() . "</p>";
                exit();
            }
        }

        public function laythongtinkhachhang($email) {
            $db = DATABASE::connect();
            try {
                $sql = "SELECT * FROM nguoidung WHERE email=:email AND loai=3";
                $cmd = $db->prepare($sql);
                $cmd->bindValue(":email", $email);
                $cmd->execute();
                $ketqua = $cmd->fetch();
                $cmd->closeCursor();
                return $ketqua;
            } catch (PDOException $e) {
                echo "<p>Lỗi truy vấn: " . $e->getMessage() . "</p>";
                exit();
            }
        }
    }
}
?>