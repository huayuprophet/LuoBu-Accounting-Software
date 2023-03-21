<?php 
/*	
标准表格控件样式；
*/
echo '<table id="tbl1_abc_sheet" class="mytable tablesorter sheet">
	<thead><tr><th>序号</th><th>名称</th><th>型号</th><th>数量</th><th>金额</th></tr></thead>
	<tbody>
	<tr><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td></tr>
	<tr><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td></tr>
	<tr><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td></tr>
	<tr><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td></tr>
	<tr><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td><td><input type="text" /></td></tr>
	</tbody>
	</table>';
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//表格插件应用
	$("#tbl1_abc_sheet").mytable({a:true,b:true,c:true,d:true});
});
//-->
</script>