<?php
require("../config.inc.php");
if(!$arrGroup['账套管理']){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//从当前数据库中检索数据库信息与用户信息；
echo '<form id="frm_sys_dbuserset" method="post" action="config/sys_dbusersetsubmit.php">';

echo '<div style="width:100%;height:20px;">请选择账套及用户进行权限匹配：</div>';

$strsql="select id,dbname from dbinfo order by id";
$sql=$mysqli->query($strsql);
echo '<select name="db_dbuserset[] size="10" multiple style="width:50%;height:85%;">';
while($info=$sql->fetch_array(MYSQLI_BOTH)){
	$tempStr=$info["id"].'|'.$info["dbname"];
	echo '<option value="'.$tempStr.'">'.$info["dbname"].'</option>';
}
echo '</select>';

$strsql="select id,name from user order by id";
$sql=$mysqli->query($strsql);
echo '<select name="user_dbuserset[] size="10" multiple style="width:50%;height:85%;">';
while($info=$sql->fetch_array(MYSQLI_BOTH)){
	$tempStr=$info["id"].'|'.$info["name"];
	echo '<option value="'.$tempStr.'">'.$info["name"].'</option>';
}
echo '</select>';

echo '<input type="submit" name="tj_sys_dbuserset" value="开始匹配" /></form>';
?>

<script type="text/javascript">
$(function(){
	//新增或保存
	var options = { 
		beforeSubmit:showRequest, // pre-submit callback
		success:showResponse, // post-submit callback
	}; 
	$("#frm_sys_dbuserset").ajaxForm(options); //此处为弹出界面，元素具有唯一性；
	// pre-submit callback 
	function showRequest(formData, jqForm, options){ 
		//alert('提交数据');
	} 
	// post-submit callback 
	function showResponse(responseText,statusText){
		alert(responseText);
	} 
});
</script>