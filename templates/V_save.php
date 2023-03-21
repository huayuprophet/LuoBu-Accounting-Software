<?php
require("../config.inc.php");
if(!$arrGroup["会计凭证"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

function getField($str){
	//通过项目表名称得到凭证表中相对应的字段名称；
	switch($str){
		case "数量外币":
		    return "slwb";
			break;
		case "现金流量":
			return "xjll";
			break;
		case "客户":
			return "kh";
			break;
		case "个人":
			return "gr";
			break;
		case "存货":
			return "ch";
			break;
		case "供应商":
			return "gys";
			break;
		case "部门":
			return "bm";
			break;
		case "核算项目":
			return "xm";
			break;
		default:
			return "";
			break;
	}
}

//转义
$arr1=stripslashes($_POST["v1"]);
$arr2=stripslashes($_POST["v2"]);

//对 JSON 格式的字符串进行编码
$arr1=json_decode($arr1,true);
$arr2=json_decode($arr2,true);

/*//打印数组；
print_r($arr1);
print_r($arr2);*/

//日期检测；
$date1=explode("-",$arr1["ywrq"]);
$date2=explode("-",$arr1["lrrq"]);
if(checkdate($date1[1],$date1[2],$date1[0])==false){
	exit('后台提示：业务日期不正确！');
}
if(checkdate($date2[1],$date2[2],$date2[0])==false){
	exit('后台提示：录入日期不正确！');
}

//借贷合是否相等检测，通过只保留两位小数后计算结果再检测；
$sumDr=0;
$sumCr=0;
for($i=0;$i<count($arr2);$i++){
	//测试金额格式是否正确；
	if($arr2[$i]["cr"]==""){
		if(!preg_match($arraySV["money"][0],$arr2[$i]["dr"])){
			exit('借方验证失败：'.$arraySV["money"][1]);
		}
	}else{
		if(!preg_match($arraySV["money"][0],$arr2[$i]["cr"])){
			exit('贷方验证失败：'.$arraySV["money"][1]);
		}
	}
	$sumDr+=floatval($arr2[$i]["dr"]);
	$sumCr+=floatval($arr2[$i]["cr"]);
}
if(round($sumDr,2)!=round($sumCr,2)){
	exit('后台提示：凭证借贷合不相等！');
}
	
$qj=$date2[0].(strlen($date2[1])==1?'0'.$date2[1]:$date2[1]);//当前凭证期间值，由4位年份和2位月份组成；

//检测录入期间是否记账，如记账，不允许再该期间新增凭证；
$sql=$mysqli->query('select max(qj) as maxQj from v1 where acc is not null');
$info=$sql->fetch_array(MYSQLI_BOTH);
if($info["maxQj"]>=$qj){
	exit('后台提示：该期间已记账，不能将凭证保存在已记账的期间内！');
}

//检测会计科目是否合法；
for($i=0;$i<count($arr2);$i++){
	if($arr2[$i]["km"]==""){
		echo '后台提示：会计科目不能为空：';
		echo '错误位于表格第[ '.($i+1).' ]行！';
		exit();
	}else{
		$mysqli->query('select * from account where id like "'.$arr2[$i]["km"].'%"');
		$afr=$mysqli->affected_rows;
		if($afr!=1){
			echo '后台提示：会计科目输入有误：';
		    echo '错误位于表格第[ '.($i+1).' ]行！';
			exit();
		}
	}
}

//关闭当前自动提交事务，从此处开始，对数据库数据的先删除，后批量增加都统一提交；
$mysqli->autocommit(FALSE);

//管理凭证号（新增与修改凭证最大区别）；
//新增凭证时，该值传入的是一个:0,0,(数字凭证号或空值)
//修改凭证时，该值传入的是一个：四位数期间，二位数月份，数字凭证号
$pz=explode(",",$arr1["pzh"]);
if($pz[0]==0){//新增；
    if($pz[2]==""){//没有指定相应的凭证号，自动获取数据库当前期间最大凭证号；
		$sql=$mysqli->query('select max(pzh) as pzhMax from V1 where qj="'.$qj.'"');
		$info=$sql->fetch_array(MYSQLI_BOTH);
		if($info){
			$pzh=$info["pzhMax"]+1;
		}else{
			$pzh=1;
		}
	}else{//手动指定相应的凭证号，先检查手动凭证号是否与数据库原有数据冲突。如冲突，停止程序执行，如不手动凭证号有效；
	    if(is_numeric($pz[2])){//检查手动凭证号是否为数字；
			$sql=$mysqli->query('select pzh from V1 where qj="'.$qj.'" and pzh="'.$pz[2].'"');
			$info=$sql->fetch_array(MYSQLI_BOTH);
			if($info){
				exit('后台提示：新增凭证号与现有数据存在冲突，请检查！');
			}else{
				$pzh=$pz[2];//新增凭证时，手动指定凭证号如与数据库数据不冲突，手动指定有效；
			}
		}else{
			exit('后台提示：凭证号必须为数字，请检查！');	
		}
	}
}else{//修改；
	if($pz[1]!=$pz[2]){//原凭证号与新凭证号不一样的；
	    if(is_numeric($pz[2])){//检查手动凭证号是否为数字；
			$sql=$mysqli->query('select pzh from V1 where qj="'.$qj.'" and pzh="'.$pz[2].'"');
			$info=$sql->fetch_array(MYSQLI_BOTH);
			if($info){
				exit('后台提示：保存凭证号与现有数据存在冲突，请检查！');
			}else{
				$mysqli->query('delete from V1 where qj="'.$pz[0].'" and pzh="'.$pz[1].'"');
				$pzh=$pz[2];
				//附件上的凭证号与凭证表同步更新；
				$mysqli->query('update v1_fj set qj="'.$qj.'",pzh="'.$pzh.'" where qj="'.$pz[0].'" and pzh="'.$pz[1].'"');
				//入出库流水上的凭证信息自动更新；
				$mysqli->query('update chin set fppzqj="'.$qj.'",fppzh="'.$pzh.'" where fppzqj="'.$pz[0].'" and fppzh="'.$pz[1].'"');
				$mysqli->query('update chout set fppzqj="'.$qj.'",fppzh="'.$pzh.'" where fppzqj="'.$pz[0].'" and fppzh="'.$pz[1].'"');
			}
		}else{
			exit('后台提示：凭证号必须为数字，请检查！');	
		}
	}else{//原凭证号与新凭证号一样的，检查期间是否一样。
	    if($qj==$pz[0]){//期间一样；
			$mysqli->query('delete from V1 where qj="'.$pz[0].'" and pzh="'.$pz[1].'"');
			$pzh=$pz[2];
		}else{//期间不一样；
			$sql=$mysqli->query('select pzh from V1 where qj="'.$qj.'" and pzh="'.$pz[2].'"');
			$info=$sql->fetch_array(MYSQLI_BOTH);
			if($info){
				exit('后台提示：保存凭证号与现有数据存在冲突，请检查！');
			}else{
				$mysqli->query('delete from V1 where qj="'.$pz[0].'" and pzh="'.$pz[1].'"');
				$pzh=$pz[2];
				//附件上的凭证号与凭证表同步更新；
				$mysqli->query('update v1_fj set qj="'.$qj.'",pzh="'.$pzh.'" where qj="'.$pz[0].'" and pzh="'.$pz[1].'"');
				//入出库流水上的凭证信息自动更新；
				$mysqli->query('update chin set fppzqj="'.$qj.'",fppzh="'.$pzh.'" where fppzqj="'.$pz[0].'" and fppzh="'.$pz[1].'"');
				$mysqli->query('update chout set fppzqj="'.$qj.'",fppzh="'.$pzh.'" where fppzqj="'.$pz[0].'" and fppzh="'.$pz[1].'"');
			}
		}
	}
}

//所有的验证通过后再自动取出凭证表中最大ID值；
$sql=$mysqli->query('select max(id) as idMax from v1');
$info=$sql->fetch_array(MYSQLI_BOTH);
if($info){
	$id=$info["idMax"]+1;
}else{
	$id=1;
}

//循环凭证表体数组，
$arrResult=array();//记录操作结果的数组；

for($i=0;$i<count($arr2);$i++){
	//echo $arr2[$i]["zy"];
	//echo $arr2[$i]["km"];
	//echo $arr2[$i]["f"];
	//echo $arr2[$i]["dr"];
	//echo $arr2[$i]["cr"];
	$strField="";
	$strValue="";
	$fullName=$arr2[$i]["f"];
	$fullName=explode("|",$fullName);
	//print_r($fullName);
	if(count($fullName)>1){
		//$fullName数组个数大于1说明有明细项目；
		//将$fullName数组再进行分解；
		for($j=1;$j<count($fullName);$j++){
			$fullNameSub=explode("→",$fullName[$j]);
			$strField.=getField($fullNameSub[0]).','.getField($fullNameSub[0]).'F,';
			//计算数量单价，当$fullNameSub[0]=="数量外币"时，表示该列是数量外币列，需要计算单价汇率；
			if($fullNameSub[0]=="数量外币"){
				if($arr2[$i]["dr"]!=""){
					$djhl=$fullNameSub[1]==0?0:round($arr2[$i]["dr"]/$fullNameSub[1],4);
				}else{
					$djhl=$fullNameSub[1]==0?0:round($arr2[$i]["cr"]/$fullNameSub[1],4);
				}
				$strValue.=round($fullNameSub[1],2).'","'.$djhl.'","';//数量外币列保留两位小数，多余的小数位数将舍弃；
			}else{
				$strValue.=$fullNameSub[1].'","'.$fullNameSub[2].'","';
			}
		}
	}
	$strField='id,ywrq,lrrq,qj,pzh,xh,zy,km,kmF,dr,cr,'.$strField.'etr';
	$strValue='"'.$id.'","'.$arr1["ywrq"].'","'.$arr1["lrrq"].'","'.$qj.'","'.$pzh.'","'.($i+1).'","'.$arr2[$i]["zy"].'","'.$arr2[$i]["km"].'","'.$arr2[$i]["f"].'","'.round($arr2[$i]["dr"],2).'","'.round($arr2[$i]["cr"],2).'","'.$strValue.$_SESSION["username"].'"';
	//echo $strField;
    //echo $strValue;
	$strsql='insert into V1('.$strField.') values('.$strValue.')';
	//echo $strsql;
	$result=$mysqli->query($strsql);
	$id++;//凭证id自动加1；
	//如果有一条记录提交失败，则回滚并中止程序的继续执行。
	if(!$result){
		$mysqli->rollback();
		exit('后台提示：凭证保存失败！');
	}
}

//取出相同期间和凭证号上附件表受影响的行数；
$strsqlfj='select * from v1_fj where qj="'.$qj.'" and pzh="'.$pzh.'"';
$sqlfj=$mysqli->query($strsqlfj);
$num_rows=$sqlfj->num_rows;
if($num_rows!=0){
	//向数据库写入附件个数；
	$strsql='update v1 set fj="'.$num_rows.'" where qj="'.$qj.'" and pzh="'.$pzh.'"';
	$mysqli->query($strsql);
}
				
//print_r($arrResult);

$mysqli->commit();//自动提交事务；

echo '后台提示：凭证保存成功：'.$pzh;

?>