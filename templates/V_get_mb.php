<?php
require("../config.inc.php");
if(!$arrGroup["会计凭证"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
$strSql='select distinct mbname from v3 where etr="'.$arrPublicVar["username"].'" order by mbname';
//echo $strSql;
$mb = array();
$sql=$mysqli->query($strSql);
while($info=$sql->fetch_array(MYSQLI_BOTH)){
	array_push($mb,$info["mbname"]);
}
$sql->free();
$mysqli->close();
echo json_encode($mb);
?>