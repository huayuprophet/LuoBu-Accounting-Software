<?php
require("../config.inc.php");
if(!$arrGroup["年度结转"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$xml=simplexml_load_file('../config/db.xml');
$xmlarray=array();
//将$xml转化成键值对应的数组，然后排序，最后写入下拉控件中；
foreach($xml->children() as $layer_one){
	$xmlarray["$layer_one->tbl"]="$layer_one->name";
}
asort($xmlarray);
echo '<table><tr><td>把【当前账套】数据结转至 <select id="db1_year1" required>';
foreach($xmlarray as $k=>$v){ 
	if($k!=$_SESSION["db"]){
		echo "<option value='$k'>$v</option>";
	}
}
echo '</select></td>
	<td><img id="wait_year1" class="hide" src="picture/wait.gif" /></td>
	<td><input id="tj_year1" type="button" value="提交" /></td></tr><table>';
echo '<div id="getinfo_year1"></div>';
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//提交；
	$("#rightHand>div:visible #tj_year1").click(function(){
		$("#rightHand>div:visible #wait_year1").show();
		$("#rightHand>div:visible #getinfo_year1").html('');
		$.post("templates/year1_submit.php",{
			db1:$("#rightHand>div:visible #db1_year1").val(),
			tj:$("#rightHand>div:visible #tj_year1").val()
		},function(data,textStatus){
			$("#rightHand>div:visible #getinfo_year1").html(data);
			$("#rightHand>div:visible #wait_year1").hide();
		});
		return false;
	});
});
//-->
</script>