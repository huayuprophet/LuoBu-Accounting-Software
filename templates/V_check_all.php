<?php
require("../config.inc.php");
if(!$arrGroup["序时账簿"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//句柄检测；
if($_POST["handle"]!='checkV'){
	echo '句柄错误！';
	exit();
}

//print_r($_POST["data_checkV"]);

$strBackInfo='';//凭证审核结果返回信息；

foreach($_POST["data_checkV"] as $data_CV){
	
	$data_arr_CV=explode("-",$data_CV);
	$qj=$data_arr_CV[0];
	$pzh=$data_arr_CV[1];

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
			$stop=false;$sumdr=0;$sumcr=0;
			$strsqlquery='select * from v1 where qj="'.$qj.'" and pzh="'.$pzh.'" order by xh asc';
			$sqlv=$mysqli->query($strsqlquery);
			while($infov=$sqlv->fetch_array(MYSQLI_BOTH)){
				//未级科目验证；
				$strsqlchildquery='select id from account where id like "'.$infov["km"].'%"';
				$mysqli->query($strsqlchildquery);
				if($mysqli->affected_rows!=1){
					$strBackInfo.='凭证：'.$data_CV.'# 审核失败：会计科目非未级科目'."\n";
					$stop=true;
					break;
				}
				//字段非空验证；
				if($arracc[$infov["km"]]["slwb"]==-1 && $infov["slwb"]==0){
					$strBackInfo.='凭证：'.$data_CV.'# 审核失败：数量外币检验失败'."\n";
					$stop=true;
					break;
				}
				if($arracc[$infov["km"]]["xjll"]==-1){
					if(is_null($infov["xjll"])){
						$strBackInfo.='凭证：'.$data_CV.'# 审核失败：现金流量空值检验失败'."\n";
						$stop=true;
						break;
					}else{
						if(!getValueExist(array("jc_xjll","id",$infov["xjll"]))){
							$strBackInfo.='凭证：'.$data_CV.'# 审核失败：现金流量赋值检验失败'."\n";
							$stop=true;
							break;
						}
					}
				}
				if($arracc[$infov["km"]]["kh"]==-1){
					if(is_null($infov["kh"])){
						$strBackInfo.='凭证：'.$data_CV.'# 审核失败：客户空值检验失败'."\n";
						$stop=true;
						break;
					}else{
						if(!getValueExist(array("jc_kh","id",$infov["kh"]))){
							$strBackInfo.='凭证：'.$data_CV.'# 审核失败：客户赋值检验失败'."\n";
							$stop=true;
							break;
						}
					}
				}
				if($arracc[$infov["km"]]["gr"]==-1){
					if(is_null($infov["gr"])){
						$strBackInfo.='凭证：'.$data_CV.'# 审核失败：个人空值检验失败'."\n";
						$stop=true;
						break;
					}else{
						if(!getValueExist(array("jc_gr","id",$infov["gr"]))){
							$strBackInfo.='凭证：'.$data_CV.'# 审核失败：个人赋值检验失败'."\n";
							$stop=true;
							break;
						}
					}
				}
				if($arracc[$infov["km"]]["ch"]==-1){
					if(is_null($infov["ch"])){
						$strBackInfo.='凭证：'.$data_CV.'# 审核失败：存货空值检验失败'."\n";
						$stop=true;
						break;
					}else{
						if(!getValueExist(array("jc_ch","id",$infov["ch"]))){
							$strBackInfo.='凭证：'.$data_CV.'# 审核失败：存货赋值检验失败'."\n";
							$stop=true;
							break;
						}
					}
				}
				if($arracc[$infov["km"]]["gys"]==-1){
					if(is_null($infov["gys"])){
						$strBackInfo.='凭证：'.$data_CV.'# 审核失败：供应商空值检验失败'."\n";
						$stop=true;
						break;
					}else{
						if(!getValueExist(array("jc_gys","id",$infov["gys"]))){
							$strBackInfo.='凭证：'.$data_CV.'# 审核失败：供应商赋值检验失败'."\n";
							$stop=true;
							break;
						}
					}
				}
				if($arracc[$infov["km"]]["bm"]==-1){
					if(is_null($infov["bm"])){
						$strBackInfo.='凭证：'.$data_CV.'# 审核失败：部门空值检验失败'."\n";
						$stop=true;
						break;
					}else{
						if(!getValueExist(array("jc_bm","id",$infov["bm"]))){
							$strBackInfo.='凭证：'.$data_CV.'# 审核失败：部门赋值检验失败'."\n";
							$stop=true;
							break;
						}
					}
				}
				if($arracc[$infov["km"]]["xm"]==-1){
					if(is_null($infov["xm"])){
						$strBackInfo.='凭证：'.$data_CV.'# 审核失败：项目空值检验失败'."\n";
						$stop=true;
						break;
					}else{
						if(!getValueExist(array("jc_xm","id",$infov["xm"]))){
							$strBackInfo.='凭证：'.$data_CV.'# 审核失败：项目赋值检验失败'."\n";
							$stop=true;
							break;
						}
					}
				}
				$sumdr+=$infov["dr"];
				$sumcr+=$infov["cr"];
			}
			if($stop==true){
				continue; //跳出本次循环，执行下一次循环；
			}
			//借贷方是否相等验证；
			if(round($sumdr,2)!=round($sumcr,2)){
				$strBackInfo.='凭证：'.$data_CV.'# 审核失败：借贷方不相等'."\n";
				continue; //跳出本次循环，执行下一次循环；
			}
			
			$mysqli->autocommit(FALSE);//关闭事务的自动提交；

			//添加审核标记；
			$strsqlsh='update V1 set chk="'.$_SESSION["username"].'" where qj="'.$qj.'" and pzh="'.$pzh.'"';
			if(!$mysqli->query($strsqlsh)){
				$mysqli->rollback();
				$strBackInfo.='凭证：'.$data_CV.'# 添加审核标记失败'."\n";
				continue;
			}

			//写入入库数据；
			$intid=getNextNumber("chin","id");
			$strsqlrk='select * from v_chin where qj="'.$qj.'" and pzh="'.$pzh.'"';
			$sqlrk=$mysqli->query($strsqlrk);
			while($info=$sqlrk->fetch_array(MYSQLI_BOTH)){
				$strsqladd='INSERT INTO chin(id,qj,pzh,ywrq,km,kmF,gys,gysF,ch,chF,xm,xmF,slwb,slwbF,dr,zy,childid) VALUES ("'.$intid.'","'.$info["qj"].'","'.$info["pzh"].'","'.$info["ywrq"].'","'.$info["km"].'","'.$info["kmF"].'","'.$info["gys"].'","'.$info["gysF"].'","'.$info["ch"].'","'.$info["chF"].'","'.$info["xm"].'","'.$info["xmF"].'","'.$info["slwb"].'","'.$info["slwbF"].'","'.$info["dr"].'","'.$info["zy"].'","'.$intid.'")';
				if(!$mysqli->query($strsqladd)){
					$mysqli->rollback();
					$strBackInfo.='凭证：'.$data_CV.'# 添加入库记录失败'."\n";
					continue;
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
					$strBackInfo.='凭证：'.$data_CV.'# 添加出库记录失败'."\n";
					continue;
				}
				$intid++;
			}

			$mysqli->commit();

			$strBackInfo.='凭证：'.$data_CV.'# 审核成功'."\n";
			
		}else if($info["chk"]==$_SESSION["username"]){
			//$mysqli->query("update V1 set chk=null where qj='".$qj."' and pzh='".$pzh."'");
			$strBackInfo.='凭证：'.$data_CV.'# 审核失败：已审核'."\n";
		}else{
			$strBackInfo.='凭证：'.$data_CV.'# 审核失败：已经审核且非同一人'."\n";
		}
	}else{
		$strBackInfo.='凭证：'.$data_CV.'# 审核失败：不符合审核条件'."\n";
	}
}

$sql->free();
$mysqli->close();

echo $strBackInfo;
?>