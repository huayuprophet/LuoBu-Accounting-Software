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
		if($info["chk"]==$_SESSION["username"]){
			//单个取消审核前需要检查入出库表中是否有记录；
			if(getValueExist(array("chin","qj",$qj,"pzh",$pzh))){
				$strBackInfo.='凭证：'.$data_CV.'# 请先删除入库数据！'."\n";
				continue; //跳出本次循环，执行下一次循环；
			}
			if(getValueExist(array("chout","qj",$qj,"pzh",$pzh))){
				$strBackInfo.='凭证：'.$data_CV.'# 请先删除出库数据！'."\n";
				continue; //跳出本次循环，执行下一次循环；
			}
			$mysqli->query("update V1 set chk=null where qj='".$qj."' and pzh='".$pzh."'");
			$strBackInfo.='凭证：'.$data_CV.'# 反审核成功'."\n";
		}else{
			$strBackInfo.='凭证：'.$data_CV.'# 反审核失败'."\n";
		}
	}else{
		$strBackInfo.='凭证：'.$data_CV.'# 反审核失败'."\n";
	}
}

$sql->free();
$mysqli->close();

echo $strBackInfo;
?>