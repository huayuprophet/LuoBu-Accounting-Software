<?php
/*
成型模板，无需修改，直接套用；
*/
require("../config.inc.php");
if(!$arrGroup[$_REQUEST["rht"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
//定义接收页面：专用接收页面全名规则：abc_set_submit_表名称，如无则使用默认页面；
if(file_exists('abc_set_submit_'.$_REQUEST["tbl"].'.php')){
	$receivefiles='templates/abc_set_submit_'.$_REQUEST["tbl"].'.php'; 
}else{
	$receivefiles='templates/abc_set_submit.php'; 
}

echo '<form id="form_abc_set" action="'.$receivefiles.'" method="post" autocomplete="off">';
echo '<table id="tbl_abc_set" align="center">';

tblToH5($_REQUEST["tbl"],$_REQUEST["id"]);

echo '<tfoot><tr><td align="center" colspan="2">
	<input name="rht" type="hidden" value="'.$_REQUEST["rht"].'" />
	<input name="tbl" type="hidden" value="'.$_REQUEST["tbl"].'" />
	<input name="cz" type="submit" value="'.$_REQUEST["cz"].'">
	</td></tr></tfoot>';

echo '</table></form>';
?>

<script type="text/javascript">
$(function(){
	//新增或保存
	var options = { 
		beforeSubmit:showRequest, // pre-submit callback
		success:showResponse, // post-submit callback
	}; 
	$("#form_abc_set").ajaxForm(options); //此处为弹出界面，元素具有唯一性；
	// pre-submit callback 
	function showRequest(formData, jqForm, options){ 
		//alert('提交数据');
	} 
	// post-submit callback 
	function showResponse(responseText,statusText){
		alert(responseText);
		$("#rightHand>div:visible #cx_abc").click();
	} 
});
</script>