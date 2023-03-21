<?php
require("../config.inc.php");
if(!$arrGroup["序时账簿"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//句柄检测；
if($_GET["handle"]!='deleteV'){
	echo '句柄错误！';
	exit();
}

$qj=mysqli_real_escape_string($mysqli,$_GET["qj"]);
$pzh=mysqli_real_escape_string($mysqli,$_GET["pzh"]);

//凭证附件检测；
$strsql='select * from V1 where qj="'.$qj.'" and pzh="'.$pzh.'" and fj<>0';
$mysqli->query($strsql);
$afr=$mysqli->affected_rows;
if($afr>0){
	echo '请先妥善处理凭证附件后再进行删除操作！';
	exit();
}

$strsql='delete from V1 where qj="'.$qj.'" and pzh="'.$pzh.'" and chk is null';
$mysqli->query($strsql);
$afr=$mysqli->affected_rows;
if($afr>0){ //如果存在数据；

	//删除凭证前，如关联到入库流水，则清除入库流水上的信息；
	$strsqlupdate='update chin set lrrq=NULL,etr=NULL,fprq=NULL,fphm=NULL,fppzqj=NULL,fppzh=NULL where fppzqj="'.$qj.'" and fppzh="'.$pzh.'"';
	$mysqli->query($strsqlupdate);

	echo '删除成功：'.$qj.'—'.$pzh."#\n";
	echo '程序将自动点击“提交”按钮刷新序时簿；';
}else{
	echo '删除失败:'.$qj.'→'.$pzh."\n";
	echo '失败可能的原因：'."\n";
	echo '1>相应的凭证已经删除；'."\n";
	echo '2>将删除的凭证已审核；';
}
$mysqli->close();
?>