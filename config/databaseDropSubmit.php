<?php

set_time_limit(180);

try{
	
	//禁止缓存；
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pramga: no-cache");

	require_once("../config/conf.php");
	
	//数据库密码验证；
	if(!preg_match("/^.{8,}$/",$_GET["rootpwd"])){
		exit('mysql 密码,长度不少于8位！');
	}
	
	$xml_TE=simplexml_load_file('trial_error0.xml');
	
	//数据库链接；
	$mysqli=@new mysqli("127.0.0.1","root",$_GET["rootpwd"]);
	
	//时区设置；
	date_default_timezone_set("PRC");
	$currentDate=date("Y-m-d");
	
	// Works as of PHP 5.2.9 and 5.3.0.
	if($mysqli->connect_error){
		//向服务器请求登录出错的次数；
		$hasNote=false;
		foreach($xml_TE->children() as $layer){
			if($layer->ips==getIp()){
				if((int)$layer->nums < 3){
					$layer->dts=$currentDate;
					$layer->nums=(int)$layer->nums+1;
					$xml_TE->asXML('trial_error0.xml');
					exit('Login：密码错误，'.$layer->nums.'(出错次数)/3(总次数)');
				}else{
					exit('Login：密码错误超出3次，系统锁死!');
				}
				$hasNote=true;
			}
		}
		if(!$hasNote){
			$xmlsub=$xml_TE->addChild('sub');
			$xmlsub->addChild('ips',getIp());
			$xmlsub->addChild('dts',$currentDate);
			$xmlsub->addChild('nums',1);
			$xml_TE->saveXML('trial_error0.xml');
			exit('Login：密码错误，1(出错次数)/3(总次数)');
		}
		//die(mysqli_connect_error());
	}else{
		//向服务器验证是否有锁死的情况；
		$i=0;
		foreach($xml_TE->sub as $sub){
			if($sub->ips==getIp()){
				if((int)$sub->nums == 3){
					exit('Login：密码错误超出3次，系统锁死!');
				}else{
					unset($xml_TE->sub[$i]);
					$xml_TE->asXML('trial_error0.xml');
					break;
				}
			}
			$i++;
		}
	}
	
	//预防数据库的安全攻击；
	$dbname=mysqli_real_escape_string($mysqli,$_GET["dbname"]);
	
	//删除数据库；
	$strsql='drop database '.$dbname;
	
	if($mysqli->query($strsql)){
		echo "成功删除数据库文件\n";
	}else{
		echo "数据库不存在，程序继续执行\n";
	}
	
	/*
	//删除db.xml文件相关数据库信息
	$doc = new DOMDocument;    
	$doc->load('db.xml');    
	$trans = $doc->documentElement->getElementsByTagName('sub');
	$length = $trans->length;   
	 
	for($i=0;$i< $length; $i++)   
	{
		$listid = $trans->item($i)->getElementsByTagName('tbl');   
		$tmpid = $listid->item(0)->nodeValue;  
		if($tmpid == $dbname)
		{
			$trans->item($i)->parentNode->removeChild($trans->item($i));
			//$listid->parentNode->parentNode->removeChild($listid->parentNode);
			break;
		}
	}

	$doc->save('db.xml');
	*/

	//删除数据库中账套的信息；
	$mysqli->query("delete from superac.dbinfo where id='$dbname'");
	
	echo "成功删除数据库列表";
	
	$mysqli->close();
	$mysqli = null;
	
}catch(Exception $e){
    echo 'Caught exception: ',$e->getMessage(),"\n";
}
?>