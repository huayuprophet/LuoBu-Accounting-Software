<?php
require("../config.inc.php");
if(!$arrGroup["序时账簿"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$yzqj=mysqli_real_escape_string($mysqli,$_POST["qj"]);//验证期间；

//使用事务机制处理反记账操作；
$mysqli->autocommit(false);
$mysqli->query('delete from v2 where qj="'.$yzqj.'"');
$mysqli->query('update v1 set acc=null where qj="'.$yzqj.'"');
$mysqli->commit();
$mysqli->close();

echo '反记账成功：'.$yzqj;
?>