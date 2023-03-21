<?php
require("../config.inc.php");
if(!$arrGroup["会计科目"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='GET'){
	
	//安全验证；
	$id=mysqli_real_escape_string($mysqli,$_GET["id"]);
	
	//会计科目数据的停用或启用；
	$strsql='update account set ty=-1*ty where id like "'.$id.'%"';
	$mysqli->query($strsql);
	$afr=$mysqli->affected_rows;
	//此处可能不止一行数据被停用或启用；
	if($afr>=1){
		echo '状态设置成功！';
	}
	
}
?>