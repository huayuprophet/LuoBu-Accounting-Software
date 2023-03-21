<?php
require("../config.inc.php");
if(!$arrGroup['账套管理']){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//系统管理中的-账套管理、用户分组、用户管理功能只限于superac数据库下可用；
if($arrPublicVar["db"]!="superac"){
	exit("请登录至【superac】账套下使用！");
}

echo '<div class="access1">
	<select id="zd_sys_db">
	<option value="dbid">数据库</option>
	<option value="dbname" selected="selected">数据库简称</option>
	<option value="userid">用户ID</option>
	<option value="username">用户名称</option>
	</select>
	<select id="tj_sys_db">
	<option value="包含" selected="selected">包含</option>
	<option value="等于">等于</option>
	<option value="开头是">开头是</option>
	<option value="结尾是">结尾是</option>
	</select>
	<input id="gjz_sys_db" type="text" maxlength="25"/>
	<input id="cx_sys_db" type="button" value="查询" />
	<input id="xq_sys_db" type="button" value="全部选取" />
	<input id="qx_sys_db" type="button" value="全部取消" />
	<input id="xz_sys_db" type="button" value="添加权限" />
	<input id="sc_sys_db" type="button" value="删除权限" />
	</div>';
echo '<div class="access2"></div>';
?>

<script type="text/javascript">
$(function(){
	//查询
	$("#cx_sys_db").on("click",function(){
		$.get("config/sys_dbaccess.php",{
			zd : $("#rightHand>div:visible #zd_sys_db option:selected").val(),
			tj : $("#rightHand>div:visible #tj_sys_db option:selected").val(),
			gjz : $("#rightHand>div:visible #gjz_sys_db").val()
		},function(data,textStatus){
			$("#rightHand>div:visible .access2").html(data);
		});
	});
	//全部选择
	$("#xq_sys_db").on("click",function(){
		$("#rightHand>div:visible .access2 input[type=checkbox]").prop("checked",true);
	});
	//全部取消
	$("#qx_sys_db").on("click",function(){
		$("#rightHand>div:visible .access2 input[type=checkbox]").prop("checked",false);
	});
	//添加权限
	$("#xz_sys_db").on("click",function(){
		$("#popbox").popup({title:"账套=>用户匹配：",width:"850",height:"525"}).load("config/sys_dbuserset.php");
		return false;
	});
	//删除权限
	$("#sc_sys_db").on("click",function(){
		//删除前提醒；
		if(!confirm("您确信要进行删除操作吗？")){
			return false;
		}
		$varvalue="";
		$("#rightHand>div:visible .access2 input:checked").each(function(){
			if($varvalue==""){
				$varvalue=$(this).val();
			}else{
				$varvalue+=','+$(this).val();
			}
		});
		$.post("config/sys_dbdel.php",{
			zdv : $varvalue
		},function(data,textStatus){
			alert(data);
		});
	});
});
</script>