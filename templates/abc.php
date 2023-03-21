<?php 
/*	
成型模板，无需修改，直接套用，如果数据表不方便全部暴露，可以使用视图功能对表进行局部显现；
该段代码的正确使用需要在php请求文件指定数据表名称值，如abc.php?tbl=tableName；
在接收的php文件中，使用$_REQUEST["rht"]可以接收权限值、$_REQUEST["tbl"]可以接收表或者视图；
*/
require("../config.inc.php");
if(!$arrGroup[$_REQUEST["rht"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
$rightvalue=mysqli_real_escape_string($mysqli,$_REQUEST["rht"]);
$tablename=mysqli_real_escape_string($mysqli,$_REQUEST["tbl"]);
$strsql='show full columns from '.$tablename.' from '.$_SESSION["db"];
//echo $strsql;
$sql=$mysqli->query($strsql);
echo '<div class="access1">';
echo '<input id="rht_abc" type="hidden" value="'.$rightvalue.'" />
	<input id="tbl_abc" type="hidden" value="'.$tablename.'" />
	<select id="zd_abc">';
while($info=$sql->fetch_array(MYSQLI_BOTH)){
	echo '<option value="'.$info["Field"].'">'.$info["Comment"].'</option>';
}
echo '</select>
	<select id="tj_abc">
	<option value="包含" selected="selected">包含</option>
	<option value="等于">等于</option>
	<option value="大于">大于</option>
	<option value="大于等于">大于等于</option>
	<option value="小于">小于</option>
	<option value="小于等于">小于等于</option>
	<option value="开头是">开头是</option>
	<option value="结尾是">结尾是</option></select>
	<input id="gjz_abc" type="text" maxlength="25"/>
	<input id="cx_abc" type="button" value="查询" />
	<input id="xz_abc" type="button" value="新增" />
	<input id="xg_abc" type="button" value="修改" />
	<input id="sc_abc" type="button" value="删除" />
	<input id="fz_abc" type="button" value="复制" />';
echo '</div>';
echo '<div class="access2"></div>';
$sql->free();
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//查询
	$("#rightHand>div:visible #cx_abc").click(function(){
		$.get("templates/abc_access.php",{
			rht : $("#rightHand>div:visible #rht_abc").val(),
			tbl : $("#rightHand>div:visible #tbl_abc").val(),
			zd : $("#rightHand>div:visible #zd_abc option:selected").val(),
			tj : $("#rightHand>div:visible #tj_abc option:selected").val(),
			gjz : $("#rightHand>div:visible #gjz_abc").val()
		},function(data,textStatus){
			$("#rightHand>div:visible .access2").html(data);
		});
	});
	/*
	数据的维护需要向被请求页面传递四个信息：cz=?&rht=?&tbl=?&id=?;
	cz:主要传递了：新增（Add）、修改（Edit）、删除（Delete）、复制（Copy）
	rht:菜单名称；tbl:需要维护的数据表名称；id：数据表中的唯一ID值；
	*/
	//新增
	$("#rightHand>div:visible #xz_abc").click(function(){
		var url="templates/abc_set.php?cz=Add&rht="+$("#rightHand>div:visible #rht_abc").val()+"&tbl="+$("#rightHand>div:visible #tbl_abc").val()+"&id=''";
		//alert(url);
		$("#popbox").popup({title:'新增',width:"350",height:"350"}).load(url);
		return false;
	});
	//修改
	$("#rightHand>div:visible #xg_abc").click(function(){
		var $elelength=$("#rightHand>div:visible input[type='checkbox']:checked").length;
		if($elelength!=1){
			alert('请勾选唯一数据！');
			return false;
		}
		var url="templates/abc_set.php?cz=Edit&rht="+$("#rightHand>div:visible #rht_abc").val()+"&tbl="+$("#rightHand>div:visible #tbl_abc").val()+"&id="+$("#rightHand>div:visible input[type='checkbox']:checked").val();
		//alert(url);
		$("#popbox").popup({title:'修改',width:"350",height:"350"}).load(url);
		return false;
	});
	//删除
	$("#rightHand>div:visible #sc_abc").click(function(){
		var $elelength=$("#rightHand>div:visible input[type='checkbox']:checked").length;
		if($elelength!=1){
			alert('请勾选唯一数据！');
			return false;
		}
		var url="templates/abc_set.php?cz=Delete&rht="+$("#rightHand>div:visible #rht_abc").val()+"&tbl="+$("#rightHand>div:visible #tbl_abc").val()+"&id="+$("#rightHand>div:visible input[type='checkbox']:checked").val();
		//alert(url);
		$("#popbox").popup({title:'删除',width:"350",height:"350"}).load(url);
		return false;
	});
	//复制
	$("#rightHand>div:visible #fz_abc").click(function(){
		var $elelength=$("#rightHand>div:visible input[type='checkbox']:checked").length;
		if($elelength!=1){
			alert('请勾选唯一数据！');
			return false;
		}
		var url="templates/abc_set.php?cz=Copy&rht="+$("#rightHand>div:visible #rht_abc").val()+"&tbl="+$("#rightHand>div:visible #tbl_abc").val()+"&id="+$("#rightHand>div:visible input[type='checkbox']:checked").val();
		//alert(url);
		$("#popbox").popup({title:'复制',width:"350",height:"350"}).load(url);
		return false;
	});
});
//-->
</script>