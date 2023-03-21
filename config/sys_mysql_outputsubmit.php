<?php
require("../config.inc.php");
if(!$arrGroup["导出数据"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

function mkdirs($dir, $mode = 0777)
{
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
    if (!mkdirs(dirname($dir), $mode)) return FALSE;
    return @mkdir($dir, $mode);
}
try{
	$tbl=mysqli_real_escape_string($mysqli,$_GET["tbl"]);
	$tblname=mysqli_real_escape_string($mysqli,$_GET["tblname"]);
	$tbladdress=trim(mysqli_real_escape_string($mysqli,$_GET["tbladdress"]));
	$qj=mysqli_real_escape_string($mysqli,$_GET["qj"]);
	//检测输入的地址是否存在；
	if(mkdirs($tbladdress)){
		if($qj!=null){
			$strsql="select * from $tbl where qj=$qj";
		}else{
			$strsql="select * from $tbl";
		}
		$exec='mysql -u'.$arrPublicVar["user"].' -p'.$arrPublicVar["pwd"].' -e "'.$strsql.'" '.$arrPublicVar["db"].' > '.$tbladdress.$tbl.'.xls'; 
		exec($exec);
		echo "success";
	}else{
		echo 'path does not exist';
	}
}catch(Exception $e){
    echo 'Caught exception: ',$e->getMessage(),"\n";
}
?>