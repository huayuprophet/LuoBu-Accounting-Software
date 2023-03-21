<?php
require("../config.inc.php");
if(!$arrGroup[$_GET["tbl"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='GET' && $_GET["drop"]=='删除'){
	
	$id=mysqli_real_escape_string($mysqli,trim($_GET["id"]));
	
	//id验证；
	if(!preg_match($arraySV["az09"][0],$id)){
		exit('ID验证失败：'.$arraySV["az09"][1]);
	}

	//删除前检查数据是否已经审核；
	$strsql='select * from '.$tblName.' where id="'.$id.'" and chk is not null';
	$afr=mysqli_num_rows($mysqli->query($strsql));
	if($afr>0){
		echo '删除失败；原因：该数据已经被审核；';
		exit();
	}

	//删除前检查数据是否有附件存在；
	$strsql='select * from '.$tblName.' where id="'.$id.'" and fj<>0';
	$afr=mysqli_num_rows($mysqli->query($strsql));
	if($afr>0){
		echo '删除失败；原因：该数据存在相应的附件；';
		exit();
	}

	//删除前检查数据是否已经被凭证表使用；
	switch($tblName){
			case "jc_xjll":
				$strsql="select * from v1 where xjll='".$id."'";
				break;
			case "jc_kh":
				$strsql="select * from v1 where kh='".$id."'";
				break;
			case "jc_gr":
				$strsql="select * from v1 where gr='".$id."'";
				break;
			case "jc_ch":
				$strsql="select * from v1 where ch='".$id."'";
				break;	
			case "jc_gys":
				$strsql="select * from v1 where gys='".$id."'";
				break;
			case "jc_bm":
				$strsql="select * from v1 where bm='".$id."'";
				break;
			case "jc_xm":
				$strsql="select * from v1 where xm='".$id."'";
				break;
			default:
				echo '检测到不合法数据，程序中止！';
				exit();
				break;
		}
		
	$sql=$mysqli->query($strsql);
	$afr=$sql->num_rows;
	if($afr>0){
		echo '删除失败；原因：该数据已经被凭证表使用'.$afr.'次；';
		exit();
	}
	$sql->free();

	//删除数据开始；
	$strsql='delete from '.$tblName.' where id="'.$id.'"';
	$mysqli->query($strsql);
	$afr=$mysqli->affected_rows;
	if($afr>0){ //如果存在数据；
		echo '删除成功：共有'.$afr.'条记录受到了影响；';
	}else{
		echo '删除失败；';
	}
	$mysqli->close();
	
}else{
	echo '无效的参数调用，请使用正确的方式打开页面！';
}
?>