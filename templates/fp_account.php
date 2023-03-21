<?php
require("../config.inc.php");
if(!$arrGroup["发票管理"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$arrid=mysqli_real_escape_string($mysqli,$_POST["id"]);
$jrsum=floatval($_POST["jrsum"]);
$fprq=mysqli_real_escape_string($mysqli,$_POST["fprq"]);
$fphm=mysqli_real_escape_string($mysqli,$_POST["fphm"]);
if(isset($_POST["gysinfo"])){
	$gysinfo=mysqli_real_escape_string($mysqli,$_POST["gysinfo"]);
}

//处理进项发票，对暂估进行冲销处理；
if($_POST["lx"]=='rk'){
	
	$arr=explode('&',$_POST["pzmb"]);//生成凭证时凭证模板数据信息；

	//验证是否已经生成凭证；
	$strsqlquery='select * from chin where id in('.$arrid.') and fphm is not null';
	$mysqli->query($strsqlquery);
	if($mysqli->affected_rows>0){
		exit('验证失败：已生成会计凭证！');
	}

	//验证是否有不同供应商的数据；
	$strsqlquery='select gys,sum(dr) from chin where id in('.$arrid.') group by gys';
	$mysqli->query($strsqlquery);
	if($mysqli->affected_rows>1){
		exit('验证失败：发票源存在不同的供应商！');
	}

	//取出凭证表中最大ID值；
	$sql=$mysqli->query('select max(id) as idMax from v1');
	$info=$sql->fetch_array(MYSQLI_BOTH);
	if($info){
		$id=$info["idMax"]+1;
	}else{
		$id=1;
	}
	$sql->free();
	
	//echo $id.'<br />';
	
	//取出指定期间最大凭证号；
	$date2=explode("-",$arrPublicVar["currentDate"]);
	$qj=$date2[0].(strlen($date2[1])==1?'0'.$date2[1]:$date2[1]);//当前凭证期间值，由4位年份和2位月份组成；
	$sql=$mysqli->query('select max(pzh) as pzhMax from v1 where qj="'.$qj.'"');
	$info=$sql->fetch_array(MYSQLI_BOTH);
	if($info){
		$pzh=$info["pzhMax"]+1;
	}else{
		$pzh=1;
	}
	$sql->free();
	
	//echo $pzh.'<br />';

	$dates=$arrPublicVar["currentDate"];

	$mysqli->autocommit(FALSE);//关闭事务的自动提交；
	$strsqlupdate='update chin set lrrq="'.$arrPublicVar["currentDate"].'",etr="'.$arrPublicVar["username"].'",fprq="'.$fprq.'",fphm="'.$fphm.'",fppzqj="'.$qj.'",fppzh="'.$pzh.'" where id in('.$arrid.')';
	if(!$mysqli->query($strsqlupdate)){
		$mysqli->rollback();
		exit('验证失败：更新入库流水失败！');
	}
	$strsqlquery='select xh,zy,km from v1 where '.$arr[0].' and '.$arr[1];
	$sql=$mysqli->query($strsqlquery);
	while($info=$sql->fetch_array(MYSQLI_BOTH)){
		//判断循环出来的会计科目是否带了供应商核算项目，如果带了，则会计科目加上供应商信息;
		if(getDbField("account","id",$info["km"],"gys")==-1){
			$kmFF=getDbField("account","id",$info["km"],"Fname");
			$kmF=$info["km"]."→".$kmFF."|供应商→".$gysinfo;
		}else{
			$kmF=$info["km"]."→".$kmFF;
		}
		if($info["xh"]==1){
			//第一行数据，第一行带金额
			$strsqlinsert="insert into v1(id,ywrq,lrrq,qj,pzh,xh,zy,km,kmF,slwb,slwbF,dr,cr,etr) values('".$id."','".$dates."','".$dates."','".$qj."','".$pzh."','".$info["xh"]."','".$info["zy"]."','".$info["km"]."','".$kmF."','0','0','".round($jrsum,2)."','0','".$arrPublicVar["username"]."')";
		}else{
			//第二行及后面的数据
			$strsqlinsert="insert into v1(id,ywrq,lrrq,qj,pzh,xh,zy,km,kmF,slwb,slwbF,dr,cr,etr) values('".$id."','".$dates."','".$dates."','".$qj."','".$pzh."','".$info["xh"]."','".$info["zy"]."','".$info["km"]."','".$kmF."','0','0','0','0','".$arrPublicVar["username"]."')";
		}
		if(!$mysqli->query($strsqlinsert)){
			$mysqli->rollback();
			exit('验证失败：生成会计凭证失败！');
		}
		$id++;
	}
	$mysqli->commit();
	echo 'qj='.$qj.'&pzh='.$pzh.'&xh=1';
}

//处理销售发票开票
if($_POST["lx"]=='ck'){
	//验证是否已经生成销售记录；
	$strsqlquery='select * from chout where id in('.$arrid.') and fphm is not null';
	$mysqli->query($strsqlquery);
	if($mysqli->affected_rows>0){
		exit('验证失败：已生成发票记录！');
	}

	//验证是否有不同客户的数据；
	$strsqlquery='select kh,sum(cr) from chout where id in('.$arrid.') group by kh';
	$mysqli->query($strsqlquery);
	if($mysqli->affected_rows>1){
		exit('验证失败：发票源存在不同的客户！');
	}

	//取出指定期间；
	$date2=explode("-",$arrPublicVar["currentDate"]);
	$qj=$date2[0].(strlen($date2[1])==1?'0'.$date2[1]:$date2[1]);//当前凭证期间值，由4位年份和2位月份组成；
	
	$dates=$arrPublicVar["currentDate"];

	$mysqli->autocommit(FALSE);//关闭事务的自动提交；
	$strsqlupdate='update chout set lrrq="'.$arrPublicVar["currentDate"].'",etr="'.$arrPublicVar["username"].'",fprq="'.$fprq.'",fphm="'.$fphm.'",fppzqj="'.$qj.'",fppzh="0" where id in('.$arrid.')';
	if(!$mysqli->query($strsqlupdate)){
		$mysqli->rollback();
		exit('验证失败：更新出库流水失败！');
	}
	$mysqli->commit();
	echo '登记成功！';
}

//处理销售发票取消开票
if($_POST["lx"]=='qxkp'){
	$mysqli->autocommit(FALSE);//关闭事务的自动提交；
	$strsqlupdate='update chout set lrrq=null,etr=null,fprq=null,fphm=null,fppzqj=null,fppzh=null where id in('.$arrid.')';
	//echo $strsqlupdate."\n";
	if(!$mysqli->query($strsqlupdate)){
		$mysqli->rollback();
		exit('验证失败：更新出库流水失败！');
	}
	$mysqli->commit();
	echo '取消成功！';
}
?>