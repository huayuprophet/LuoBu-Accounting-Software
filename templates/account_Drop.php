<?php 
require("../config.inc.php");
if(!$arrGroup["会计科目"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$id=mysqli_real_escape_string($mysqli,$_GET["id"]);
$strsql='select * from account where id="'.$id.'"';
$sql=$mysqli->query($strsql);
$info=$sql->fetch_array(MYSQLI_BOTH);
if($info){ //如果存在数据；
?>
<form action="templates/account_DropSubmit.php" method="post" id="accountDropForm" name="accountDropForm">
<table align="center">
<tr><td colspan="2" align="center"><font size="+3">会计科目</font></td></tr>
<tr>
<td>ID（纯数字组成科目级次：4-2-2-2-2）</td><td><input name="id" type="text" maxlength="12" style="width:450px" readonly value="<?php echo $info["id"]; ?>" required /></td>
</tr>
<tr>
<td>名称（会计科目名称，不得使用：|→）</td><td><input name="name" type="text" maxlength="25" style="width:450px" value="<?php echo $info["name"]; ?>" required /></td>
</tr>
<tr>
<td>分类（会计科目五大标准分类）</td>
<td>
<select name="t" style="width:450px">
<option value="资产" <?php if($info["t"]=='资产'){echo 'selected="selected"';} ?>>资产</option>
<option value="负债" <?php if($info["t"]=='负债'){echo 'selected="selected"';} ?>>负债</option>
<option value="权益" <?php if($info["t"]=='权益'){echo 'selected="selected"';} ?>>权益</option>
<option value="成本" <?php if($info["t"]=='成本'){echo 'selected="selected"';} ?>>成本</option>
<option value="利润" <?php if($info["t"]=='利润'){echo 'selected="selected"';} ?>>利润</option>
</select>
</td>
</tr>
<tr>
<td>方向（会计科目借贷方向的指定）</td>
<td>
<select name="dc" style="width:450px">
<option value="借方" <?php if($info["dc"]=='借方'){echo 'selected="selected"';} ?>>借方</option>
<option value="贷方" <?php if($info["dc"]=='贷方'){echo 'selected="selected"';} ?>>贷方</option>
</select>
</td>
</tr>
<tr>
<td>基础资料<br/><br/>1、选择“是”带上项目核算<br/><br/>2、选择“否”取消项目核算</td>
<td align="left">
	<table>
	<tr>
	<td width="100px">
	数量外币
	</td>
	<td>
	<select name="slwb">
	<option value="0" <?php if($info["slwb"]==0){echo 'selected="selected"';} ?>>否</option>
	<option value="-1" <?php if($info["slwb"]==-1){echo 'selected="selected"';} ?>>是</option>
	</select>
	</td>
	<td width="100px"></td>
	<td width="100px">
	现金流量
	</td>
	<td>
	<select name="xjll">
	<option value="0" <?php if($info["xjll"]==0){echo 'selected="selected"';} ?>>否</option>
	<option value="-1" <?php if($info["xjll"]==-1){echo 'selected="selected"';} ?>>是</option>
	</select>
	</td>
	<tr>
	<td width="100px">
	客户
	</td>
	<td>
	<select name="kh">
	<option value="0" <?php if($info["kh"]==0){echo 'selected="selected"';} ?>>否</option>
	<option value="-1" <?php if($info["kh"]==-1){echo 'selected="selected"';} ?>>是</option>
	</select>
	</td>
	<td width="100px"></td>
	<td width="100px">
	个人
	</td>
	<td>
	<select name="gr">
	<option value="0" <?php if($info["gr"]==0){echo 'selected="selected"';} ?>>否</option>
	<option value="-1" <?php if($info["gr"]==-1){echo 'selected="selected"';} ?>>是</option>
	</select>
	</td>
	</tr>
	<tr>
	<td width="100px">
	存货
	</td>
	<td>
	<select name="ch">
	<option value="0" <?php if($info["ch"]==0){echo 'selected="selected"';} ?>>否</option>
	<option value="-1" <?php if($info["ch"]==-1){echo 'selected="selected"';} ?>>是</option>
	</select>
	</td>
	<td width="100px"></td>
	<td width="100px">
	供应商
	</td>
	<td>
	<select name="gys">
	<option value="0" <?php if($info["gys"]==0){echo 'selected="selected"';} ?>>否</option>
	<option value="-1" <?php if($info["gys"]==-1){echo 'selected="selected"';} ?>>是</option>
	</select>
	</td>
	</tr>
	<tr>
	<td width="100px">
	部门
	</td>
	<td>
	<select name="bm">
	<option value="0" <?php if($info["bm"]==0){echo 'selected="selected"';} ?>>否</option>
	<option value="-1" <?php if($info["bm"]==-1){echo 'selected="selected"';} ?>>是</option>
	</select>
	</td>
	<td width="100px"></td>
	<td width="100px">
	核算项目
	</td>
	<td>
	<select name="xm">
	<option value="0" <?php if($info["xm"]==0){echo 'selected="selected"';} ?>>否</option>
	<option value="-1" <?php if($info["xm"]==-1){echo 'selected="selected"';} ?>>是</option>
	</select>
	</td>
	</tr>
	</table>
</td>
</tr>
<tr>
<td colspan="2" align="center"><br /><br />
<?php
//已经审核的数据不允许删除；
if(is_null($info["chk"])){
    echo '<input name="drop" type="submit" value="删除" />';
}else{
	echo '<input name="drop" type="submit" disabled="disabled" value="删除" />';
}
$sql->free();
$mysqli->close();
?>
</td>
</tr>
</table>
</form>
<?php
}else{
	echo '未检测到相关数据，该数据可能被删除；';	
}
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//新增或保存
	var options = { 
		beforeSubmit:showRequest, // pre-submit callback
		success:showResponse, // post-submit callback
	}; 
	$("#accountDropForm").ajaxForm(options); 
});
// pre-submit callback 
function showRequest(formData, jqForm, options){ 

} 
// post-submit callback 
function showResponse(responseText, statusText){
	alert(responseText);
}
//-->
</script>