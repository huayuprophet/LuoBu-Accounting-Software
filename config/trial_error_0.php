<?php
require("../config.inc.php");
if($_SESSION["userid"]!='1001'){
	if(!$arrGroup['用户分组']){
		exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
	}
}

try{
	
	//禁止缓存；
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pramga: no-cache");
	
	//预防数据库的安全攻击；
	$ips=mysqli_real_escape_string($mysqli,$_GET["ips"]);
	
	//删除xml文件相关数据信息
	$doc = new DOMDocument;    
	$doc->load('trial_error0.xml');    
	$trans = $doc->documentElement->getElementsByTagName('sub');
	$length = $trans->length;   
	 
	for($i=0;$i< $length; $i++)   
	{
		$listid = $trans->item($i)->getElementsByTagName('ips');   
		$tmpid = $listid->item(0)->nodeValue;  
		if($tmpid == $ips)
		{
			$trans->item($i)->parentNode->removeChild($trans->item($i));
			//$listid->parentNode->parentNode->removeChild($listid->parentNode);
			break;
		}
	}

	$doc->save('trial_error0.xml');
	
	echo "成功删除，关闭本页面再进重新获取！";
	
}catch(Exception $e){
    echo 'Caught exception: ',$e->getMessage(),"\n";
}
?>