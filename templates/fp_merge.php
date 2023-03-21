<?php
require("../config.inc.php");
if(!$arrGroup["发票管理"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$starid=mysqli_real_escape_string($mysqli,$_POST["startid"]);
$id=mysqli_real_escape_string($mysqli,$_POST["id"]);
$sumsl=floatval($_POST["sumsl"]);
$sumjr=floatval($_POST["sumjr"]);

//echo $slwb."\n";

if($_POST["lx"]=='rk'){
	$strsqlupdate='update chin set slwb="'.$sumsl.'",dr="'.$sumjr.'" where id="'.$starid.'"';
	$strsqldelete='delete from chin where id in('.$id.')';
}else{
	$strsqlupdate='update chout set slwb="'.$sumsl.'",dr="'.$sumjr.'" where id="'.$starid.'"';
	$strsqldelete='delete from chout where id in('.$id.')';
}

//echo $strsqlupdate."\n".$strsqldelete;

$mysqli->autocommit(FALSE);//关闭事务的自动提交；
if(!$mysqli->query($strsqlupdate)){
	$mysqli->rollback();
	exit('合并更新出错！');
}
if(!$mysqli->query($strsqldelete)){
	$mysqli->rollback();
	exit('合并删除出错！');
}
$mysqli->commit();
echo '合并成功，请刷新！';
?>