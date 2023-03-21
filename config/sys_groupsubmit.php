<?php
require("../config.inc.php");
if(!$arrGroup['用户分组']){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

switch($_REQUEST["czgroup"]){
	case "add"://新增组
		//检验该组名称在数据库中是否存在
		$mysqli->query("select * from `groups` where Gname='".$_POST["name"]."'");
		if(mysqli_affected_rows($mysqli)>0){
			echo '该分组已经存在！';
			exit();
		}
		//分解菜单名称字段
		$arrmenu=explode('|',$_POST["menu"]);
		$mysqli->autocommit(false);
		for($i=0;$i<count($arrmenu);$i++){
			$j=$i+1;
			$strsql="insert into `groups`(Gid,Gname,Gcontent,Gvalue) values ($j,'$_POST[name]','$arrmenu[$i]',0)";
			//echo $strsql;
			if(!$mysqli->query($strsql)){
				$mysqli->rollback();
				echo '用户分组添加失败，数据回滚！';
				exit();
			}
		}
		$mysqli->commit();
		$mysqli->autocommit(true);
		echo '添加用户分组成功！';
		break;
	case "updategroup"://生成新的分组权限
		//从权限表中提取不重复的分组记录
		$strsql='select distinct Gname from `groups` order by Gname';
		$sql=$mysqli->query($strsql);
		//分解菜单名称字段
		$arrmenu=explode('|',$_POST["menu"]);
		$mysqli->autocommit(false);
		//重新生成新分组记录前删除所有数据
		if(!$mysqli->query("delete from `groups`")){
			$mysqli->rollback();
			echo '删除用户分组数据失败，数据回滚！';
			exit();
		}
		//循环添加数组
		while($info=$sql->fetch_array(MYSQLI_BOTH)){
			for($i=0;$i<count($arrmenu);$i++){
				$j=$i+1;
				$strsql="insert into `groups`(Gid,Gname,Gcontent,Gvalue) values ($j,'$info[Gname]','$arrmenu[$i]',0)";
				//echo $strsql;
				if(!$mysqli->query($strsql)){
				$mysqli->rollback();
				echo '用户分组添加失败，数据回滚！';
				exit();
				}
			}
		}
		//始终指定管理员权限
		$strsql='update `groups` set Gvalue=-1 where Gname="管理员" and Gcontent like "17%"';
		$mysqli->query($strsql);
		$mysqli->commit();
		$mysqli->autocommit(true);
		echo '生成新的分组权限成功！';
		break;
	case "edit"://修改组
		$mysqli->query("update `groups` set Gname='".$_GET["newname"]."' where Gname='".$_GET["name"]."'");
		if(mysqli_affected_rows($mysqli)>0){
			//同时修改用户表中对应的组名称
			$mysqli->query("update user set Gname='".$_GET["newname"]."' where Gname='".$_GET["name"]."'");
			echo '修改成功！';
		}else{
			echo '修改失败！';
		}
		break;
	case "delete"://删除组
		//删除前验证用户表中该组是否已被使用
		$mysqli->query("select * from user where Gname='".$_GET["name"]."'");
		if(mysqli_affected_rows($mysqli)>0){
			echo '该分组已经被用户表使用，无法删除！';
			exit();
		}
		$mysqli->query("delete from `groups` where Gname='".$_GET["name"]."'");
		if(mysqli_affected_rows($mysqli)>0){
			echo '删除成功！';
		}else{
			echo '删除失败！';
		}
		break;
	case "保存"://修改组权限
		//先将所有的菜单权限赋值为0，再根据已选择的复选框赋值为-1
		$strsql='update `groups` set Gvalue=0 where Gname="'.$_POST["Gname"].'"';
		$mysqli->query($strsql);
		if(isset($_POST["Gvalue"])){
			for($i=0;$i<=count(@$_POST["Gvalue"])-1;$i++){ 
				$arr=explode("|",@$_POST["Gvalue"][$i]);
				$strsql='update `groups` set Gvalue=-1 where Gid="'.$arr[0].'" and Gname="'.$arr[1].'"';
				//echo $strsql;
				$mysqli->query($strsql);
			}
		}
		//对于【管理员】组，始终保持拥有“系统管理”权限
		if($_POST["Gname"]=='管理员'){
			$strsql='update `groups` set Gvalue=-1 where Gname="管理员" and Gcontent like "17%"';
			$mysqli->query($strsql);
		}
		echo '保存成功！';
		break;
	default:
		echo "not request!";
		break;
}
?>