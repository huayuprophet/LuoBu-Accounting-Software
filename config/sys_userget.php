<?php
require("../config.inc.php");
if(!$arrGroup['用户管理']){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if(isset($_REQUEST["ID"])){
	$id=mysqli_real_escape_string($mysqli,$_REQUEST["ID"]);
	$strsql='select id,name,telephone,Gname from user where id="'.$id.'"';
	$sql=$mysqli->query($strsql);
	$info=$sql->fetch_array(MYSQLI_BOTH);
}
$strsqlgroup='select distinct Gname from `groups`';
$sqlgroup=$mysqli->query($strsqlgroup);
echo '<br><form id="userset_sys_userget" action="config/sys_usersubmit.php" method="post">';
echo '<table id="tbluserget" align="center">';
echo '<tr><td>ID</td><td>';
echo isset($id)?'<input name="ID" type="text" required  readonly value="'.$info["id"].'">':'<input name="ID" type="text" required>';
echo '</td></tr>';
echo '<tr><td>名称</td><td>';
echo isset($id)?'<input name="name" type="text" required value="'.$info["name"].'">':'<input name="name" type="text" required/>';
echo '</td></tr>';
echo '<tr><td>电话</td><td>';
echo isset($id)?'<input name="telephone" type="text" required value='.$info["telephone"].'>':'<input name="telephone" type="text" required/>';
echo '</td></tr>';
echo '<tr><td>群组</td><td>';
echo '<select name="Gname" size="1" required>';
while($infogroup=$sqlgroup->fetch_array(MYSQLI_BOTH)){
	if($info["Gname"]==$infogroup["Gname"]){
		echo '<option selected value="'.$infogroup["Gname"].'">'.$infogroup["Gname"].'</option>';
	}else{
		echo '<option value="'.$infogroup["Gname"].'">'.$infogroup["Gname"].'</option>';
	}
}
echo '</select>';
echo '</td></tr>';
echo isset($id)?'<tr><td>重置密码</td><td><input type="checkbox" name="resetpwd"></td></tr>':'';
echo '<tfoot><tr><td align="center" colspan="2">';
echo isset($id)?'<input name="czuser" type="submit" value="修改">':'<input name="czuser" type="submit" value="新增">';
echo '</td></tr></tfoot>';
echo '</table>';
echo '</form>';
$sqlgroup->free();
if(isset($id)){
	$sql->free();
	$mysqli->close();
}
?>
<style type="text/css"> 
#tbluserget td{
	height:38px;
}
</style>

<script type="text/javascript">
$(function(){
	//新增或保存
	var options = { 
		beforeSubmit:showRequest, // pre-submit callback
		success:showResponse, // post-submit callback
	}; 
	$("#popbox #userset_sys_userget").ajaxForm(options); 
});
// pre-submit callback 
function showRequest(formData, jqForm, options){ 
    //判断是否脱离框架；
	if($("#rightHand").length==0){
		alert("脱离框架，操作失败！");
		return false;
	}
} 
// post-submit callback 
function showResponse(responseText, statusText){
	if(responseText.indexOf("成功")>-1){
	　　alert(responseText);
		$("#rightHand>div:visible").load("config/sys_user.php");
	}else{
		alert(responseText);
	}
} 
</script>