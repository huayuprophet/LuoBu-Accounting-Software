<?php
require("../config.inc.php");
if(!$arrGroup["发票管理"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$oldid=mysqli_real_escape_string($mysqli,$_GET["id"]);
$slwb=round(mysqli_real_escape_string($mysqli,$_GET["slr"]),2);//数量精确到2位小数；

//echo $slwb."\n";

if($_GET["lx"]=='rk'){
	$newid=getNextNumber("chin","id");
	$strsql='select * from chin where id="'.$oldid.'" and fphm is null';
	$sql=$mysqli->query($strsql);
	$info=$sql->fetch_array(MYSQLI_BOTH);
	if($info){//如果满足条件的记录存在；
		$strsqladd='INSERT INTO chin(id,qj,pzh,ywrq,km,kmF,gys,gysF,ch,chF,xm,xmF,slwb,slwbF,dr,zy,childid) VALUES ("'.$newid.'","'.$info["qj"].'","'.$info["pzh"].'","'.$info["ywrq"].'","'.$info["km"].'","'.$info["kmF"].'","'.$info["gys"].'","'.$info["gysF"].'","'.$info["ch"].'","'.$info["chF"].'","'.$info["xm"].'","'.$info["xmF"].'","'.$slwb.'","'.$info["slwbF"].'","'.round($slwb * $info["slwbF"],2).'","'.$info["zy"].'","'.$info["childid"].'")';
		$strsqledit='update chin set slwb="'.($info["slwb"]-$slwb).'",dr="'.($info["dr"]-round($slwb * $info["slwbF"],2)).'" where id="'.$oldid.'"';
		//echo $strsqladd."\n".$strsqledit;
		$mysqli->autocommit(FALSE);//关闭事务的自动提交；
		if(!$mysqli->query($strsqladd)){
			$mysqli->rollback();
			exit('拆分新数据出错！');
		}
		if(!$mysqli->query($strsqledit)){
			$mysqli->rollback();
			exit('拆分原数据出错！');
		}
		$mysqli->commit();
		echo '拆分成功，请刷新！';
	}
}else{
	$newid=getNextNumber("chout","id");
	$strsql='select * from chout where id="'.$oldid.'" and fphm is null';
	$sql=$mysqli->query($strsql);
	$info=$sql->fetch_array(MYSQLI_BOTH);
	if($info){//如果满足条件的记录存在；
		$strsqladd='INSERT INTO chout(id,qj,pzh,ywrq,km,kmF,kh,khF,ch,chF,xm,xmF,slwb,slwbF,cr,zy,childid) VALUES ("'.$newid.'","'.$info["qj"].'","'.$info["pzh"].'","'.$info["ywrq"].'","'.$info["km"].'","'.$info["kmF"].'","'.$info["kh"].'","'.$info["khF"].'","'.$info["ch"].'","'.$info["chF"].'","'.$info["xm"].'","'.$info["xmF"].'","'.$slwb.'","'.$info["slwbF"].'","'.round($slwb * $info["slwbF"],2).'","'.$info["zy"].'","'.$info["childid"].'")';
		$strsqledit='update chout set slwb="'.($info["slwb"]-$slwb).'",cr="'.($info["cr"]-round($slwb * $info["slwbF"],2)).'" where id="'.$oldid.'"';
		//echo $strsqladd."\n".$strsqledit;
		$mysqli->autocommit(FALSE);//关闭事务的自动提交；
		if(!$mysqli->query($strsqladd)){
			$mysqli->rollback();
			exit('拆分新数据出错！');
		}
		if(!$mysqli->query($strsqledit)){
			$mysqli->rollback();
			exit('拆分原数据出错！');
		}
		$mysqli->commit();
		echo '拆分成功，请刷新！';
	}
}
?>