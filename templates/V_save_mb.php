<?php
require("../config.inc.php");
if(!$arrGroup["会计凭证"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
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

//检测是否有同名的数据
$strSql='select * from v3 where mbname="'.$arr1["mbname"].'" and etr="'.$arrPublicVar["username"].'"';
$mysqli->query($strSql);
$afr=$mysqli->affected_rows;
if($afr>0){
	echo '后台提示：模板名称重复，保存失败！';
	exit();
}

//关闭当前自动提交事务，从此处开始，对数据库数据的先删除，后批量增加都统一提交；
$mysqli->autocommit(FALSE);

//获取模板表中最大ID值；
$sql=$mysqli->query('select max(id) as idMax from v3');
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
	$strField='id,mbname,zy,km,kmF,dr,cr,etr';
	$strValue='"'.$id.'","'.$arr1["mbname"].'","'.$arr2[$i]["zy"].'","'.$arr2[$i]["km"].'","'.$arr2[$i]["f"].'","'.round($arr2[$i]["dr"],2).'","'.round($arr2[$i]["cr"],2).'","'.$_SESSION["username"].'"';
	//echo $strField;
    //echo $strValue;
	$strsql='insert into V3('.$strField.') values('.$strValue.')';
	//echo $strsql;
	$result=$mysqli->query($strsql);
	$id++;//凭证id自动加1；
	//如果有一条记录提交失败，则回滚并中止程序的继续执行。
	if(!$result){
		$mysqli->rollback();
		exit('后台提示：凭证模板保存失败！');
	}
}
				
//print_r($arrResult);

$mysqli->commit();//自动提交事务；

echo 'YES';

?>