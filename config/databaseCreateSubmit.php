<?php

set_time_limit(180);

try{
	
	//禁止缓存；
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pramga: no-cache");

	require_once("../config/conf.php");
	
	//数据库名称验证；
	if(!preg_match("/^[A-Za-z0-9]+$/",$_GET["dbname"])){
		exit('数据库名称为字母+数字,不得使用中文！');
	}
	
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
	$dbtype=mysqli_real_escape_string($mysqli,$_GET["dbtype"]);//账套分类；
	$dbname=mysqli_real_escape_string($mysqli,$_GET["dbname"]);//数据库名称；
	$dbinfo=mysqli_real_escape_string($mysqli,$_GET["dbinfo"]);//数据库简称；
	$dbgsmc=mysqli_real_escape_string($mysqli,$_GET["dbgsmc"]);//公司名称，核算主体；
	
	//验证名称是否包含匹配字符；
	$fieldValidation=array("→","|","@","echo","all",",",".");
	foreach($fieldValidation as $yz){
		if(substr_count($dbname,$yz)!=0){
			echo '验证失败；原因：名称中不能出现 '.$yz.' 字符';
			exit();
		}
	}
	
	/*
	1、检测文件夹是否存在，如不存在创建新文件夹并拷贝文件，如存在，中止程序的运行。
	2、为了文件目录的安全，给每一个新建的文件夹下拷贝index.html文件。
	*/
	$fileName1=$fileName0.$dbname.'/';
	$fileName2=$fileName1.'_db/';
	//首先在项目外创建存放文件的总文件夹；
	if(!file_exists($fileName0)){
		mkdir($fileName0);
		copy('index.html',$fileName0.'index.html');
	}
	//接着在总文件夹内创建应用程序文件夹；
	if(file_exists($fileName1)){
		exit('服务器文件夹存在，请检查！');
	}else{
		if(mkdir($fileName1)&&mkdir($fileName2)){
			if(copy('index.html',$fileName1.'index.html')&&copy('index.html',$fileName2.'index.html')){
				echo "成功新建服务器文件\n";
			}else{
				exit('服务器文件拷贝失败，请检查！');
			}
		}else{
			exit('服务器文件夹创建失败，请检查！');
		}
	}
	
	/*
	//向db.xml文件写入新建数据库信息
	$xml=simplexml_load_file('db.xml');
	
	$xmlsub=$xml->addChild('sub');
	$xmlsub->addChild('tbl',$dbname);
	$xmlsub->addChild('name',$dbinfo);
	
	$xml->saveXML('db.xml');
	
	echo "成功更新数据库列表\n";
	*/
	
	//创建数据库；
	$strsql='create database '.$dbname.' default character set utf8 collate utf8_general_ci';
	
	$mysqli->query($strsql) or die('Database already exists');

	//数据库的选择；
	$mysqli->select_db($dbname);
	
	//按不同的类型还原sql文件
	switch($dbtype){
		case "new":
			$exec='mysql -uroot -p'.$_GET["rootpwd"].' '.$dbname.' < sql/new.sql';
			exec($exec);
			echo "成功新建数据库文件";
			break;
		case "demo":
			$exec='mysql -uroot -p'.$_GET["rootpwd"].' '.$dbname.' < sql/demo.sql';
			exec($exec);
			echo "成功新建演示数据库";
			break;
		default:
			$exec="";
	}
	
	//更新数据库中公司名称信息；
	$mysqli->query("update info set namef='$dbgsmc' where idf=1");

	//更新超级账套数据库中账套的信息；
	$mysqli->query("insert into superac.dbinfo(id,dbname) values('$dbname','$dbinfo')");

	//如果是超级账套，则自动加入一条登录关联信息；
	if($dbname=="superac"){
		$strsql='INSERT INTO superac.dbuser(id,dbid,dbname,userid,username) VALUES("1","'.$dbname.'","'.$dbinfo.'","1001","admin")';
		$mysqli->query($strsql);
	}
	
	$mysqli->close();
	$mysqli = null;
	
}catch(Exception $e){
    echo 'Caught exception: ',$e->getMessage(),"\n";
}
?>