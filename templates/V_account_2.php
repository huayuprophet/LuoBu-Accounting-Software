<?php
require("../config.inc.php");
if(!$arrGroup["序时账簿"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$yzqj=mysqli_real_escape_string($mysqli,$_POST["qj"]);//验证期间；

//防止重复记账；
$sql=$mysqli->query('select * from v2 where qj="'.$yzqj.'"');
$afr=$sql->num_rows;
if($afr>0){
	exit('记账失败，原因：该期间'.$yzqj.'已经记账！');
}
$sql->free();

//记账前必须保证该期间内的所有凭证都已审核；
$sql=$mysqli->query('select * from v1 where qj="'.$yzqj.'" and chk is null');
$afr=$sql->num_rows;
if($afr>0){
	exit('记账失败，原因：该期间'.$yzqj.'存在未审核的凭证！');
}
$sql->free();

//记账前检查该期间内的凭证是否存在断号现象；
$sql=$mysqli->query('select distinct pzh from v1 where qj="'.$yzqj.'" order by pzh');
$info=$sql->fetch_array(MYSQLI_BOTH);
if($info){
    $i=1;
	DO{
		if($info["pzh"]!=$i){
			exit('记账失败，原因：该期间缺失['.$i.']号凭证，请检查！');
		}
		$i++;
	}while($info=$sql->fetch_array(MYSQLI_BOTH));
}
$sql->free();

//以下是调用mysql的存储过程来完成相应的记录操作；
if($mysqli->query("call copyv1tov2('".$yzqj."','".$_SESSION["username"]."')")){
	echo 'success';
}else{
	echo 'no success';
}
?>