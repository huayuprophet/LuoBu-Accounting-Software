<?php
//权限设置，禁止非法阅读；
require("../config.inc.php");
if(!$arrGroup[$_POST["tbl"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//文件上传后台核心代码，首先判断有无上传文件，存在上传文件的再检查文件类型是否符合要求，最后执行上传动作；
if(empty($_FILES["up_file"]["name"])){
	echo '无上传文件的刷新！';
}else{
	//将文件信息赋值给变量$fileinfo；
	$fileinfo = $_FILES["up_file"];
	//循环所有的上传文件，该类型可以上传多个附件；
	echo '上传文件数量：'.count($fileinfo["name"])."\n";
	for($i=0;$i<count($fileinfo["name"]);$i++){
		$tbl = mysqli_real_escape_string($mysqli,$_POST["tbl"]);
		$id = mysqli_real_escape_string($mysqli,$_POST["id_"]);
		//获取文件的扩展名；
		$kzm=strrev(explode('.',strrev($fileinfo["name"][$i]))[0]);
		//上传文件类型限定；
		$arrayFileType=array("pdf","jpg","png","gif","xlsx","xls","docx","doc","rar","zip","ofd");
		if(!in_array($kzm,$arrayFileType,true)){
			echo '上传文件类型不正确!';
		}else{
			//上传文件新的名称由表名称，id值，时间，操作员，序号四个部分组成，每个部分使用下划线区分；
			//时间与序号是区分不同文件的关键变量，时间可能会重复，但是加上了序号，重复的机率变得更小。
			$newName=$tbl.'_'.$id.'_'.@strtotime("now").'_'.md5($_SESSION["username"]).'_'.$i.'.'.$kzm;
			//判断文件的大小，1M=1048576KB，本处文件大小不能超出10M；
			if($fileinfo['size'][$i]<104857600 && $fileinfo['size'][$i]>0){
				//上传文件；
				$upFileOk = move_uploaded_file($fileinfo["tmp_name"][$i],$arrPublicVar["filesystems"].$newName);
				//判断文件是否上传成功；
				if($upFileOk){
					//上传成功后向 v1_fj 表中写入附件的相关信息；
					$strsql='insert into jc_fj(fileaddress,filename,tablename,recordid) values("'.$newName.'","'.$fileinfo["name"][$i].'","'.$tbl.'","'.$id.'")';
					$mysqli->query($strsql);
					echo '上传成功：'.$fileinfo["name"][$i]."\n";
				}else{
					echo '上传失败：'.$fileinfo["name"][$i]."\n";
				}
			}else{
				echo '上传失败，文件大小超出100M或未知：'.$fileinfo["name"][$i]."\n";
			}
		}
	}
}
?>