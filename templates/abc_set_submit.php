<?php
/*
成型模板，无需修改，直接套用，如需自定义规则，请仔细阅读以下注释：
1、本文件的命名规则为：abc_set_submit_或者表名.php，请求源来源于：abc_set.php文件。
2、请注意本文件需要操作mysql表的方法，mysql数据表需要定义字符型关键字段 id 。
3、在每一个case结构下面，会有一段“自动代码”，如果需要自动管理数据表，请不要随意修改。
*/
require("../config.inc.php");
if(!$arrGroup[$_REQUEST["rht"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
//print_r($_REQUEST);
try{
	
	$tblName = mysqli_real_escape_string($mysqli,$_REQUEST["tbl"]);
	$id = mysqli_real_escape_string($mysqli,$_REQUEST["id"]);
	
	switch($_REQUEST["cz"]){
		case "Add":
		case "Copy":
			//以下可以自行添加逻辑代码；

			//以下代码为自动代码，请勿随意修改；
			$f1='';$v1='';
			foreach($_REQUEST as $key=>$value){
				if($key=='rht'){
					break; //终止循环
				}
				if($f1==''){
					$f1=$key;
				}else{
					$f1=$f1.','.$key;
				}
				if($v1==''){
					$v1='\''.$value.'\'';
				}else{
					$v1=$v1.',\''.$value.'\'';
				}
			}
			$strsqladd="insert into ".$tblName."($f1) values($v1)";
			//echo $strsqladd."\n";
			$mysqli->query($strsqladd);
			$afr=$mysqli->affected_rows;
			if($afr>0){
				echo '操作成功！';
			}else{
				echo '操作失败！';
			}
			break;
		case "Edit":
			//以下可以自行添加逻辑代码；

			//以下代码为自动代码，请勿随意修改；
			$fv='';
			foreach($_REQUEST as $key=>$value){
				if($key=='rht'){
					break; //终止循环
				}
				if($key=='id'){
					continue; //跳出本次循环
				}
				if($fv==''){
					$fv=$key.'=\''.$value.'\'';
				}else{
					$fv .= ','.$key.'=\''.$value.'\'';
				}
			}
			$strsqledit='update '.$tblName.' set '.$fv.' where id="'.$id.'"';
			//echo $strsqledit."\n";
			$mysqli->query($strsqledit);
			$afr=$mysqli->affected_rows;
			if($afr>0){
				echo '操作成功！';
			}else{
				echo '操作失败！';
			}
			break;
		case "Delete":
			//以下可以自行添加逻辑代码；
			
			//以下代码为自动代码，请勿随意修改；
			$strsqldelete='delete from '.$tblName.' where id="'.$id.'"';
			//echo $strsqldelete."\n";
			$mysqli->query($strsqldelete);
			$afr=$mysqli->affected_rows;
			if($afr>0){
				echo '操作成功！';
			}else{
				echo '操作失败！';
			}
			break;
		default:
			echo "not request!";
			break;
	}
	
}catch (Exception $e) {
    echo $e->getMessage();
    die(); // 终止异常
}
?>