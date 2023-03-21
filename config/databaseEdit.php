<?php

set_time_limit(180);

try{
	
	//禁止缓存；
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pramga: no-cache");
	
	/*
	$db = simplexml_load_file("db.xml");
	foreach ($db->sub as $sub) {
		if($sub->tbl==$_POST["id"]){
			$sub->name=$_POST["name"];
			break;
		}
	}
	$db->asXML('db.xml');
	*/

	//数据库链接；
	$mysqli=@new mysqli("127.0.0.1","root",$_POST["rootpwd"]);
	if($mysqli->connect_error){
		echo '数据库连接失败！';
	}else{
		$strsql='update superac.dbinfo set dbname="'.$_POST["name"].'" where id="'.$_POST["id"].'"';
		//更新数据库中账套的信息；
		$mysqli->query($strsql);
		echo '修改标签成功！';
	}
	
}catch(Exception $e){
    echo 'Caught exception: ',$e->getMessage(),"\n";
}
?>