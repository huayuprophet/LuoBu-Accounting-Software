<?php
require("../config.inc.php");
if(!$arrGroup['用户分组']){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//根据数据表中的数据写输出标准的树控件html元素；
$strsql='select Gid,Gname,Gcontent,Gvalue from `groups` where Gname="'.urldecode($_GET["name"]).'" order by Gid asc';
$sql=$mysqli->query($strsql);
echo '<form id="rightset_sys_groupget" action="config/sys_groupsubmit.php" method="post">
	<div id="menu_sys_groupgets">
	<input name="czgroup" type="submit" value="保存" />
	<input class="zk" type="button" value="全部展开"/>
	<input class="ss" type="button" value="全部收缩"/>
	<input class="xq" type="button" value="全部选取"/>
	<input class="qx" type="button" value="全部取消"/>
	<ul class="mytree">';
	$i=0;$j=0;
	while($info=$sql->fetch_array(MYSQLI_BOTH)){
		$keyValue=explode('=>',$info["Gcontent"]);
		$i=substr_count($keyValue[0],'-');
		if($i>$j){
			$tempM=$i-$j;//此处计算出有多少个封闭ul起点；
			for($tempN=1;$tempN<=$tempM;$tempN++){
				echo '<ul>';
			}
			echo '<li><input type="hidden" name="Gname" value="'.$info["Gname"].'" /><input type="checkbox" name="Gvalue[]" ';
			if($info["Gvalue"]==-1){echo 'checked="checked"';}
			echo ' value="'.$info["Gid"].'|'.$info["Gname"].'"';
			echo '/>'.$keyValue[1].'</li>';
		}else if($i<$j){
			$tempI=$j-$i;//此处计算出有多少个封闭ul回路；
			for($tempJ=1;$tempJ<=$tempI;$tempJ++){
				echo '</ul>';
			}
			echo '<li><input type="hidden" name="Gname" value="'.$info["Gname"].'" /><input type="checkbox" name="Gvalue[]" ';
			if($info["Gvalue"]==-1){echo 'checked="checked"';}
			echo ' value="'.$info["Gid"].'|'.$info["Gname"].'"';
			echo '/>'.$keyValue[1].'</li>';
		}else{
			echo '<li><input type="hidden" name="Gname" value="'.$info["Gname"].'" /><input type="checkbox" name="Gvalue[]" ';
			if($info["Gvalue"]==-1){echo 'checked="checked"';}
			echo ' value="'.$info["Gid"].'|'.$info["Gname"].'"';
			echo '/>'.$keyValue[1].'</li>';
		}
		$j=substr_count($info["Gcontent"],'-');
	}
echo '</ul></div></form>';
?>

<script type="text/javascript">
$(function(){
	//样式及通用动作调用；
	$("#menu_sys_groupgets").mytree();
	
	//保存
	var options = { 
		beforeSubmit:showRequest, // pre-submit callback
		success:showResponse, // post-submit callback
	}; 
	$("#rightset_sys_groupget").ajaxForm(options); 
	// pre-submit callback 
	function showRequest(formData, jqForm, options){ 
		//判断是否脱离框架；
		if($("#rightHand").length==0){
			alert("脱离框架，操作失败！");
			return false;
		}
	} 
	// post-submit callback 
	function showResponse(responseText, statusText){
		alert(responseText);
	} 
});
</script>