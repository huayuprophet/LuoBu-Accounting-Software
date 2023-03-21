<?php
//引用主程序文件；
require_once("../config.inc.php");
//安全事项一：遍历所有的临时文件，删除属于自己创建的临时文件；
$dir="../templates/access/";
$handle = opendir($dir); 
if($handle){
	while(($fl = readdir($handle)) !== false){
		$temp = $dir.substr(DIRECTORY_SEPARATOR,1).$fl;
		//如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
		if(is_dir($temp) && $fl!='.' && $fl != '..'){
			//echo '目录：'.$temp.'<br>';
			unlink($temp);//凡是目录，全部删除；
		}else{
			if($fl!='.' && $fl != '..'){
				//echo '文件：'.$temp.'<br>';
				$str1="_".$arrPublicVar["userid"];
				if(stripos($temp,$str1)>0) unlink($temp);
			}
		}
	}
}
//安全事项二：注销会话；
session_unset();
session_destroy();
setcookie('PHPSESSID',0,time()-3600);
echo '成功安全退出！';
?>