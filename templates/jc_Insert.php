<?php 
require("../config.inc.php");
if(!$arrGroup[$_GET["tbl"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
?>

<form id="jc_InsertForm" action="templates/jc_InsertSubmit.php" method="get">
<input name="tbl" type="hidden" value="<?php echo $_GET["tbl"] ?>" /><br/>
<table align="center">
<tr><td colspan="2" align="center"><font size="+3"><?php echo $_GET["tbl"] ?></font></td></tr>
<tr>
<td>ID</td><td><input name="id" type="text" maxlength="12" style="width:550px" required />
<br/><font color="gray">注：ID不超出12位字母、数字或二者混合组成。</font>
</td>
</tr>
<tr>
<td>名称</td><td><input name="name" type="text" maxlength="250" style="width:550px" required />
<br/><font color="gray">注：名称不能使用禁用字符或者超出250个字符。</font>
</td>
</tr>
<tr>
<td>分类</td><td><input name="typec" type="text" maxlength="250" style="width:550px" />
<br/><font color="gray">注：分类不能超出250个字符，下拉无数据可手动填写。</font>
</td>
</tr>
<tr>
<td colspan="2" align="center"><input name="insert" type="submit" value="新增" /></td>
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
	$("#jc_InsertForm").ajaxForm(options); 

	//分类下拉提示；
	$("#jc_InsertForm input[name=typec]").getSuggestBox({RequestURI:"templates/jc_typec.php",FindSource:$("#jc_InsertForm input[name=tbl]").val(),Callback:function(arrtd){
		$("#jc_InsertForm input[name=typec]").val(arrtd[0]);
	}});
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