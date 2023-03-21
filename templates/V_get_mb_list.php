<?php
require("../config.inc.php");
if(!$arrGroup["会计凭证"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$strSql='select zy,km,kmF,dr,cr from v3 where mbname="'.$_POST["v1"].'" and etr="'.$arrPublicVar["username"].'" order by id';
//echo $strSql;
$mb = array();
$sql=$mysqli->query($strSql);
while($info=$sql->fetch_array(MYSQLI_BOTH)){
	$strRow= $info["zy"].','.$info["km"].','.$info["kmF"].','.$info["dr"].','.$info["cr"];
	array_push($mb,$strRow);
}
$sql->free();
$mysqli->close();
echo json_encode($mb);
?>