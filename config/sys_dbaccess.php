<?php
require("../config.inc.php");
if(!$arrGroup['账套管理']){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//安全验证；
$zd=mysqli_real_escape_string($mysqli,$_GET["zd"]);
$tj=mysqli_real_escape_string($mysqli,$_GET["tj"]);
$gjz=mysqli_real_escape_string($mysqli,$_GET["gjz"]);

//构造查询SQL语句；
switch($tj){
	case "包含":
		$strsql='select * from dbuser where '.$zd.' like "%'.$gjz.'%" order by dbid,userid';
		break;
	case "开头是":
		$strsql='select * from dbuser where '.$zd.' like "'.$gjz.'%" order by dbid,userid';
		break;
	case "结尾是":
		$strsql='select * from dbuser where '.$zd.' like "%'.$gjz.'" order by dbid,userid';
		break;
	case "等于":
		$strsql='select * from dbuser where '.$zd.'="'.$gjz.'" order by dbid,userid';
		break;
	default://非以上结果的处理；
		echo '<div>get取值不正确</div>';
		exit();
}

//从当前数据库中检索数据库信息与用户信息；
$sql=$mysqli->query($strsql);
$info=$sql->fetch_array(MYSQLI_BOTH);
if($info){
	echo '<table id="tbl1_sys_db" class="tablesorter"><thead><tr><td>T</td><td>数据库</td><td>数据库简称</td><td>用户ID</td><td>用户名称</td></tr></thead>';
	DO{
		echo '<tr><td><input name="dbuser_sys_dbaccess[]" type="checkbox" value="'.$info["id"].'" /></td><td>'.$info["dbid"].'</td><td>'.$info["dbname"].'</td><td>'.$info["userid"].'</td><td>'.$info["username"].'</td></tr>';
	}while($info=$sql->fetch_array(MYSQLI_BOTH));
	echo '</table>';
	$sql->free();
	$mysqli->close();
}else{
	echo '无账套数据！';
}
?>

<style type="text/css"> 
#tbl1_sys_db td{
	text-align:center;
}
</style>

<script type="text/javascript">
$(function(){
	//表格插件应用
	$("#rightHand>div:visible #tbl1_sys_db").mytable({interlacedColor:true,selectedColor:true,clickedColor:true,columnWidth:false});
	//表格排序插件应用
	$("#rightHand>div:visible #tbl1_sys_db").tablesorter({
		widgets        : ['zebra', 'columns'],
		usNumberFormat : false,
		sortReset      : true,
		sortRestart    : true,
		headers:{0:{sorter:false}}
	});
});
</script>