<?php
require("../config.inc.php");
if(!$arrGroup["会计科目"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='POST'){
	
	$id=mysqli_real_escape_string($mysqli,trim($_POST["id"]));
	
	//会计科目代码必须是数字；
	if(!preg_match("/^\d{4,12}$/",$id)){
		exit('ID必须为4-12位的数字！');
	}

	//审核未审核的基础数据；
	if(isset($_POST["check1"])){
		$strsql='update account set chk="'.$_SESSION["username"].'" where id="'.$id.'" and chk is null';
		//echo $strsql.'<br>';
		$mysqli->query($strsql); 
		$afr=$mysqli->affected_rows;
		if($afr==1){ //如果存在数据；
			echo '审核成功；共有'.$afr.'条记录受到影响；';
		}else{
			echo '审核失败；可能的原因：1>该记录已被审核；';
		}
	}
	//取消本人审核的基础数据；
	if(isset($_POST["check2"])){
		$strsql='update account set chk=null where id="'.$id.'" and chk="'.$_SESSION["username"].'"';
		//echo $strsql.'<br>';
		$mysqli->query($strsql); 
		$afr=$mysqli->affected_rows;
		if($afr==1){ //如果存在数据；
			echo '反审核成功；共有'.$afr.'条记录受到影响；';
		}else{
			echo '反审核失败；可能的原因：1>该记录本身未被审核；2>非同一审核人不能反审核；';
		}
	}
	//审核未审核的全部基础数据；
	if(isset($_POST["check3"])){
		$strsql='update account set chk="'.$_SESSION["username"].'" where chk is null';
		//echo $strsql.'<br>';
		$mysqli->query($strsql); 
		$afr=$mysqli->affected_rows;
		if($afr>0){ //如果存在数据；
			echo '全部审核成功；共有'.$afr.'条记录受到影响；';
		}else{
			echo '全部审核失败；可能的原因：1>全部记录均已被审核；';
		}
	}
	//反审核已审核的全部基础数据；
	if(isset($_POST["check4"])){
		$strsql='update account set chk=null where chk="'.$_SESSION["username"].'"';
		//echo $strsql.'<br>';
		$mysqli->query($strsql); 
		$afr=$mysqli->affected_rows;
		if($afr>0){ //如果存在数据；
			echo '全部反审核成功；共有'.$afr.'条记录受到影响；';
		}else{
			echo '全部反审核失败；可能的原因：1>全部记录均已被审核；';
		}
	}

}else{
	echo '无效的参数调用，请使用正确的方式打开页面！';
}
?>