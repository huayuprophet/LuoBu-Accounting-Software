<?php
require("../config.inc.php");
if(!$arrGroup["会计凭证"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$arr1=explode(";",$_GET["arrXM"]);

$arrResult=array();//记录检测结果的数组；

for($i=0;$i<count($arr1);$i++){
	
	$arr2=explode(",",$arr1[$i]);
		
	switch($arr2[0]){
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
		default: //"数量外币",不需要去表中查找数据，只需要确认是数字就可以了:
			$tblName="slwb";
			break;
	}
	
	//当$tblName为数量外币时，向$arrResult数组变量压入1，否则压入0；
	//当$tblName不为数量外币时，再分别查询相关表，看关键字是否存在于表中；
	if($tblName=="slwb"){
		if(is_numeric($arr2[1])){
			array_push($arrResult,1);
		}else{
			array_push($arrResult,0);
		}
	}else{
		$strsql='select * from '.$tblName.' where id="'.$arr2[1].'"';
		$sql=$mysqli->query($strsql);
		$result=$sql->num_rows;
		if($result==1){
			array_push($arrResult,1);
		}else{
			array_push($arrResult,0);
		}
	}
}
	
//该页面向V1传递项目信息是否合符条件提示，acc_yes：表示符合条件，acc_no：表示存在错误；
//当数组$arrResult中出现一次零时，表示核算项目数组有一处存在问题，相应的其他的也不能通过；
if(in_array(0,$arrResult)){
	echo 'acc_no';
}else{
	echo 'acc_yes';
}
?>