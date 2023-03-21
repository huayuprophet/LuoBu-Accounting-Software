<?php
require("../config.inc.php");
if(!$arrGroup[$_GET["tbl"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='GET' && ($_GET['insert']=='新增' || $_GET['insert']=='复制')){

	$id=mysqli_real_escape_string($mysqli,trim($_GET["id"]));
	$name=mysqli_real_escape_string($mysqli,trim($_GET["name"]));
	$typec=mysqli_real_escape_string($mysqli,trim($_GET["typec"]));
	
	//id验证；
	if(!preg_match($arraySV["az09"][0],$id)){
		exit('ID验证失败：'.$arraySV["az09"][1]);
	}

	//验证名称是否包含匹配字符；
	foreach($fieldValidation as $yz){
		if(substr_count($name,$yz)!=0){
			echo '验证失败；原因：名称中不能出现 '.$yz.' 字符';
			exit();
		}
	}

	//新增数据开始；
	$strsql='insert into '.$tblName.'(id,name,typec,etr) values("'.$id.'","'.$name.'","'.$typec.'","'.$_SESSION["username"].'")';
	$insert=$mysqli->query($strsql);

	if($insert){ //如果存在数据；
		echo '新增成功；';
	}else{
		echo '新增失败；';
	}
	$mysqli->close();
	
}else{
	echo '无效的参数调用，请使用正确的方式打开页面！';
}
?>