<?php
require("../config.inc.php");
if(!empty($_POST["txtpassword1"]) && !empty($_POST["txtpassword2"])){
	
	//旧密码验证；
	if(!preg_match($arraySV["pwd"][0],$_POST["txtpassword1"])){
		exit('旧密码验证失败：'.$arraySV["pwd"][1]);
	}
	
	//新密码验证；
	if(!preg_match($arraySV["pwd"][0],$_POST["txtpassword2"])){
		exit('新密码验证失败：'.$arraySV["pwd"][1]);
	}
	
	$oldpwd=md5($_POST["txtpassword1"]);
	$newpwd=md5($_POST["txtpassword2"]);
	
	$sql=$mysqli->query('select * from user where id="'.$_SESSION["userid"].'" and password="'.$oldpwd.'"');
	$info=$sql->fetch_array(MYSQLI_BOTH);
	if(!$info){
		echo '您输入的旧密码不正确；';
	}else{
		$updateSql=$mysqli->query('update user set password="'.$newpwd.'" where id="'.$_SESSION["userid"].'"');
		if($updateSql){
			echo '密码修改成功；';
		}else{
			echo '密码修改失败；';
		}
	}
	$sql->free();
	$mysqli->close();
	
}else{
	echo '无效的参数调用，请使用正确的方式打开页面！';
}
?>