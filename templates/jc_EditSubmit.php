<?php
require("../config.inc.php");
if(!$arrGroup[$_GET["tbl"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='GET' && $_GET['edit']=='修改'){

	$oldid=mysqli_real_escape_string($mysqli,trim($_GET["oldid"]));//原记录ID
	$newid=mysqli_real_escape_string($mysqli,trim($_GET["newid"]));//新记录ID
	$name=mysqli_real_escape_string($mysqli,trim($_GET["name"]));
	$typec=mysqli_real_escape_string($mysqli,trim($_GET["typec"]));
	
	//id验证；
	if(!preg_match($arraySV["az09"][0],$newid)){
		exit('ID验证失败：'.$arraySV["az09"][1]);
	}
	
	//验证名称是否包含匹配字符；
	foreach($fieldValidation as $yz){
		if(substr_count($name,$yz)!=0){
			echo '验证失败；名称中不能出现 '.$yz.' 字符';
			exit();
		}
	}

	//获取数据修改前名称字段值；
	$strsql='select name,typec from '.$tblName.' where id="'.$oldid.'"';
	$oldsql=$mysqli->query($strsql);
	$olddata=$oldsql->fetch_array(MYSQLI_BOTH);

	//检测新旧名称、分类是否发生变动；
	if($oldid==$newid){
		if($_GET["name"]==$olddata["name"]&&$_GET["typec"]==$olddata["typec"]){
			echo '未检测到有需要修改的数据！';
			exit();
		}
	}
	
	//修改前检查该记录是否已经审核；
	$strsql='select * from '.$tblName.' where id="'.$oldid.'" and chk is not null';
	$afr=mysqli_num_rows($mysqli->query($strsql));
	if($afr>0){
		echo '修改失败；原因：该数据已经被审核；';
		exit();
	}

	//保存数据开始；
	$strsql='update '.$tblName.' set id="'.$newid.'",name="'.$name.'",typec="'.$typec.'",etr="'.$_SESSION["username"].'" where id="'.$oldid.'"';
	$update=$mysqli->query($strsql);
	if($update){ //如果存在数据；
		echo '修改成功；';
		//构造相关变量；
		$zd=substr($tblName,3,strlen($tblName));
		$zdf=$zd.'F';
		$oldxmName=@$_GET["tbl"].'→'.$_GET["oldid"].'→'.$olddata["name"];
		$newxmName=@$_GET["tbl"].'→'.$_GET["newid"].'→'.$_GET["name"];
		//更新凭证表中的相关数据；
		$strsql='update v1 set '.$zd.'=replace('.$zd.',"'.$_GET["oldid"].'","'.$_GET["newid"].'"),'.$zdf.'=replace('.$zdf.',"'.$olddata["name"].'","'.$_GET["name"].'"),kmF=replace(kmF,"'.$oldxmName.'","'.$newxmName.'") where '.$zd.'="'.$_GET["oldid"].'"';
		//echo '<br>'.$strsql;
		$mysqli->query($strsql);
		$afr=$mysqli->affected_rows;
		if($afr>0){
			echo "\n".'凭证表更新成功：共有'.$afr.'条记录受到影响；';
		}else{
			echo "\n".'凭证表未更新，原因：该记录凭证表未使用；';
		}
		//更新记账表中的相关数据；
		$strsql='update v2 set kmF=replace(kmF,"'.$oldxmName.'","'.$newxmName.'")';
		//echo '<br>'.$strsql;
		$mysqli->query($strsql);
		$afr=$mysqli->affected_rows;
		if($afr>0){
			echo "\n".'记账表更新成功：共有'.$afr.'条记录受到影响；';
		}else{
			echo "\n".'记账表未更新，原因：该记录记账表未使用；';
		}
	}else{
		echo '修改失败；';
	}
	$oldsql->free();
	$mysqli->close();
	
}else{
	echo '无效的参数调用，请使用正确的方式打开页面！';
}
?>