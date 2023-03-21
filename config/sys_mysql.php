<?php
require("../config.inc.php");
if(!$arrGroup["导出数据"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
?>

<div>
<?php
$xml=simplexml_load_file('sys_mysql_xls.xml');
echo '数据库中的表：';
echo '<select id="tbl_mysql">';
foreach($xml->children() as $layer_one){
	echo "<option value='$layer_one->id'>$layer_one->name</option>";
}
echo '</select>';
?>
&nbsp;
外部文件存储地址：<input id="tbladdress_mysql" type="text" maxlength="250" />
<input id="output_mysql" type="button" value="导出" />
<p>注意事项：</p>
<p>1、请输入文件导出目录，如目录不存在，系统会自动创建。如： d:/file/</p>
<p>2、地址长度不能超出250个字符，成功导出后，系统自动记忆导出目录。</p>
<p>3、本页面采用了 mysql 自带导出 excel 技术，导入可使用 phpMyAdmin。</p>
<p>4、本页面导出功能只在服务器（主机）上使用有效，客户远程端无法操作。</p>
</div>

<script type="text/javascript">
//如果参数zjqOutPutPath存在，则自动填入；
if($.cookie("zjqPath")!=null){
	$("#rightHand>div:visible #tbladdress_mysql").val($.cookie("zjqPath"));
}
$(function(){
	//导出
	$("#rightHand>div:visible #output_mysql").on("click",function(){
		//地址非空检测；
		if($("#rightHand>div:visible #tbladdress_mysql").val()==''){
			alert('地址不能为空！');
			$("#rightHand>div:visible #tbladdress_mysql").focus(); 
			return false;
		}
		//序时、入库或出库记录的导出；
		var testfield=$("#rightHand>div:visible #tbl_mysql").find("option:selected").text();
		if(testfield=="序时记录"||testfield=="入库记录"||testfield=="出库记录"){
			var r=prompt("请输入会计期间（四位年份两位月份），空值导出所有记录:");
			if(r==null){
				alert('导出中止！');
				return false;
			}
		}else{
			var r = null;
		}
		//提交导出数据参数；
		//alert($("#rightHand>div:visible #tbl_mysql").find("option:selected").val());
		//alert($("#rightHand>div:visible #tbl_mysql").find("option:selected").text());
		//alert($("#rightHand>div:visible #tbladdress_mysql").val());
		//alert(r);
		$.get("config/sys_mysql_outputsubmit.php",{
			tbl:$("#rightHand>div:visible #tbl_mysql").find("option:selected").val(),
			tblname:$("#rightHand>div:visible #tbl_mysql").find("option:selected").text(),
			tbladdress:$("#rightHand>div:visible #tbladdress_mysql").val(),
			qj:r
		},function(data,textStatus){
			if(data=="success"){
				alert("导出成功！");
				$.cookie("zjqPath",$("#rightHand>div:visible #tbladdress_mysql").val(),{expires:3650});
			}else{
				alert(data);
			}
		});
	});
});
</script>