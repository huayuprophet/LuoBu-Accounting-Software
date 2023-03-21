<?php
require("../config.inc.php");
if(!$arrGroup["项目账表"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$objQj=new QJ();
$objQj->getQj('select distinct qj from v1 order by qj desc');

echo '<form id="form_xmyrb" method="post" autocomplete="off">
<table>
<tr>
<td>期间：</td>
<td>
<select name="qj0" size="1">';
	foreach($objQj->arrayQj as $intqj){
	  echo '<option value="'.$intqj.'">'.$intqj.'</option>';
	}
	echo '</select>
</td>
<td>—</td>
<td>
<select name="qj1" size="1">';
	foreach($objQj->arrayQj as $intqj){
	  echo '<option value="'.$intqj.'">'.$intqj.'</option>';
	}
	echo '</select>
</td>
</tr>
<tr>
<td>项目名称：</td>
<td colspan="3">
<select id="xmName_Vbook_xmyrb" name="xmName" style="width:100px">
<option value="xjll" selected="selected">现金流量</option>
<option value="kh">客户</option>
<option value="gr">个人</option>
<option value="ch">存货</option>
<option value="gys">供应商</option>
<option value="bm">部门</option>
<option value="xm">核算项目</option>
</select>
</td>
</tr>
<tr>
<td>会计科目：</td>
<td colspan="3">
<div id="kmxm_Vbook_xmyrb"></div>
</td>
</tr>
<tr>
<td>
<select name="serach">
<option value="id">ID</option>
<option value="name" selected="selected">名称</option>
</select>
</td>
<td colspan="3">
<select name="cp">
<option value="1" selected="selected">包含</option>
<option value="2">等于</option>
<option value="3">开头是</option>
<option value="4">结尾是</option>
</select>
<input name="keyword" type="text" style="width:110px" />
</td>
</tr>
<tr>
<td>数量外币：</td>
<td colspan="3">
<select name="slwb" style="width:100px">
<option value="0" selected="selected">否</option>
<option value="1">是</option>
</select>
</td>
</tr>
<tr>
<td colspan="4" align="center">
<img id="wait_xmyrb" class="hide" src="picture/wait.gif" />
<input id="tj_xmyrb" name="tj" type="button" value="提交" />
</td>
</tr>
</table>
</div>
</form>';
	
unset($objQj);

echo '<div id=access_Vbook_xmyrb></div>';
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//获取会计科目信息；
	$.post("templates/Vbook_xmyrbkm.php",$("#rightHand>div:visible #form_xmyrb").serialize(),function(data,textStatus){
		$("#rightHand>div:visible #kmxm_Vbook_xmyrb").html(data);
	});
	$("#rightHand>div:visible #xmName_Vbook_xmyrb").change(function(){
		$.post("templates/Vbook_xmyrbkm.php",$("#rightHand>div:visible #form_xmyrb").serialize(),function(data,textStatus){
			$("#rightHand>div:visible #kmxm_Vbook_xmyrb").html(data);
		});
		return false;
	});
	//提交查询凭证条件；
	$("#rightHand>div:visible #tj_xmyrb").click(function(){
		//检查是否有会计科目被选中；
		if($("#rightHand>div:visible #form_xmyrb input:checked").length==0){
			alert("请勾选相应的会计科目！");
			return false;
		}
		$("#rightHand>div:visible #access_Vbook_xmyrb").empty();
		$("#rightHand>div:visible #wait_xmyrb").show();
		$.post("templates/Vbook_xmyrbaccess.php",$("#rightHand>div:visible #form_xmyrb").serialize(),function(data,textStatus){
			$("#rightHand>div:visible #access_Vbook_xmyrb").html(data);
			$("#rightHand>div:visible #wait_xmyrb").hide();
		});
		return false;
	});
});
//-->
</script>