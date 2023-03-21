<?php
require("../config.inc.php");
?>
<script type="text/javascript">
<!--
$(function(){
	$("#rightHand>div:visible #sendpassword").click(function(){
		if($("#rightHand>div:visible #txtpassword1").val().length==0||$("#rightHand>div:visible #txtpassword2").val().length==0||$("#rightHand>div:visible #txtpassword3").val().length==0){
			alert('密码输入框不能为空！');
			return false;
		}
		if($("#rightHand>div:visible #txtpassword2").val()!=$("#rightHand>div:visible #txtpassword3").val()){
			alert('两次输入的新密码不一致！');
			return false;
		}
		$.post("config/sys_changePWDcheck.php",{
			txtpassword1:$("#rightHand>div:visible #txtpassword1").val(),
			txtpassword2:$("#rightHand>div:visible #txtpassword2").val()
		},function(data,textStatus){
			if(data.indexOf("成功")>-1){
			　　alert(data);
				$("#rightHand>div:visible #txtpassword1").val('');
				$("#rightHand>div:visible #txtpassword2").val('');
				$("#rightHand>div:visible #txtpassword3").val('');
			}else{
				alert(data);
			}
		});
	});
});
//-->
</script>

<form>
<table>
<tr>
<td colspan="2" align="left">修改当前用户的登录密码：</td>
</tr>
<tr>
<td align="left">旧密码：</td><td><input id="txtpassword1" maxlength="18" type="password" required /></td>
</tr>
<tr>
<td align="left">新密码：</td><td><input id="txtpassword2" maxlength="18" type="password" required /></td>
</tr>
<tr>
<td align="left">再次输入新密码：</td><td><input id="txtpassword3" maxlength="18" type="password" required /></td>
</tr>
<tr>
<td colspan="2" align="center" ><input id="sendpassword" type="button" value="提交" /></td>
</tr>
</table>
</form>