<?php
require("../config.inc.php");
if(!$arrGroup["会计科目"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='POST' && $_POST["drop"]=='删除'){
	
	$id=mysqli_real_escape_string($mysqli,trim($_POST["id"]));
	
	//会计科目代码必须是数字；
	if(!preg_match("/^\d{4,12}$/",$id)){
		exit('ID必须为4-12位的数字！');
	}

	//删除前检查该记录是否已经审核；
	$strsql='select * from account where id="'.$id.'" and chk is not null';
	$afr=mysqli_num_rows($mysqli->query($strsql));
	if($afr>0){
		echo '删除失败；原因：该科目已经被审核；';
		exit();
	}

	//删除前检查ID是否存在下级；
	$strsql='select * from account where id like "'.$id.'%"';
	$sql=$mysqli->query($strsql);
	$afr=$sql->num_rows;
	if($afr>1){
		echo '删除失败；原因：该科目存在'.($afr-1).'个下级明细数据；';
		exit();
	}
	$sql->free();

	//删除前检查ID是否被凭证表使用；
	$strsql='select * from v1 where km="'.$id.'"';
	$sql=$mysqli->query($strsql);
	$afr=$sql->num_rows;
	if($afr>0){
		echo '删除失败；原因：该科目已经被凭证表使用'.$afr.'次；';
		exit();
	}
	$sql->free();

	//删除数据开始；
	$strsql='delete from account where id="'.$id.'"';
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