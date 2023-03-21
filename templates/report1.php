<?php
require("../config.inc.php");
if(!$arrGroup["财务报表"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$objQj=new QJ();
$objQj->getQj('select distinct qj from v2 order by qj desc');

echo '<form id="form_report1" method="post" action="templates/report1_upFile.php" autocomplete="off">
<table>
<tr><td>请上传报表模板文件：</td><td>
<input id="up_file0" name="up_file" type="file" />
</td>
<td><img id="wait_report0" class="hide" src="picture/wait.gif" /></td>
<td><input id="tj_report0" type="submit" name="submit" value="上传" /></td></tr>
<tr><td>报表期间（已记账）：</td><td colspan="3">
<select name="qj0" size="1">';
	foreach($objQj->arrayQj as $intqj){
	  echo '<option value="'.$intqj.'">'.$intqj.'</option>';
	}
	echo '</select>&nbsp;<img id="wait_report1" class="hide" src="picture/wait.gif" />&nbsp;<input id="tj_report1" name="tj" type="button" value="提交" /></td></tr>
</table></form>
<div id="getinfo_report1"></div>';
	
unset($objQj);
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//报表模板上传；
	var options = { 
		beforeSubmit:showRequest, // pre-submit callback
		success:showResponse, // post-submit callback
	}; 
	$("#form_report1").ajaxForm(options); 
	//提交数据至后台计算；
	$("#rightHand>div:visible #tj_report1").click(function(){
		$("#rightHand>div:visible #wait_report1").show();
		$("#rightHand>div:visible #getinfo_report1").html('');
		$.post("templates/report1_submit.php",$("#rightHand>div:visible #form_report1").serialize(),function(data,textStatus){
			$("#rightHand>div:visible #getinfo_report1").html(data);
			$("#rightHand>div:visible #wait_report1").hide();
		});
		return false;
	});
});
// pre-submit callback 
function showRequest(formData, jqForm, options){ 
	//上传前判断上传文件是否是excel文件；
	if(form_report1.up_file0.value.substr(-5,5)!='.xlsx'){
		alert('上传报表模板文件格式有误！');
		return false;
	}
} 
// post-submit callback 
function showResponse(responseText, statusText){
	alert(responseText);
	return false;
}
//-->
</script>