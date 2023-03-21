<?php 
require("../config.inc.php");
if(!$arrGroup[$_GET["tbl"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
$tbl=mysqli_real_escape_string($mysqli,$_GET["tbl"]);
$id=mysqli_real_escape_string($mysqli,$_GET["id"]);
$strsql='select * from '.$tblName.' where id="'.$id.'"';
$sql=$mysqli->query($strsql);
$info=$sql->fetch_array(MYSQLI_BOTH);
if($info){ //如果存在数据；
?>
<form id="jc_EditForm" action="templates/jc_EditSubmit.php" method="get">
<input name="tbl" type="hidden" value="<?php echo $_GET["tbl"] ?>" /><br/>
<table align="center">
<tr><td colspan="2" align="center"><font size="+3"><?php echo $_GET["tbl"]; ?></font></td></tr>
<tr>
<td>ID</td><td><input name="oldid" type="hidden" maxlength="12" style="width:550px" readonly value="<?php echo $info["id"]; ?>" required />
<input name="newid" type="text" maxlength="12" style="width:550px" value="<?php echo $info["id"]; ?>" required />
<br/><font color="gray">注：ID不超出12位字母、数字或二者混合组成。</font>
</td>
</tr>
<tr>
<td>名称</td><td><input name="name" type="text" maxlength="250" style="width:550px" value="<?php echo $info["name"]; ?>" required />
<br/><font color="gray">注：名称不能使用禁用字符或者超出250个字符。</font>
</td>
</tr>
<tr>
<td>分类</td><td><input name="typec" type="text" maxlength="250" style="width:550px" value="<?php echo $info["typec"]; ?>"/>
<br/><font color="gray">注：分类不能超出250个字符，下拉无数据可手动填写。</font>
</td>
</tr>
<tr>
<td colspan="2" align="center">
<?php
//已经审核的数据不允许修改；
if(is_null($info["chk"])){
    echo '<input name="edit" type="submit" value="修改" />';
}else{
	echo '<input name="edit" type="submit" disabled="disabled" value="修改" />';
}
$sql->free();
$mysqli->close();
?>
</td>
</tr>
</table>
</form>
<?php
}else{
	echo '未检测到相关数据，该数据可能被删除；';
}
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//新增或保存
	var options = { 
		beforeSubmit:showRequest, // pre-submit callback
		success:showResponse, // post-submit callback
	}; 
	$("#jc_EditForm").ajaxForm(options); 

	//分类下拉提示；
	$("#jc_EditForm input[name=typec]").getSuggestBox({RequestURI:"templates/jc_typec.php",FindSource:$("#jc_EditForm input[name=tbl]").val(),Callback:function(arrtd){
		$("#jc_EditForm input[name=typec]").val(arrtd[0]);
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