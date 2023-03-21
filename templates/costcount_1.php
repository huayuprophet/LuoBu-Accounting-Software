<?php 
require("../config.inc.php");
if(!$arrGroup["成本计算"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$qj1=mysqli_real_escape_string($mysqli,$_GET["qj1"]);
$km1=mysqli_real_escape_string($mysqli,$_GET["km1"]);
$ch1=mysqli_real_escape_string($mysqli,$_GET["ch1"]);
$ch2=mysqli_real_escape_string($mysqli,$_GET["ch2"]);
$mb1=mysqli_real_escape_string($mysqli,$_GET["mb1"]);
$mb2=mysqli_real_escape_string($mysqli,$_GET["mb2"]);

//检查三个会计科目是否一样；
if($km1==$mb1||$km1==$mb2||$mb1==$mb2){
	echo '会计科目指定有误，请检查！';
	exit();
}

//检查会计期间是否记账；
$sql=$mysqli->query('select qj from V1 where qj="'.$qj1.'" and acc is not null');
$info=$sql->fetch_array(MYSQLI_BOTH);
if($info){
	echo '该会计期间已记账，无法进行凭证生成！';
	exit();
}

//检查源会计科目是否带数量外币和存货核算；
$sql=$mysqli->query('select id from account where id="'.$km1.'" and slwb=-1 and ch=-1');
$info=$sql->fetch_array(MYSQLI_BOTH);
if(!$info){
	echo '会计科目不带数量外币和存货核算，请检查！';
	exit();
}

//检查目标会计科目是否带数量外币和存货核算；
$sql=$mysqli->query('select id from account where id="'.$mb1.'" and slwb=-1 and ch=-1');
$info=$sql->fetch_array(MYSQLI_BOTH);
if(!$info){
	echo '会计科目不带数量外币和存货核算，请检查！';
	exit();
}

//检查目标会计科目是否带数量外币和存货核算；
$sql=$mysqli->query('select id from account where id="'.$mb2.'" and slwb=-1 and ch=-1');
$info=$sql->fetch_array(MYSQLI_BOTH);
if(!$info){
	echo '会计科目不带数量外币和存货核算，请检查！';
	exit();
}

//检查会计期间和会计科目是否在凭证表中存在；
$sql=$mysqli->query('select qj,km from V1 where qj="'.$qj1.'" and km="'.$km1.'"');
$info=$sql->fetch_array(MYSQLI_BOTH);
if(!$info){
	echo '凭证表中不存在相关数据！';
	exit();
}

//获取凭证表中下一个凭证号；
$sql=$mysqli->query('select max(pzh) as pzhMax from V1 where qj="'.$qj1.'"');
$info=$sql->fetch_array(MYSQLI_BOTH);
if($info){
	$pzh=$info["pzhMax"]+1;
}else{
	$pzh=1;
}

//关闭当前自动提交事务；
$mysqli->autocommit(FALSE);

//构造查询语句，提取数据，并以此加入到数据库中；
/*
//单条记录
if($ch1==""&&$ch2!=""){
	$strsql="select * from V1 where qj='$qj1' and km='$km1' and ch<='$ch2' order by pzh";
}elseif($ch1!=""&&$ch2==""){
	$strsql="select * from V1 where qj='$qj1' and km='$km1' and ch>='$ch1' order by pzh";
}elseif($ch1==""&&$ch2==""){
	$strsql="select * from V1 where qj='$qj1' and km='$km1' order by pzh";
}else{
	$strsql="select * from V1 where qj='$qj1' and km='$km1' and ch between '$ch1' and '$ch2' order by pzh";
}
*/

if($ch1==""&&$ch2!=""){
	$strsql="select zy,ch,chF,sum(slwb) as slwb,sum(slwbF) as slwbF from V1 where qj='$qj1' and km='$km1' and ch<='$ch2' group by ch order by ch";
}elseif($ch1!=""&&$ch2==""){
	$strsql="select zy,ch,chF,sum(slwb) as slwb,sum(slwbF) as slwbF from V1 where qj='$qj1' and km='$km1' and ch>='$ch1' group by ch order by ch";
}elseif($ch1==""&&$ch2==""){
	$strsql="select zy,ch,chF,sum(slwb) as slwb,sum(slwbF) as slwbF from V1 where qj='$qj1' and km='$km1' group by ch order by ch";
}else{
	$strsql="select zy,ch,chF,sum(slwb) as slwb,sum(slwbF) as slwbF from V1 where qj='$qj1' and km='$km1' and ch between '$ch1' and '$ch2' group by ch order by ch";
}

$sql=$mysqli->query($strsql);
$id=getNextNumber("V1","id");
$i=1;
$ywrq=$arrPublicVar["currentDate"];
$lrrq=$arrPublicVar["currentDate"];
$etr=$arrPublicVar["username"];
while($info=$sql->fetch_array(MYSQLI_BOTH)){
	//向借方写一条数据；
	$newkm=$mb1.'→'.getDbField("account","id",$mb1,"Fname").'|数量外币→'.$info['slwb'].'|存货→'.$info['ch'].'→'.$info['chF'];
	$strsqlinsert="insert into v1(id,ywrq,lrrq,qj,pzh,xh,zy,km,kmF,slwb,slwbF,dr,cr,xjll,xjllF,kh,khF,gr,grF,ch,chF,gys,gysF,bm,bmF,xm,xmF,etr,chk,acc,fj)";
	$strsqlinsert.=" values($id,'$ywrq','$lrrq',$qj1,$pzh,$i,'$info[zy]','$mb1','$newkm',$info[slwb],$info[slwbF],1,0,";
	$strsqlinsert.="NULL,NULL,NULL,NULL,NULL,NULL,'$info[ch]','$info[chF]',NULL,NULL,NULL,NULL,NULL,NULL,'$etr',NULL,NULL,0)";
	if($mysqli->query($strsqlinsert)){
		$i++;
		$id++;
	}else{
		$mysqli->rollback();
		exit('后台提示：凭证生成失败，插入数据失败！');
	}
	//向贷方写一条数据；
	$newkm=$mb2.'→'.getDbField("account","id",$mb2,"Fname").'|数量外币→'.$info['slwb'].'|存货→'.$info['ch'].'→'.$info['chF'];
	$strsqlinsert="insert into v1(id,ywrq,lrrq,qj,pzh,xh,zy,km,kmF,slwb,slwbF,dr,cr,xjll,xjllF,kh,khF,gr,grF,ch,chF,gys,gysF,bm,bmF,xm,xmF,etr,chk,acc,fj)";
	$strsqlinsert.=" values($id,'$ywrq','$lrrq',$qj1,$pzh,$i,'$info[zy]','$mb2','$newkm',$info[slwb],$info[slwbF],0,1,";
	$strsqlinsert.="NULL,NULL,NULL,NULL,NULL,NULL,'$info[ch]','$info[chF]',NULL,NULL,NULL,NULL,NULL,NULL,'$etr',NULL,NULL,0)";
	if($mysqli->query($strsqlinsert)){
		$i++;
		$id++;
	}else{
		$mysqli->rollback();
		exit('后台提示：凭证生成失败，插入数据失败！');
	}
}

//成本自动计算，在 $qj1 的情况下，把 $mb2 科目的借方数写入贷方；
$mysqli->query("call count_cost('".$qj1."','".$mb2."')");

//凭证自动找平；
$strsql="select * from V1 where qj='$qj1' and pzh=$pzh order by xh desc";
$sql=$mysqli->query($strsql);
while($info=$sql->fetch_array(MYSQLI_BOTH)){
	if($info["xh"]%2==0){
		$crjr=$info["cr"];
	}else{
		$mysqli->query("update V1 set dr=$crjr where qj='$qj1' and pzh=$pzh and xh=$info[xh]");
	}
}

$mysqli->commit();//自动提交事务；

echo '凭证生成成功！';
?>