<?php
require("../config.inc.php");
if(!$arrGroup['用户分组']){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//系统管理中的-账套管理、用户分组、用户管理功能只限于superac数据库下可用；
if($arrPublicVar["db"]!="superac"){
	exit("请登录至【superac】账套下使用！");
}

@$groupname=mysqli_real_escape_string($mysqli,$_REQUEST["groupname"]);

echo '<div class="access1">';
if($groupname==''){
	$strsql='select distinct Gname from `groups` order by Gname';
	echo '组名称：<input id="findgrouptext" type="text" /> <input id="findgroupbtn_sys_userget" type="button" value="查找" /> <input id="updategroup_sys_userget" type="button" value="重新生成用户分组权限" /> <input id="trial_error_sys_userget" type="button" value="超安全次数登录信息跟踪" href="config/trial_error.php"/>';
}else{
	$strsql='select distinct Gname from `groups` where Gname like "%'.$groupname.'%" order by Gname';
	echo '组名称：<input id="findgrouptext" type="text" value="'.$groupname.'" /> <input id="findgroupbtn_sys_userget" type="button" value="查找" /> <input id="updategroup_sys_userget" type="button" value="重新生成用户分组权限" /> <input id="trial_error_sys_userget" type="button" value="超安全次数登录信息跟踪" href="config/trial_error.php"/>';
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
	echo '<table id="tbl1_sys_group" class="mytable tablesorter"><thead><tr><td>操作</td><td>组名称</td></tr></thead>';
	DO{
		echo '<tr><td><a name="a" href="#"><img src="templates/picture/b_insert.png" title="新增" /></a>';
		if($info["Gname"]!='管理员'){
			echo '<a name="e" href="config/sys_groupsubmit.php?czgroup=edit&name='.$info["Gname"].'"><img src="templates/picture/b_edit.png" title="修改" /></a>
				  <a name="d" href="config/sys_groupsubmit.php?czgroup=delete&name='.$info["Gname"].'"><img src="templates/picture/b_drop.png" title="删除" /></a>';
		}
		echo '<a name="r" href="config/sys_groupget.php?name='.urlencode($info["Gname"]).'"><img src="picture/locked.png" title="权限设置" /></a></td><td>'.$info["Gname"].'</td></tr>';
	}while($info=$sql->fetch_array(MYSQLI_BOTH));
	echo '</table>';
	$sql->free();
	$mysqli->close();
}else{
	echo '<br/>用户组无数据！';
}

echo '</div>';
?>
<style type="text/css"> 
#tbl1_sys_group td{
	text-align:center;
}
</style>

<script type="text/javascript">
$(function(){
	//表格插件应用
	$("#rightHand>div:visible #tbl1_sys_group").mytable({interlacedColor:true,selectedColor:true,clickedColor:true,columnWidth:false});
	//表格排序插件应用
	$("#rightHand>div:visible #tbl1_sys_group").tablesorter({
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
	$("#rightHand>div:visible #findgroupbtn_sys_userget").on("click",function(){
		$strURL='config/sys_group.php?groupname='+$("#rightHand>div:visible #findgrouptext").val();
		$("#rightHand>div:visible").load($strURL);
	});
	//重新生成分组权限数据
	$("#rightHand>div:visible #updategroup_sys_userget").on("click",function(){
		if(confirm("您确信要重新生成分组权限数据吗？\n该操作将依据当前左侧菜单名称及全部用户分组信息重新生成权限数据！\n该操作会生成一个空的分组权限数据，生成成功后请再指定权限！")){
			//序列化菜单选项
			var $mymenu;
			$("#mymenu li").each(function(){
				if($(this).has("a").length>0&&!$(this).hasClass("noRight")){//菜单上面存在链接且不存在权限限制
					if($mymenu==undefined||$mymenu==null||$mymenu==""){
						$mymenu=$(this).attr("id")+'=>'+$(this).text();
					}else{
						$mymenu+='|'+$(this).attr("id")+'=>'+$(this).text();
					}
				}
			});
			//alert($mymenu);
			$.post("config/sys_groupsubmit.php",{
				czgroup:"updategroup",
				menu:$mymenu
			},function(data,textStatus){
				alert(data);
			});
			return false;
		}
	});
	//超安全次数登录信息跟踪
	$("#rightHand>div:visible #trial_error_sys_userget").on("click",function(){
		$("#popbox").popup({title:"超安全次数登录信息跟踪:",width:"500",height:"550"}).load($(this).attr("href"));
		return false;
	});
	//新增
	$("#rightHand>div:visible #tbl1_sys_group a[name=a]").on("click",function(){
		//序列化菜单选项
		var $mymenu;
		$("#mymenu li").each(function(){
			if($(this).has("a").length>0&&!$(this).hasClass("noRight")){//菜单上面存在链接且不存在权限限制
				if($mymenu==undefined||$mymenu==null||$mymenu==""){
					$mymenu=$(this).attr("id")+'=>'+$(this).text();
				}else{
					$mymenu+='|'+$(this).attr("id")+'=>'+$(this).text();
				}
			}
		});
		//alert($mymenu);
		var r=prompt("请输入新增组名称:");
		if(r){
			$.post("config/sys_groupsubmit.php",{
				czgroup:"add",
				name:r,
				menu:$mymenu
			},function(data,textStatus){
				alert(data);
				$("#rightHand>div:visible #findgroupbtn_sys_userget").click();
			});
		}
		return false;
	});
	//修改
	$("#rightHand>div:visible #tbl1_sys_group a[name=e]").on("click",function(){
		var r=prompt("请输入修改后的组名称:");
		if(r){
			var $url=$(this).attr("href")+"&newname="+r;
			$.get($url,function(data,textStatus){
				alert(data);
				$("#rightHand>div:visible #findgroupbtn_sys_userget").click();
			});
		}
		return false;
	});
	//删除
	$("#rightHand>div:visible #tbl1_sys_group a[name=d]").on("click",function(){
		if(confirm("您确信要删除该组吗？")){
			$.get($(this).attr("href"),function(data,textStatus){
				alert(data);
				$("#rightHand>div:visible #findgroupbtn_sys_userget").click();
			});
		}
		return false;
	});
	//获取权限
	$("#rightHand>div:visible #tbl1_sys_group a[name=r]").on("click",function(){
		$("#popbox").popup({title:"权限设置:",width:"500",height:"500"}).load($(this).attr("href"));
		return false;
	});
});
</script>