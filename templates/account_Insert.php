<?php 
require("../config.inc.php");
if(!$arrGroup["会计科目"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
?>
<form action="templates/account_InsertSubmit.php" method="post" id="accountInsertForm" name="accountInsertForm">
<table align="center">
<tr><td colspan="2" align="center"><font size="+3">会计科目</font></td></tr>
<tr>
<td>ID（纯数字组成科目级次：4-2-2-2-2）</td><td><input name="id" type="text" maxlength="12" style="width:450px" required /></td>
</tr>
<tr>
<td>名称（会计科目名称，不得使用：|→）</td><td><input name="name" type="text" maxlength="25" style="width:450px" required /></td>
</tr>
<tr>
<td>分类（会计科目五大标准分类）</td>
<td>
<select name="t" style="width:450px">
<option value="资产" selected="selected">资产</option>
<option value="负债">负债</option>
<option value="权益">权益</option>
<option value="成本">成本</option>
<option value="利润">利润</option>
</select>
</td>
</tr>
<tr>
<td>方向（会计科目借贷方向的指定）</td>
<td>
<select name="dc" style="width:450px">
<option value="借方" selected="selected">借方</option>
<option value="贷方">贷方</option>
</select>
</td>
</tr>
<tr>
<td>基础资料<br/><br/>1、选择“是”带上项目核算<br/><br/>2、选择“否”取消项目核算</td>
<td align="left">
	<table>
	<tr>
	<td width="100px">
	数量外币
	</td>
	<td>
	<select name="slwb">
	<option value="0" selected="selected">否</option>
	<option value="-1">是</option>
	</select>
	</td>
	<td width="100px"></td>
	<td width="100px">
	现金流量
	</td>
	<td>
	<select name="xjll">
	<option value="0" selected="selected">否</option>
	<option value="-1">是</option>
	</select>
	</td>
	<tr>
	<td width="100px">
	客户
	</td>
	<td>
	<select name="kh">
	<option value="0" selected="selected">否</option>
	<option value="-1">是</option>
	</select>
	</td>
	<td width="100px"></td>
	<td width="100px">
	个人
	</td>
	<td>
	<select name="gr">
	<option value="0" selected="selected">否</option>
	<option value="-1">是</option>
	</select>
	</td>
	</tr>
	<tr>
	<td width="100px">
	存货
	</td>
	<td>
	<select name="ch">
	<option value="0" selected="selected">否</option>
	<option value="-1">是</option>
	</select>
	</td>
	<td width="100px"></td>
	<td width="100px">
	供应商
	</td>
	<td>
	<select name="gys">
	<option value="0" selected="selected">否</option>
	<option value="-1">是</option>
	</select>
	</td>
	</tr>
	<tr>
	<td width="100px">
	部门
	</td>
	<td>
	<select name="bm">
	<option value="0" selected="selected">否</option>
	<option value="-1">是</option>
	</select>
	</td>
	<td width="100px"></td>
	<td width="100px">
	核算项目
	</td>
	<td>
	<select name="xm">
	<option value="0" selected="selected">否</option>
	<option value="-1">是</option>
	</select>
	</td>
	</tr>
	</table>
</td>
</tr>
<tr>
<td colspan="2" align="center"><br /><br /><input name="insert" type="submit" value="新增" /></td>
</tr>
</table>
</form>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//新增或保存
	var options = { 
		beforeSubmit:showRequest, // pre-submit callback
		success:showResponse, // post-submit callback
	}; 
	$("#accountInsertForm").ajaxForm(options); 
});
// pre-submit callback 
function showRequest(formData, jqForm, options){ 

} 
// post-submit callback 
function showResponse(responseText, statusText){
	alert(responseText);
}
//-->
</script>