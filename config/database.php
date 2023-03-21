<style type="text/css"> 
.db_database {width:845px;height:262px;left:50%;top:50%;margin-top:5px;margin-left:-15px;border:0px solid #ccc;border-radius:8px 8px 8px 8px}
.db_database select{width:250px; height:30px}
.db_database input{width:250px; height:30px}
.db_database input[type="submit"]{height:30px}
a {font-weight:normal;}
</style>

<script type="text/javascript">
$(function(){ 
    var options = { 
        beforeSubmit:showRequest, // pre-submit callback
        success:showResponse, // post-submit callback
    }; 
    $("#popbox #dbform_database").ajaxForm(options); 
	
	//获取数据库列表；
	$("#popbox #getdblist_database").on("click",function(){
		//此时只要密码正确就能获取到数据库列表信息；
		if($("#popbox #rootpwd_database").val()==''){
			alert('密码不能为空！');
			$("#popbox #rootpwd_database").focus();
			return false;
		}
		$("#popbox #wait2_database").show();
		$strURL='config/databaseList.php?rootpwd='+$("#popbox #rootpwd_database").val();
		$("#popbox #dblist_database").load($strURL);
		$("#popbox #wait2_database").hide();
	});

	//自动录入超级账套固定信息；
	$("#superac_database").on("click",function(){
		$("#dbname_database").val("superac");
		$("#dbinfo_database").val("集团（总部）");
	});
}); 
// pre-submit callback 
function showRequest(formData, jqForm, options){ 
    //var queryString = $.param(formData); 
    //alert('About to submit: \n\n' + queryString); 
    //return true; 
	$("#popbox #wait1_database").show();
} 
// post-submit callback 
function showResponse(responseText, statusText){
	$("#popbox #wait1_database").hide();
	alert(responseText);
} 
</script>

<div class="db_database" align="center">
<form id="dbform_database" name="form1" action="config/databaseCreateSubmit.php" method="get">
<table border="0" cellspacing="0">
	<tr>
		<td height="40">&nbsp;&nbsp;<font color="black">账套类型（会计科目基础数据分类）:</font></td>
		<td height="40">
			<select name="dbtype" size="1" required>
				<option selected value="new">标准账套</option>
				<option value="demo">演示账套</option>
			</select>
		</td>
	</tr>
	<tr>
		<td height="40">&nbsp;&nbsp;<font color="black">数据库名称（字母+数字,不得使用中文）:</font></td>
		<td height="40">
			<input id="dbname_database" name="dbname" type="text" maxlength="12" required/>
			<input id="superac_database" type="button" value="superac" style="width:90px" title="自动录入超级账套信息"/>
		</td>
	</tr>
	<tr>
		<td height="40">&nbsp;&nbsp;<font color="black">中文简称（登录显示不同数据库的名称）:</font></td>
		<td height="40"><input id="dbinfo_database" name="dbinfo" type="text" maxlength="12" required/></td>
	</tr>
	<tr>
		<td height="40">&nbsp;&nbsp;<font color="black">公司全称（数据库中核算主体公司名称）:</font></td>
		<td height="40"><input name="dbgsmc" type="text" maxlength="25" required/></td>
	</tr>
	<tr>
		<td height="40">&nbsp;&nbsp;<font color="black">数据库密码（mysql 密码,长度不少于8位）:</font></td>
		<td height="40"><input id="rootpwd_database" name="rootpwd" type="password" maxlength="18" required /></td>
	</tr> 
	<tr>
		<td colspan="2" height="40" align="center">
			<img id="wait1_database" class="hide" src="picture/wait.gif" />
			<input name="ok" type="submit" value="创建新数据库" />
			<img id="wait2_database" class="hide" src="picture/wait.gif" />
			<input id="getdblist_database" type="button" value="获取数据库列表" />
		</td>
	</tr>
</table>
</form>
<div id="dblist_database"></div>
</div>