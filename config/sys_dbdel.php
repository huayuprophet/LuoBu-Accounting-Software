<?php
require("../config.inc.php");
if(!$arrGroup['账套管理']){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_POST["zdv"]==""){
	exit("未选定删除内容！");
}else{
	$arr=explode(',',$_POST["zdv"]);
	foreach ($arr as $value) {
		if($value!=1){
			$strsql="delete from dbuser where id='$value'";
			$mysqli->query($strsql);
		}
	}
	echo '删除成功，请点“查询”进行刷新！';
}
?>