<?php
require("../config.inc.php");
if(!$arrGroup["序时账簿"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//句柄检测；
if($_GET["handle"]!='checkV'){
	echo '句柄错误！';
	exit();
}

$qj=mysqli_real_escape_string($mysqli,$_GET["qj"]);
$pzh=mysqli_real_escape_string($mysqli,$_GET["pzh"]);

$strsql='select chk from v1 where qj="'.$qj.'" and pzh="'.$pzh.'" and acc is null';
$sql=$mysqli->query($strsql);
$info=$sql->fetch_array(MYSQLI_BOTH);
if($info){//如果满足条件的记录存在；
	if(is_null($info["chk"])){ //如果凭证未审核；

		//采用逐行逐字段的对凭证数据进行验证，思路：将会计科目装载于二维数组中，每次验证调取二维数组进行比对；

		//第一步装载会计科目；
		$arracc=array();
		$strsqlquery='select id,slwb,xjll,kh,gr,ch,gys,bm,xm from account';
		$sqlacc=$mysqli->query($strsqlquery);
		while($infoacc=$sqlacc->fetch_array(MYSQLI_BOTH)){
			$arracc[$infoacc["id"]]=array("slwb"=>$infoacc["slwb"],"xjll"=>$infoacc["xjll"],"kh"=>$infoacc["kh"],"gr"=>$infoacc["gr"],"ch"=>$infoacc["ch"],"gys"=>$infoacc["gys"],"bm"=>$infoacc["bm"],"xm"=>$infoacc["xm"]);
		}
		 
		//第二步验证会计凭证数据；
		$sumdr=0;$sumcr=0;
		$strsqlquery='select * from v1 where qj="'.$qj.'" and pzh="'.$pzh.'" order by xh asc';
		$sqlv=$mysqli->query($strsqlquery);
		while($infov=$sqlv->fetch_array(MYSQLI_BOTH)){
			//未级科目验证；
			$strsqlchildquery='select id from account where id like "'.$infov["km"].'%"';
			$mysqli->query($strsqlchildquery);
			if($mysqli->affected_rows!=1){
				exit('第['.$infov["xh"].']行：会计科目非未级科目！');
			}
			//字段非空验证；
			if($arracc[$infov["km"]]["slwb"]==-1 && $infov["slwb"]==0){
				exit('第['.$infov["xh"].']行：数量外币检验失败！');
			}
			if($arracc[$infov["km"]]["xjll"]==-1){
				if(is_null($infov["xjll"])){
					exit('第['.$infov["xh"].']行：现金流量空值检验失败！');
				}else{
					if(!getValueExist(array("jc_xjll","id",$infov["xjll"]))){
						exit('第['.$infov["xh"].']行：现金流量赋值检验失败！');
					}
				}
			}
			if($arracc[$infov["km"]]["kh"]==-1){
				if(is_null($infov["kh"])){
					exit('第['.$infov["xh"].']行：客户空值检验失败！');
				}else{
					if(!getValueExist(array("jc_kh","id",$infov["kh"]))){
						exit('第['.$infov["xh"].']行：客户赋值检验失败！');
					}
				}
			}
			if($arracc[$infov["km"]]["gr"]==-1){
				if(is_null($infov["gr"])){
					exit('第['.$infov["xh"].']行：个人空值检验失败！');
				}else{
					if(!getValueExist(array("jc_gr","id",$infov["gr"]))){
						exit('第['.$infov["xh"].']行：个人赋值检验失败！');
					}
				}
			}
			if($arracc[$infov["km"]]["ch"]==-1){
				if(is_null($infov["ch"])){
					exit('第['.$infov["xh"].']行：存货空值检验失败！');
				}else{
					if(!getValueExist(array("jc_ch","id",$infov["ch"]))){
						exit('第['.$infov["xh"].']行：存货赋值检验失败！');
					}
				}
			}
			if($arracc[$infov["km"]]["gys"]==-1){
				if(is_null($infov["gys"])){
					exit('第['.$infov["xh"].']行：供应商空值检验失败！');
				}else{
					if(!getValueExist(array("jc_gys","id",$infov["gys"]))){
						exit('第['.$infov["xh"].']行：供应商赋值检验失败！');
					}
				}
			}
			if($arracc[$infov["km"]]["bm"]==-1){
				if(is_null($infov["bm"])){
					exit('第['.$infov["xh"].']行：部门空值检验失败！');
				}else{
					if(!getValueExist(array("jc_bm","id",$infov["bm"]))){
						exit('第['.$infov["xh"].']行：部门赋值检验失败！');
					}
				}
			}
			if($arracc[$infov["km"]]["xm"]==-1){
				if(is_null($infov["xm"])){
					exit('第['.$infov["xh"].']行：项目空值检验失败！');
				}else{
					if(!getValueExist(array("jc_xm","id",$infov["xm"]))){
						exit('第['.$infov["xh"].']行：项目赋值检验失败！');
					}
				}
			}
			$sumdr+=$infov["dr"];
			$sumcr+=$infov["cr"];
		}
		//借贷方是否相等验证；
		if(round($sumdr,2)!=round($sumcr,2)){
			exit('验证失败：借贷方不相等！');
		}

		//验证完成，开始进行审核处理；
		$mysqli->autocommit(FALSE);//关闭事务的自动提交；

		//添加审核标记；
		$strsqlsh='update V1 set chk="'.$_SESSION["username"].'" where qj="'.$qj.'" and pzh="'.$pzh.'"';
		if(!$mysqli->query($strsqlsh)){
			$mysqli->rollback();
			exit('ns');
		}

		//写入入库数据；
		$intid=getNextNumber("chin","id");
		$strsqlrk='select * from v_chin where qj="'.$qj.'" and pzh="'.$pzh.'"';
		$sqlrk=$mysqli->query($strsqlrk);
		while($info=$sqlrk->fetch_array(MYSQLI_BOTH)){
			$strsqladd='INSERT INTO chin(id,qj,pzh,ywrq,km,kmF,gys,gysF,ch,chF,xm,xmF,slwb,slwbF,dr,zy,childid) VALUES ("'.$intid.'","'.$info["qj"].'","'.$info["pzh"].'","'.$info["ywrq"].'","'.$info["km"].'","'.$info["kmF"].'","'.$info["gys"].'","'.$info["gysF"].'","'.$info["ch"].'","'.$info["chF"].'","'.$info["xm"].'","'.$info["xmF"].'","'.$info["slwb"].'","'.$info["slwbF"].'","'.$info["dr"].'","'.$info["zy"].'","'.$intid.'")';
			if(!$mysqli->query($strsqladd)){
				$mysqli->rollback();
				exit('ns');
			}
			$intid++;
		}
		
		//写入出库数据；
		$intid=getNextNumber("chout","id");
		$strsqlck='select * from v_chout where qj="'.$qj.'" and pzh="'.$pzh.'"';
		$sqlck=$mysqli->query($strsqlck);
		while($info=$sqlck->fetch_array(MYSQLI_BOTH)){
			$strsqladd='INSERT INTO chout(id,qj,pzh,ywrq,km,kmF,kh,khF,ch,chF,xm,xmF,slwb,slwbF,cr,zy,childid) VALUES ("'.$intid.'","'.$info["qj"].'","'.$info["pzh"].'","'.$info["ywrq"].'","'.$info["km"].'","'.$info["kmF"].'","'.$info["kh"].'","'.$info["khF"].'","'.$info["ch"].'","'.$info["chF"].'","'.$info["xm"].'","'.$info["xmF"].'","'.$info["slwb"].'","'.$info["slwbF"].'","'.$info["cr"].'","'.$info["zy"].'","'.$intid.'")';
			if(!$mysqli->query($strsqladd)){
				$mysqli->rollback();
				exit('ns');
			}
			$intid++;
		}

		$mysqli->commit();

		echo 'ys';
		
	}else if($info["chk"]==$_SESSION["username"]){
		//单个取消审核前需要检查入出库表中是否有记录；
		if(getValueExist(array("chin","qj",$qj,"pzh",$pzh))){
			exit('反审核失败，请先删除入库数据！');
		}
		if(getValueExist(array("chout","qj",$qj,"pzh",$pzh))){
			exit('反审核失败，请先删除出库数据！');
		}
		$mysqli->query("update V1 set chk=null where qj='".$qj."' and pzh='".$pzh."'");
		echo 'ns';
	}else{
		echo '操作失败:'.$qj.'→'.$pzh."\n";
		echo '失败可能的原因：'."\n";
		echo '1>将要审核的凭证已经审核且非同一人；';
	}
}else{
	echo '操作失败:'.$qj.'→'.$pzh."\n";
	echo '失败可能的原因：'."\n";
	echo '1>相应的凭证已经记账；'."\n";
	echo '2>相应的凭证已经删除；';
}
$sql->free();
$mysqli->close();
?>