<?php
require("../config.inc.php");
/*
英文项目名称对应的中文表名称
*/
if(isset($_GET["tbl"])){
	switch($_GET["tbl"]){
		case "jc_xjll":
			$tblName="现金流量";
			break;
		case "jc_kh":
			$tblName="客户";
			break;
		case "jc_gr":
			$tblName="个人";
			break;
		case "jc_ch":
			$tblName="存货";
			break;	
		case "jc_gys":
			$tblName="供应商";
			break;
		case "jc_bm":
			$tblName="部门";
			break;
		case "jc_xm":
			$tblName="核算项目";
			break;
		default:
			$tblName="";
	}
}

if(!$arrGroup[$tblName]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='GET'){
	
	//安全验证；
	$tbl=mysqli_real_escape_string($mysqli,$_GET["tbl"]);
	$id=mysqli_real_escape_string($mysqli,$_GET["id"]);
	
	//基础数据的停用或启用；
	$strsql='update '.$tbl.' set ty=-1*ty where id="'.$id.'"';
	$mysqli->query($strsql);
	$afr=$mysqli->affected_rows;
	if($afr==1){
		echo '状态设置成功！';
	}
	
}
?>