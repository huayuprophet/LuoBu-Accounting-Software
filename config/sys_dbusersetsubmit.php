<?php
require("../config.inc.php");
if(!$arrGroup['账套管理']){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_POST["tj_sys_dbuserset"]=='开始匹配'){
	//是否选取相关的数据
	if(!isset($_POST["db_dbuserset"])){
		exit("未选取账套！");
	}
	if(!isset($_POST["user_dbuserset"])){
		exit("未选取用户！");
	}
	for($i=0;$i<=count(@$_POST["db_dbuserset"])-1;$i++){ 
		$arri=explode("|",@$_POST["db_dbuserset"][$i]);
		for($j=0;$j<=count(@$_POST["user_dbuserset"])-1;$j++){ 
			$arrj=explode("|",@$_POST["user_dbuserset"][$j]);
			//echo $arri[0].'-'.$arri[1].'-'.$arrj[0].'-'.$arrj[1]."\n";
			if(!getValueExist(array("dbuser","dbid",$arri[0],"userid",$arrj[0]))){
				//echo $arri[0].'-'.$arri[1].'-'.$arrj[0].'-'.$arrj[1]."\n";
				$m=getNextNumber("dbuser","id");
				$strsql="insert into dbuser(id,dbid,dbname,userid,username) values('$m','$arri[0]','$arri[1]','$arrj[0]','$arrj[1]')";
				$mysqli->query($strsql);
			}
		}
	}
	echo '匹配成功，请点“查询”进行刷新！';
}
?>