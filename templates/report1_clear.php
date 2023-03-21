<?php
require("../config.inc.php");
if(!$arrGroup["财务报表"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//清空所有新生成的报表临时文件；
$path = $arrPublicVar["filesystems"].'_db/report_*.xlsx';
if(count(glob($path))==0){
	echo '无临时文件！';
}else{
	echo "获取".count(glob($path))."个计算文件：\n";
	foreach(glob($path) as $filename){
		if(unlink($filename)){
			echo "清除".substr($filename,-29)."成功\n";
		}
	}
	echo "获取1个报表下载文件：\n";
	$tempUrl='../templates/access/report_'.$arrPublicVar["userid"].'.xlsx';
	unlink($tempUrl);
	echo "清除报表下载文件成功";
}
?>