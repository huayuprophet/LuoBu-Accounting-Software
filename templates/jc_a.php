<?php 
//$_GET[jc_tbl]来源于主菜单GET请求；
require("../config.inc.php");
//echo urldecode($_GET["jc_tbl"]);
if(!$arrGroup[$_GET["jc_tbl"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
?>
<div class="access1">
<span id="jc_aTitle"><?php echo $_GET["jc_tbl"]; ?></span>
<select id="zd">
<option value="id">ID</option>
<option value="name" selected="selected">名称</option>
<option value="ty">停用</option>
<option value="etr">录入</option>
<option value="chk">审核</option>
</select>
<select id="tj">
<option value="包含" selected="selected">包含</option>
<option value="等于">等于</option>
<option value="开头是">开头是</option>
<option value="结尾是">结尾是</option>
</select>
<input id="gjz" type="text" maxlength="25"/>
<input id="cx" type="button" value="查询" />
<input id="xz" type="button" value="新增" />
<input id="xg" type="button" value="修改" />
<input id="sc" type="button" value="删除" />
<input id="fz" type="button" value="复制" />
<input id="sh" type="button" value="审核" />
<input id="fjgl" type="button" value="附件管理" />
</div>
<div class="access2"></div>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//查询
	$("#rightHand>div:visible #cx").click(function(){
		$.get("templates/jc_access.php",{
			tbl : $("#rightHand>div:visible #jc_aTitle").text(),
			zd : $("#rightHand>div:visible #zd option:selected").val(),
			tj : $("#rightHand>div:visible #tj option:selected").val(),
			gjz : $("#rightHand>div:visible #gjz").val()
		},function(data,textStatus){
			$("#rightHand>div:visible .access2").html(data);
		});
	});
	//新增
	$("#rightHand>div:visible #xz").click(function(){
		var url="templates/jc_Insert.php?tbl="+encodeURI($("#rightHand>div:visible #jc_aTitle").text());
		$("#popbox").popup({title:'新增',width:"600",height:"333"}).load(url);
		return false;
	});
	//修改
	$("#rightHand>div:visible #xg").click(function(){
		var $len=$("#rightHand>div:visible input[type='checkbox']:checked").length;
		if($len!=1){
			alert('请勾选唯一数据！');
			return false;
		}
		var url="templates/jc_Edit.php?tbl="+encodeURI($("#rightHand>div:visible #jc_aTitle").text())+"&id="+$("#rightHand>div:visible input[type='checkbox']:checked").val();
		$("#popbox").popup({title:'修改',width:"600",height:"333"}).load(url);
		return false;
	});
	//删除
	$("#rightHand>div:visible #sc").click(function(){
		var $len=$("#rightHand>div:visible input[type='checkbox']:checked").length;
		if($len!=1){
			alert('请勾选唯一数据！');
			return false;
		}
		var url="templates/jc_Drop.php?tbl="+encodeURI($("#rightHand>div:visible #jc_aTitle").text())+"&id="+$("#rightHand>div:visible input[type='checkbox']:checked").val();
		$("#popbox").popup({title:'删除',width:"600",height:"333"}).load(url);
		return false;
	});
	//复制
	$("#rightHand>div:visible #fz").click(function(){
		var $len=$("#rightHand>div:visible input[type='checkbox']:checked").length;
		if($len!=1){
			alert('请勾选唯一数据！');
			return false;
		}
		var url="templates/jc_Copy.php?tbl="+encodeURI($("#rightHand>div:visible #jc_aTitle").text())+"&id="+$("#rightHand>div:visible input[type='checkbox']:checked").val();
		$("#popbox").popup({title:'复制',width:"600",height:"333"}).load(url);
		return false;
	});
	//审核
	$("#rightHand>div:visible #sh").click(function(){
		var $len=$("#rightHand>div:visible input[type='checkbox']:checked").length;
		if($len!=1){
			alert('请勾选唯一数据！');
			return false;
		}
		var url="templates/jc_Check.php?tbl="+encodeURI($("#rightHand>div:visible #jc_aTitle").text())+"&id="+$("#rightHand>div:visible input[type='checkbox']:checked").val();
		$("#popbox").popup({title:'审核',width:"600",height:"333"}).load(url);
		return false;
	});
	//附件管理；
	$("#rightHand>div:visible #fjgl").click(function(){
		var $len=$("#rightHand>div:visible input[type='checkbox']:checked").length;
		if($len!=1){
			alert('请勾选唯一数据！');
			return false;
		}
		var url="templates/jc_upFile.php?tbl="+$("#rightHand>div:visible #jc_aTitle").text()+"&id="+$("#rightHand>div:visible input[type='checkbox']:checked").val();
		$(this).mytabs({strTitle:"基础资料附件管理",strUrl:url});
		return false;
	});
});
//-->
</script>