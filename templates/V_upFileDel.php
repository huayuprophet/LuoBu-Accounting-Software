<?php
require("../config.inc.php");
if(!$arrGroup["序时账簿"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//只有上传附件本人才有删除附件的权利；
$md5User=md5($_SESSION["username"]);
if(!substr_count($_GET["keyword"],$md5User)){
	echo '非本人上传附件，不能删除；';
	exit();
}

$fileAddrersStemp = $arrPublicVar["filesystems"].$_GET["keyword"];

if(file_exists($fileAddrersStemp)){
	//删除永久文件；
	if(unlink($fileAddrersStemp)){
		//删除数据库中与该附件相关的记录；
		$strsql='delete from v1_fj where fileaddress="'.$_GET["keyword"].'"';
		$mysqli->query($strsql);
		echo '成功删除该附件；';
	}else{
		echo '删除该附件失败；';
	}
}else{
	//删除数据库中与该附件相关的记录；
	$strsql='delete from v1_fj where fileaddress="'.$_GET["keyword"].'"';
	$mysqli->query($strsql);
	echo '成功删除文件记录；';
}
?>