<?php
require("../config.inc.php");
if(!$arrGroup["序时账簿"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$objQj=new QJ();
$objQj->getQj('select distinct qj from v1 where chk is not null and acc is null order by qj');

echo '<form>
	  <div align="center">
	  <table>
	  <tr height="60px" valign="bottom">
	  <td>记账期间：</td>
	  <td>
	  <select id="qj_vaccount_1" size="1">';
	  foreach($objQj->arrayQj as $intqj){
		echo '<option value="'.$intqj.'">'.$intqj.'</option>';
	  }
echo '</select>
	  </td>
	  <td><input id="jz_vaccount_1" type="button" value="下一步" /></td>
	  </tr>
	  <tr height="40px" valign="bottom">
	  <td colspan="3">
	  <font color="red">1>记账期间内所有的凭证需审核；</font>
	  </td>
	  </tr>
	  <tr height="40px" valign="bottom">
	  <td colspan="3">
	  <font color="red">2>记账期间所有凭证须连续编号；</font>
	  </td>
	  </tr>
	  <tr height="40px" valign="bottom">
	  <td colspan="3">
	  <font color="red">3>成功记账的期间凭证只能查看；</font>
	  </td>
	  </tr>
	  <tr height="40px" valign="bottom">
	  <td colspan="3">
	  <font color="red">4>记账在线等待时间最长10分钟；</font>
	  </td>
	  </tr>
	  </table>
	  </div>
	  </form>';
		
unset($objQj);
?>

<script language="javascript" type="text/javascript">
<!--

var c=0;
var t;
function timedCount(){
	document.getElementById('jz_vaccount_1').value='正在计算，执行时间：'+c+'秒。'
	c=c+1;
	t=setTimeout("timedCount()",1000)
}

function stopCount(){clearTimeout(t)}
	
$(function(){
	$("#popbox #jz_vaccount_1").click(function(){
		$.ajax({
			url: "templates/V_account_2.php",
			type: "post",
			timeout: 600000,//1秒等于1000毫秒，此处600000毫秒相当于请求时间为10分钟；
			data: {qj:$("#popbox #qj_vaccount_1").val()},
			dataType: "text",
			beforeSend:function(){
				//记账的期间不能为空；
				if($("#popbox #qj_vaccount_1").val()==''){
					alert('必需指定一个期间！');
					$("#popbox #qj_vaccount_1").focus();
					return false;
				}
				//提交后开始计时；
				$("#popbox #jz_vaccount_1").attr("disabled",true);
				timedCount();
			},
			success: function(data){
				alert('★记账期间：'+$("#popbox #qj_vaccount_1").val()+'\n★信息反馈：'+data);
				stopCount();//程序执行完时结束计时；
				$("#popbox div.popupclose").trigger("click");
				$("#rightHand>div:visible #tj_Vbook").click();
			  },
			error: function(){
				stopCount();//程序执行完时结束计时；
				alert('记账操作未成功执行！');
			}
        });
	});
});
</script>