<?php
require("../config.inc.php");
if(!$arrGroup['用户管理']){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//用户ID验证；
if(!preg_match($arraySV["num4"][0],$_REQUEST["ID"])){
	exit('用户ID验证失败：'.$arraySV["num4"][1]);
}

switch($_REQUEST["czuser"]){
	case "新增"://新增用户
		$id=mysqli_real_escape_string($mysqli,$_POST["ID"]);
		$name=mysqli_real_escape_string($mysqli,$_POST["name"]);
		$telephone=mysqli_real_escape_string($mysqli,$_POST["telephone"]);
		$Gname=mysqli_real_escape_string($mysqli,$_POST["Gname"]);
		//检验该用户是否存在
		$mysqli->query("select * from user where id='".$id."'");
		if(mysqli_affected_rows($mysqli)>0){
			echo '该用户已经存在！';
			exit();
		}
		$strsql='insert into user(id,name,password,telephone,Gname) values("'.$id.'","'.$name.'","'.md5("123456").'","'.$telephone.'","'.$Gname.'")';
		$mysqli->query($strsql);
		if(mysqli_affected_rows($mysqli)>0){
			echo '新增成功！';
		}else{
			echo '新增失败！';
		}
		break;
	case "修改"://修改用户
		$id=mysqli_real_escape_string($mysqli,$_POST["ID"]);
		$name=mysqli_real_escape_string($mysqli,$_POST["name"]);
		$telephone=mysqli_real_escape_string($mysqli,$_POST["telephone"]);
		$Gname=mysqli_real_escape_string($mysqli,$_POST["Gname"]);
		if(@$_POST["resetpwd"]=="0"){
			$strsql='update user set name="'.$name.'",telephone="'.$telephone.'",Gname="'.$Gname.'" where id="'.$id.'"';
		}else{
			$strsql='update user set name="'.$name.'",password="'.md5("123456").'",telephone="'.$telephone.'",Gname="'.$Gname.'" where id="'.$id.'"';
		}
		//echo $strsql;
		$mysqli->query($strsql);
		if(mysqli_affected_rows($mysqli)>0){
			echo '修改成功！';
		}else{
			echo '修改失败！';
		}
		break;
	case "delete"://删除用户
		$id=mysqli_real_escape_string($mysqli,$_GET["ID"]);
		if($id=='1001'){
			echo '系统管理员用户，不允许删除！';
			exit();
		}
		$mysqli->query("delete from user where id='".$id."'");
		if(mysqli_affected_rows($mysqli)>0){
			echo '删除成功！';
		}else{
			echo '删除失败！';
		}
		break;
	case "查看权限"://修改查看凭证权限
		//先将所有的菜单权限赋值为0，再根据已选择的复选框赋值为-1
		$strsql='update `groups` set Rvalue=0 where Gname="'.$_POST["Gname"].'"';
		$mysqli->query($strsql);
		for($i=0;$i<=count($_POST["Rvalue"]);$i++){ 
			$arr=explode("|",$_POST["Rvalue"][$i]);
			$strsql='update `groups` set Rvalue=-1 where Rid="'.$arr[0].'" and Gname="'.$arr[1].'"';
			$mysqli->query($strsql);
		} 
		echo '保存成功！';
		break;
	default:
		echo "not request!";
		break;
}
?>