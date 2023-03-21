<?php
require("../config.inc.php");
if(!$arrGroup["财务报表"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$path = $arrPublicVar["filesystems"].'_db/';//$path定义上传的文件存放地址，可根据实际的需要进行改动；
	
//判断是否有上传文件；
if(!empty($_FILES['up_file']['name'])){
	//将文件信息赋值给变量$fileinfo；
	$fileinfo=$_FILES['up_file'];
	//获取文件的扩展名；
	$kzm=strrev(explode('.',strrev($fileinfo['name']))[0]);
	//上传文件新的名称；
	$newName="report.xlsx";
	//判断文件的大小，1M=1048576KB，本处文件大小不能超出10M；
	if($fileinfo['size']<104857600 && $fileinfo['size']>0){
		//上传文件；
		move_uploaded_file($fileinfo['tmp_name'],$path.$newName);
		//判断文件是否上传成功；
		if($fileinfo){
			//上传成功后向 v1_fj 表中写入附件的相关信息；
			echo '上传成功!';
		}else{
			echo '上传失败，请重新上传！';
		}
	}else{
		echo '上传失败，文件大小超出100M或未知！';
	}
}
?>