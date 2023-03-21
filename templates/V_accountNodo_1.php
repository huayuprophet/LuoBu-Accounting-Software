<?php
require("../config.inc.php");
if(!$arrGroup["序时账簿"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$objQj=new QJ();
$objQj->getQj('select distinct qj from v2 order by qj desc');

echo '<form id="frmfjz" name="frm1" action="templates/V_accountNodo_2.php" method="post">
	  <div align="center">
	  <table>
	  <tr height="60px" valign="bottom">
	  <td>记账期间：</td>
	  <td>
	  <select name="qj" size="1" required>';
	foreach($objQj->arrayQj as $intqj){
		echo '<option value="'.$intqj.'">'.$intqj.'</option>';
	}
echo '</select>
	  </td>
	  <td><input type="submit" value="下一步" /></td>
	  </tr>
	  <tr height="60px" valign="bottom">
	  <td colspan="3">
	  <font color="red">1>反记账后的凭证可以取消审核；</font>
	  </td>
	  </tr>
	  <tr height="60px" valign="bottom">
	  <td colspan="3">
	  <font color="red">2>反记账并取消审核凭证可修改；</font>
	  </td>
	  </tr>
	  </table>
	  </div>
	  </form>';

unset($objQj);
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//新增或保存
	var options = { 
		beforeSubmit:showRequest, // pre-submit callback
		success:showResponse, // post-submit callback
	}; 
	$("#popbox #frmfjz").ajaxForm(options); 
});
// pre-submit callback 
function showRequest(formData, jqForm, options){ 

} 
// post-submit callback 
function showResponse(responseText, statusText){
	alert(responseText);
	$("div.popupclose").trigger("click")
	$("#rightHand>div:visible #tj_Vbook").click();
}
//-->
</script>