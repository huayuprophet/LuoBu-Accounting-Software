<?php
require("../config.inc.php");
if(!$arrGroup['用户管理']){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//系统管理中的-账套管理、用户分组、用户管理功能只限于superac数据库下可用；
if($arrPublicVar["db"]!="superac"){
	exit("请登录至【superac】账套下使用！");
}

@$username=mysqli_real_escape_string($mysqli,$_REQUEST["username"]);

echo '<div class="access1">';

if($username==''){
	$strsql='select id,name,telephone,Gname from user order by id';
	echo '名称：<input id="findusertext" type="text" /> <input id="finduserbtn" type="button" value="查找" />';
}else{
	$strsql='select id,name,telephone,Gname from user where name like "%'.$username.'%" order by id';
	echo '名称：<input id="findusertext" type="text" value="'.$username.'" /> <input id="finduserbtn" type="button" value="查找" />';
}

echo '</div>';

echo '<div class="access2">';

//=================================数据分页代码（开始）=================================
$page=isset($_GET['page'])?$_GET['page']:1;//页码数
//echo '页码数：'.$page.',';
$limitStart=$page==1?0:$arrPublicVar["tableRows"]*($page-1);//SQL语句中limit起始数
//echo 'limit起始数'.$limitStart.'<br/>';
$totalRecords=mysqli_num_rows($mysqli->query($strsql));//总页数
$p=new Page($totalRecords,$arrPublicVar["tableRows"],$page,$arrPublicVar["tableRows"]);
$strsql.=' limit '.$limitStart.','.$arrPublicVar["tableRows"];//在常用strSql语句后面加入limit语句
echo $totalRecords==0?"":$p->showPages(1);//自动生成导航条
//=================================数据分页代码（结束）=================================

$sql=$mysqli->query($strsql);
$info=$sql->fetch_array(MYSQLI_BOTH);
if($info){
	echo '<table id="tbl1_sys_user" class="mytable tablesorter"><thead><tr><td>操作</td><td>ID</td><td>名称</td><td>电话</td><td>隶属组</td></tr></thead>';
	DO{
		echo '<tr><td>
			<a name="a" href="#"><img src="templates/picture/b_insert.png" title="新增" /></a>
			<a name="e" href="config/sys_userget.php?ID='.$info["id"].'"><img src="templates/picture/b_edit.png" title="修改" /></a>
			<a name="d" href="config/sys_usersubmit.php?czuser=delete&ID='.$info["id"].'"><img src="templates/picture/b_drop.png" title="删除" /></a>
			</td><td>'.$info["id"].'</td><td>'.$info["name"].'</td><td>'.$info["telephone"].'</td><td>'.$info["Gname"].'</td></tr>';
	}while($info=$sql->fetch_array(MYSQLI_BOTH));
	echo '</table>';
	$sql->free();
	$mysqli->close();
}else{
	echo '<br/><a name="a" href="#"><img src="templates/picture/b_insert.png" title="新增" /></a>';
}

echo '</div>';
?>
<style type="text/css"> 
#tbl1_sys_user td{
	text-align:center;
}
</style>

<script type="text/javascript">
$(function(){
	//表格插件应用
	$("#rightHand>div:visible #tbl1_sys_user").mytable({interlacedColor:true,selectedColor:true,clickedColor:true,columnWidth:false});
	//表格排序插件应用
	$("#rightHand>div:visible #tbl1_sys_user").tablesorter({
		widgets        : ['zebra', 'columns'],
		usNumberFormat : false,
		sortReset      : true,
		sortRestart    : true,
		headers:{0:{sorter:false}}
	});
	//分页跳转
	$("#rightHand>div:visible span").on("click","a",function(){
		$("#rightHand>div:visible").load($(this).attr("href"));
		return false;
	});
	//查找
	$("#rightHand>div:visible #finduserbtn").on("click",function(){
		$strURL='config/sys_user.php?username='+$("#rightHand>div:visible #findusertext").val();
		$("#rightHand>div:visible").load($strURL);
	});
	//新增
	$("#rightHand>div:visible #tbl1_sys_user a[name=a]").on("click",function(){
		$("#popbox").popup({title:"新增用户:",width:"300",height:"270"}).load("config/sys_userget.php");
		return false;
	});
	//修改
	$("#rightHand>div:visible #tbl1_sys_user a[name=e]").on("click",function(){
		$("#popbox").popup({title:"修改用户:",width:"300",height:"320"}).load($(this).attr("href"));
		return false;
	});
	//删除
	$("#rightHand>div:visible #tbl1_sys_user a[name=d]").on("click",function(){
		if(!confirm("您确信要删除该用户吗？")){
			return false;
		}
		$.get($(this).attr("href"),function(data,textStatus){
			alert(data);
			$("#rightHand>div:visible #finduserbtn").click();
		});
		return false;
	});
});
</script>