<?php 
require("../config.inc.php");
if(!$arrGroup["成本计算"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$qj1=mysqli_real_escape_string($mysqli,$_GET["qj1"]);
//$km1=mysqli_real_escape_string($mysqli,$_GET["km1"]);
//$ch1=mysqli_real_escape_string($mysqli,$_GET["ch1"]);
//$ch2=mysqli_real_escape_string($mysqli,$_GET["ch2"]);
//$mb1=mysqli_real_escape_string($mysqli,$_GET["mb1"]);
$mb2=mysqli_real_escape_string($mysqli,$_GET["mb2"]);

//检查会计期间是否记账；
$sql=$mysqli->query('select qj from V1 where qj="'.$qj1.'" and acc is not null');
$info=$sql->fetch_array(MYSQLI_BOTH);
if($info){
	echo '该会计期间已记账，无法进行凭证生成！';
	exit();
}

//检查目标会计科目是否带数量外币和存货核算；
$sql=$mysqli->query('select id from account where id="'.$mb2.'" and slwb=-1 and ch=-1');
$info=$sql->fetch_array(MYSQLI_BOTH);
if(!$info){
	echo '会计科目不带数量外币和存货核算，请检查！';
	exit();
}

//关闭当前自动提交事务；
$mysqli->autocommit(FALSE);

//成本自动计算，在 $qj1 的情况下，把 $mb2 科目的借方数写入贷方；
$mysqli->query("call count_cost('".$qj1."','".$mb2."')");

//找平会计凭证，并取消对应会计凭证的审核；
$strsql="select qj,pzh,sum(dr) as drs,sum(cr) as crs from v1 group by qj,pzh having qj='$qj1' and drs<>crs";
$sql=$mysqli->query($strsql);
while($info=$sql->fetch_array(MYSQLI_BOTH)){
	$mysqli->query('update V1 set dr="'.$info["crs"].'" where qj="'.$qj1.'" and pzh="'.$info["pzh"].'" and dr<>0');//找平凭证
	$mysqli->query('update V1 set chk=null where qj="'.$qj1.'" and pzh="'.$info["pzh"].'"');//取消审核
}

$mysqli->commit();//自动提交事务；

echo '成本计算完成，请手工检查！';
?>