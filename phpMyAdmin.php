<?php
try{
	
	//禁止缓存；
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pramga: no-cache");
	
	//调用自动获取IP地址函数文件；
	require_once("config/conf.php");
	
	if(getIp()!='127.0.0.1'){
		header("refresh:3;url=./");
		exit("非法访问,等待跳转...");
	}else{
		/*
		为了安全起见，只能在服务器机器中才能访问phpMyAdmin；
		修改phpMyAdmin的名称，并不影响访问，所以建议将phpMyAdmin名称修改为他人不容易猜到的名称；
		*/
		header("refresh:0;url=http://127.0.0.1/phpMyAdmin/");
	}
	
}catch(Exception $e){
    echo 'Caught exception: ',$e->getMessage(),"\n";
}
?>