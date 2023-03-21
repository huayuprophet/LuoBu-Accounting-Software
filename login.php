<?php session_start();?>

<style type="text/css">
.logindiv {position:absolute;width:350px;height:250px;left:50%;top:50%; 
margin-left:-170px;margin-top:-85px;border:0px solid #ccc;border-radius:8px 8px 8px 8px}
.logindiv img{width:80px; height:100px}
.logindiv select{width:180px; height:30px}
.logindiv input{width:180px; height:30px}
.logindiv input[type="submit"]{width:100px; height:30px}
a {font-weight:normal;}
</style>

<div class="logindiv">
<form id="loginform" name="form1" action="config.inc.php" method="post">
<table width="350" border="0" cellspacing="0">
	<tr>
		<td width="126" height="50">&nbsp;&nbsp;<font color="black">账&nbsp;套:</font></td>
		<td width="224" height="50"><input id="db_login" name="db" type="text" required/></td>
	</tr>
	<tr>
		<td width="126" height="50">&nbsp;&nbsp;<font color="black">用&nbsp;户:</font></td>
		<td width="224" height="50"><input name="user" type="text" maxlength="12" required/></td>
	</tr>
	<tr>
		<td width="126" height="50">&nbsp;&nbsp;<font color="black">密&nbsp;码:</font></td>
		<td width="224" height="50"><input name="pwd" type="password" maxlength="18" required/></td>
	</tr>
	<tr>
		<td colspan="2" height="50" align="center"><input name="ok" type="submit" value="登&nbsp;&nbsp;录"/></td>
	</tr>
</table>
</form>
</div>

<script type="text/javascript">

//自动加载上一次正确的数据库和用户名称；
if($.cookie("zjqData")!=null){
	$("#loginform [name='db']").val($.cookie("zjqData"));
	$("#loginform [name='user']").val($.cookie("zjqUser"));
	$("#loginform [name='pwd']").focus();
}

$(function(){ 
    var options = { 
        beforeSubmit:showRequest, // pre-submit callback
        success:showResponse, // post-submit callback
    }; 
    $("#loginform").ajaxForm(options); 
	//数据下拉提示：
	$("#db_login").getSuggestBox({RequestURI:"config.inc.php",FindSource:"db_login",Callback:function(arrtd){
		$("#db_login").val(arrtd[0]);
	}});
}); 
// pre-submit callback 
function showRequest(formData, jqForm, options){ 
    //var queryString = $.param(formData); 
    //alert('About to submit: \n\n' + queryString); 
    //return true; 
} 
// post-submit callback 
function showResponse(responseText, statusText){
	if(responseText=='ConnectError'){
		alert('数据库连接错误！');
	}else if(responseText.indexOf('Login：')!=-1){
		alert(responseText);
		$("#loginform [name='pwd']").focus();
	}else{
		$("#footerinformation").load("config/sys_getLogin.php");
		$.cookie("zjqData",$("#loginform [name='db']").val(),{expires:3650});
		$.cookie("zjqUser",$("#loginform [name='user']").val(),{expires:3650});
		$("div .popupclose").click();
	}
} 
</script>