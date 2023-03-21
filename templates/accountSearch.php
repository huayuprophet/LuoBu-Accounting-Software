<?php
require("../config.inc.php");
if(!$arrGroup["会计科目"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
//取出前端传来的关键字；
$searchText=mysqli_real_escape_string($mysqli,$_GET["keywordSuggestBox"]);
//构建sql查询语句；
$strSql='select id,Fname,t,dc from account where id like "'.$searchText.'%" or Fname like "%'.$searchText.'%" order by id asc';
//执行sql语句
$sql=$mysqli->query($strSql);
//定义一个数组；
$array=array();
//循环数组，这里仅使用关联数组：MYSQLI_ASSOC；
while($info=$sql->fetch_array(MYSQLI_ASSOC)){
	//向数组中添加数组，形成一个二维数组；
	array_push($array,$info);
}
$sql->free();
$mysqli->close();
//生成json数据；
echo json_encode($array);
?>