<?php 
require("../model/database.php");
require("../model/danhmuc.php");
require("../model/mathang.php");

$dm = new DANHMUC();
$danhmuc = $dm->laydanhmuc();

$mh = new MATHANG();

if(isset($_REQUEST["action"])){
    $action = $_REQUEST["action"];
}
else{
    $action = "null"; 
}

switch($action){

    case "null": 	
        $m = $mh->laymathang();
        include("main.php");
        break;

        case "group": 	
            if(isset($_REQUEST["id"]))
            {
                $danhmuc_id = $_REQUEST["id"];
            }
            else
            {
                $danhmuc_id = 1;
            }
        
            $dmuc = $dm->laydanhmuctheoid($danhmuc_id);
        
            $m = $mh->laymathangtheodanhmuc($danhmuc_id);
        
            include("group.php");
            break;
        
		case "detail": // Hoặc "chitiet"
			if(isset($_REQUEST["id"])) {
				$mathang_id = $_REQUEST["id"];
				// Lấy thông tin chi tiết của 1 mặt hàng theo ID sản phẩm
				$ct = $mh->laymathangtheoid($mathang_id); 
				include("detail.php"); // Giao diện hiển thị chi tiết sản phẩm
			}
			break;
    default:
        break;
}
?>
