<?php
require("../config.inc.php");
if(!$arrGroup["年度结转"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

/*
实现不同数据库表之间数据的复制，参考：https://www.cnblogs.com/chLxq/p/11429561.html
*/
if(($_SERVER['REQUEST_METHOD']=='POST')&&isset($_POST["db1"])&&isset($_POST["tj"])){

	$db0=$arrPublicVar["db"];
	$db1=mysqli_real_escape_string($mysqli,$_POST["db1"]);
	$tj=mysqli_real_escape_string($mysqli,$_POST["tj"]);
	
	//检验目标源是否指定；
	if($db1==null) exit('必须指定目标源数据库！');
	
	//检验数据库是否一致；
	if($db0==$db1) exit('数据库一致，程序中止！');
	
	//执行结转功能；
	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
	
	//首先检测目标数据库中 v1 表中是否有记录；
	$mysqli->query("select * from $db1.v1");
	if($mysqli->affected_rows>0){
		exit('目标数据库不符合初始化条件！');
	}
	
	$callStartTime = microtime(true);
	
	echo "执行时间          执行内容" , EOL;
	
	//基础的表直接拷贝；
	$arrtbl=array("account","chin","chout","groups","info","jc_bm","jc_ch","jc_fj","jc_gr","jc_gys","jc_kh","jc_xjll","jc_xm","user");
	foreach($arrtbl as $tblname){
		$mysqli->query("delete from $db1.$tblname");
		if($mysqli->query("insert into $db1.$tblname select * from $db0.$tblname")){
			echo date('H:i:s'), ' '.$tblname, ' 拷贝成功' , EOL;
		}else{
			echo '<font color="red">' , date('H:i:s'), ' '.$tblname, ' 拷贝失败</font>', EOL;
		}
	}

	//入出库表删除数据历史数据；
	$mysqli->query("delete from $db1.chin where fphm is not null");
	$mysqli->query("delete from $db1.chout where fphm is not null");

	//凭证数据的初始化；
	if($mysqli->query("create temporary table vtemp select * from v1")){
		echo date('H:i:s'), ' vtemp', ' 创建成功' , EOL;
	}else{
		echo date('H:i:s'), ' vtemp', ' 创建失败' , EOL;
	}
	
	//根据会计科目的方向来修改 vtemp 表中的数据；
	$sql=$mysqli->query("select id,dc from account where id in(select distinct km as id from v1) order by id");
	while($info=$sql->fetch_array(MYSQLI_BOTH)){
		//echo $info["id"], $info["dc"] , EOL;
		$km=$info["id"];
		if($info["dc"]=='借方'){
			$strsql="update vtemp set slwb=-1*slwb,dr=-1*cr,cr=0 where km='$km' and dr=0";
		}else if($info["dc"]=='贷方'){
			$strsql="update vtemp set slwb=-1*slwb,cr=-1*dr,dr=0 where km='$km' and cr=0";
		}else{
			echo '科目借贷方向有一处错误！' , EOL;
		}
		//echo $strsql , EOL;
		$mysqli->query($strsql);
	}
	echo date('H:i:s'), ' vtemp', ' 修改成功' , EOL;
	
	//取源表中按期间和凭证号排序最后一条记录；
	$sql=$mysqli->query("select * from v1 order by lrrq desc limit 1");
	$info=$sql->fetch_array(MYSQLI_BOTH);
	$ywrq=$info["ywrq"];
	$lrrq=$info["lrrq"];
	$qj=$info["qj"];
	$etr=$arrPublicVar["username"];
	
	$strsql="select km,kmF,xjll,xjllF,kh,khF,gr,grF,ch,chF,gys,gysF,bm,bmF,xm,xmF,";
	$strsql.="round(sum(slwb),2) as slwb,round(sum(dr),2) as dr,round(sum(cr),2) as cr from vtemp group by kmF order by km";
	$sql=$mysqli->query($strsql);
	$i=1;
	
	//关闭自动提交事务；
	$mysqli->autocommit(FALSE);
	
	//循环所有临时表中的记录并写入到新表中；
	while($info=$sql->fetch_array(MYSQLI_BOTH)){
		if(!($info["dr"]==0&&$info["cr"]==0)){	
			$km=$info["km"];$kmF=$info["kmF"];
			$xjll=$info["xjll"];$xjllF=$info["xjllF"];
			$kh=$info["kh"];$khF=$info["khF"];
			$gr=$info["gr"];$grF=$info["grF"];
			$ch=$info["ch"];$chF=$info["chF"];
			$gys=$info["gys"];$gysF=$info["gysF"];
			$bm=$info["bm"];$bmF=$info["bmF"];
			$xm=$info["xm"];$xmF=$info["xmF"];
			$slwb=$info["slwb"];$dr=$info["dr"];$cr=$info["cr"];
			if($slwb==0){
				$slwbF=0;
			}else{
				$slwbF=round($dr==0?$cr/$slwb:$dr/$slwb,4);
			}
		
			$strsqlinsert="insert into $db1.v1(id,ywrq,lrrq,qj,pzh,xh,zy,km,kmF,slwb,slwbF,dr,cr,xjll,xjllF,kh,khF,gr,grF,ch,chF,gys,gysF,bm,bmF,xm,xmF,etr,chk,acc,fj) ";
			$strsqlinsert.="values($i,'$ywrq','$lrrq','$qj',1,$i,'初始化','$km','$kmF',$slwb,$slwbF,$dr,$cr,'$xjll','$xjllF','$kh','$khF','$gr','$grF','$ch','$chF','$gys','$gysF','$bm','$bmF','$xm','$xmF','$etr',NULL,NULL,0)";
			
			//echo $strsqlinsert , EOL;
			
			if(!$mysqli->query($strsqlinsert)){
				echo '<font color="red">'.date('H:i:s'), ' vtemp', ' 添加失败</font>' , EOL;
				$mysqli->rollback();
				break;
			}
			
			$i++;
		}
	}
	
	$mysqli->commit();//自动提交事务；
	
	echo date('H:i:s'), ' 初始化凭证数据结束' , EOL;
	
	$callEndTime = microtime(true);
	$callTime = $callEndTime - $callStartTime;
	echo '以上工作全部完成总共耗时：' , sprintf('%.4f',$callTime) , " 秒" , EOL;
	
}else{
	echo '非法操作！';
}
?>