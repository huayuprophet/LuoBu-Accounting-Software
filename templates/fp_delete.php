<?php
require("../config.inc.php");
if(!$arrGroup["发票管理"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$qj=mysqli_real_escape_string($mysqli,$_GET["qj"]);
$pzh=mysqli_real_escape_string($mysqli,$_GET["pzh"]);

if($_GET["lx"]=='rk'){
	$strsql='select id from chin where qj="'.$qj.'" and pzh="'.$pzh.'" and fphm is not null';
	$strsqldelete='delete from chin where qj="'.$qj.'" and pzh="'.$pzh.'"';
}else{
	$strsql='select id from chout where qj="'.$qj.'" and pzh="'.$pzh.'" and fphm is not null';
	$strsqldelete='delete from chout where qj="'.$qj.'" and pzh="'.$pzh.'"';
}

//echo $strsql."\n";
//echo $strsqldelete;

$sql=$mysqli->query($strsql);
$num_rows=$sql->num_rows;
if($num_rows>0){
	exit('发票已录入，无法删除！');
}

$strsqlquery='select * from V1 where qj="'.$qj.'" and pzh="'.$pzh.'" and acc is not null';//已记账；
$sqlquery=$mysqli->query($strsqlquery);
$num_rows=$sqlquery->num_rows;
if($num_rows>0){
	exit('凭证已经记账！');
}

if($mysqli->query($strsqldelete)){
	$mysqli->query("update V1 set chk=null where qj='".$qj."' and pzh='".$pzh."'");
	echo '流水数据删除成功，凭证已取消审核！';
}
?>