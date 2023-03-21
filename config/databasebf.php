<?php

set_time_limit(180);

try{

	//禁止缓存；
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pramga: no-cache");
	
	//调用公共参数；
	require_once("../config/conf.php");

	//数据库链接；
	$mysqli=@new mysqli("127.0.0.1","root",$_GET["rootpwd"]);

	// Works as of PHP 5.2.9 and 5.3.0.
	if($mysqli->connect_error){
		die(mysqli_connect_error());
	}
	
	//时区设置；
	date_default_timezone_set("PRC");

	//预防数据库的安全攻击；
	$dbname=mysqli_real_escape_string($mysqli,$_GET["dbname"]);

	$fileName1=$fileName0.$dbname.'/';
	$fileName2=$fileName1.'_db/';

	//-h127.0.0.1限定了只能在主机上备份当前数据库；
	$exec="mysqldump -h127.0.0.1 -uroot -p".$_GET["rootpwd"]." ".$dbname." > ".$fileName2.$dbname.date("_Y.m.d_H.i.s").".sql";
	$result=exec($exec);
	if(!$result){
		echo '备份成功！';
	}else{
		echo '备份失败！';
	}
	
	$mysqli->close();
	$mysqli = null;

}catch(Exception $e){
    echo 'Caught exception: ',$e->getMessage(),"\n";
}
?>