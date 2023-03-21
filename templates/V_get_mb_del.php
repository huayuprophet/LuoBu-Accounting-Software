<?php
require("../config.inc.php");
if(!$arrGroup["会计凭证"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$strSql='delete from v3 where mbname="'.$_POST["v1"].'" and etr="'.$arrPublicVar["username"].'"';
//echo $strSql;
$mysqli->query($strSql);
$afr=$mysqli->affected_rows;
if($afr>0){
	echo 'YES';
}else{
	echo 'NO';
}
$mysqli->close();
?>