<?php
require("../config.inc.php");

/*
中文项目名称对应的英文表名称
*/
if(isset($_REQUEST["findSource"])){
	switch($_REQUEST["findSource"]){
		case "现金流量":
			$tblName="jc_xjll";
			break;
		case "客户":
			$tblName="jc_kh";
			break;
		case "个人":
			$tblName="jc_gr";
			break;
		case "存货":
			$tblName="jc_ch";
			break;	
		case "供应商":
			$tblName="jc_gys";
			break;
		case "部门":
			$tblName="jc_bm";
			break;
		case "核算项目":
			$tblName="jc_xm";
			break;
		default:
			$tblName="";
	}
}

$keyword=mysqli_real_escape_string($mysqli,$_REQUEST["keywordSuggestBox"]);//后台接收到的查找关键字
$strSql="select distinct typec from $tblName where typec like '%$keyword%'";

//echo $strSql;
$sql=$mysqli->query($strSql);
//定义一个数组；
$array=array();
while($info=$sql->fetch_array(MYSQLI_ASSOC)){
	//向数组中添加数组，形成一个二维数组；
	array_push($array,$info);
}
$sql->free();
$mysqli->close();
//生成json数据；
echo json_encode($array);
?>