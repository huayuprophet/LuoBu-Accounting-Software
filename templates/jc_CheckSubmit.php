<?php
require("../config.inc.php");
if(!$arrGroup[$_GET["tbl"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='GET'){
	
	$id=mysqli_real_escape_string($mysqli,$_GET["id"]);
	
	//id验证；
	if(!preg_match($arraySV["az09"][0],$id)){
		exit('ID验证失败：'.$arraySV["az09"][1]);
	}

	//审核未审核的基础数据；
	if(isset($_GET["check1"])){
		$strsql='update '.$tblName.' set chk="'.$_SESSION["username"].'" where id="'.$id.'" and chk is null';
		//echo $sql.'<br>';
		$check=$mysqli->query($strsql); 
		$afr=$mysqli->affected_rows;
		if($afr==1){ //如果存在数据；
			echo '审核成功；共有'.$afr.'条记录受到影响；';
		}else{
			echo '审核失败；可能的原因：1>该记录已被审核；';
		}
	}
	//取消本人审核的基础数据；
	if(isset($_GET["check2"])){
		$strsql='update '.$tblName.' set chk=null where id="'.$id.'" and chk="'.$_SESSION["username"].'"';
		//echo $sql.'<br>';
		$check=$mysqli->query($strsql); 
		$afr=$mysqli->affected_rows;
		if($afr==1){ //如果存在数据；
			echo '反审核成功；共有'.$afr.'条记录受到影响；';
		}else{
			echo '反审核失败；';
			echo '可能的原因：1>该记录本身未被审核；';
			echo '可能的原因：2>非同一审核人不能反审核；';
		}
	}
	//审核未审核的全部基础数据；
	if(isset($_GET["check3"])){
		$strsql='update '.$tblName.' set chk="'.$_SESSION["username"].'" where chk is null';
		//echo $sql.'<br>';
		$check=$mysqli->query($strsql); 
		$afr=$mysqli->affected_rows;
		if($afr>0){ //如果存在数据；
			echo '全部审核成功；共有'.$afr.'条记录受到影响；';
		}else{
			echo '全部审核失败；可能的原因：1>全部记录均已被审核；';
		}
	}
	
	//取消本人审核的全部基础数据；
	if(isset($_GET["check4"])){
		$strsql='update '.$tblName.' set chk=null where chk="'.$_SESSION["username"].'"';
		//echo $sql.'<br>';
		$check=$mysqli->query($strsql); 
		$afr=$mysqli->affected_rows;
		if($afr>0){ //如果存在数据；
			echo '全部反审核成功；共有'.$afr.'条记录受到影响；';
		}else{
			echo '全部反审核失败；可能的原因：1>全部记录均已被反审核；2>审核与反审核非同一人；';
		}
	}
	$mysqli->close();
	
}else{
	echo '无效的参数调用，请使用正确的方式打开页面！';
}
?>