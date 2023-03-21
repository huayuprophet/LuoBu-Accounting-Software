<?php
//权限设置，禁止非法阅读；
require("../config.inc.php");
if(!$arrGroup["序时账簿"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

echo '<div id="div0_V_upFile">';

echo '<form id="frm1_upFile" name="frm1" action="templates/V_upFileSubmit.php" method="post" enctype="multipart/form-data">
	<strong>[凭证'.$_GET["qj"].' - '.$_GET["pzh"].'#]</strong>
	<input name="up_file[]" type="file" multiple />
	<input id="qj_upFile" name="qj" type="hidden" value="'.$_GET["qj"].'" />
	<input id="pzh_upFile" name="pzh" type="hidden" value="'.$_GET["pzh"].'" />
	<input type="submit" name="submit" value="上传文件"/>
	<input type="button" id="toggle_V_upFile" value="隐藏列表" />    上传文件类型：pdf,jpg,png,gif,xlsx,xls,docx,doc,rar,zip,ofd
	</form><div id="div2_V_upFile">';

//写出与附件相关的链接并更新附件个数字段；
if(is_dir($arrPublicVar["filesystems"])){
	$qj = mysqli_real_escape_string($mysqli,$_GET["qj"]);
	$pzh = mysqli_real_escape_string($mysqli,$_GET["pzh"]);
	$strsqlfj='select * from v1_fj where qj="'.$qj.'" and pzh="'.$pzh.'"';
	$sqlfj=$mysqli->query($strsqlfj);
	$infofj=$sqlfj->fetch_array(MYSQLI_BOTH);
	$num_rows=$sqlfj->num_rows;
	$i=1;
	if($infofj){
		do{
			//获取文件的扩展名；
			$kzm=strrev(explode('.',strrev($infofj["fileaddress"]))[0]);
			//临时文件名称，包含扩展名称；
			$tempName = $qj.'_'.$pzh.'_'.$i.'_'.$arrPublicVar["userid"].'.'.$kzm;
			if($i==1){
				echo '<a href="#" tags="'.$tempName.'" class="yl_V_upFile">'.$infofj["filename"].'</a>&nbsp';
			}else{
				echo '&nbsp<a href="#" tags="'.$tempName.'" class="yl_V_upFile">'.$infofj["filename"].'</a>&nbsp';
			}
			if(file_exists($arrPublicVar["filesystems"].$infofj["fileaddress"])){
				copy($arrPublicVar["filesystems"].$infofj["fileaddress"],"../templates/access/".$tempName);//将文件写入临时文件夹
			}else{
				echo '源文件丢失！';
			}
			echo '<a href=templates/V_upFileDel.php?keyword='.$infofj["fileaddress"].' class="delfileaddress_V_upFile"><abbr title="删除附件"><img src="picture/b_drop.png" /></abbr></a>';
			$i++;
		}while($infofj=$sqlfj->fetch_array(MYSQLI_BOTH));
	}else{
		echo '未检索到相关凭证附件！';
	}
	//向数据库写入附件个数；
	$strsql='update v1 set fj="'.$num_rows.'" where qj="'.$qj.'" and pzh="'.$pzh.'"';
	$mysqli->query($strsql);
}else{
	echo '目录路径错误；';
}

echo '</div></div><div id="div1_V_upFile"><iframe id="pdfread_V_upFile" name="pdfread_V_upFile"></iframe></div>';
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//设置阅读器的大小；
	$("#pdfread_V_upFile").width($("#rightHand").width()-40).height($("#rightHand").height()-120);
	//附件列表切换；
	$("#toggle_V_upFile").click(function(){
		if($("#div2_V_upFile").is(":visible")){
			$(this).attr("value","显示列表");
			$("#div2_V_upFile").hide();
		}else{
			$(this).attr("value","隐藏列表");
			$("#div2_V_upFile").show();
		}
	})
	//在线预览PDF文件；
	$(".yl_V_upFile").click(function(){ 
		$("#pdfread_V_upFile").attr("src","./templates/access/"+$(this).attr("tags"));
		$("#div1_V_upFile").media();
		return false;
	});
	//附件的删除；
	$(".delfileaddress_V_upFile").click(function(){ 
		//删除前提醒；
		if(!confirm("您确信要删除该附件吗？")){
			return false;
		}
		var url = $(this).attr("href");
		$.get(url,function(data,textStatus){
			alert(data);
			var url="templates/V_upFile.php?qj="+$("#rightHand>div:visible #qj_upFile").val()+"&pzh="+$("#rightHand>div:visible #pzh_upFile").val();
			$(this).mytabs({strTitle:"附件管理",strUrl:url});
		});
		return false;
	});
	//表格提交，文件的上传；
	var options = { 
		beforeSubmit:showRequest, // pre-submit callback
		success:showResponse, // post-submit callback
	}; 
	$("#frm1_upFile").ajaxForm(options);
	// pre-submit callback 
	function showRequest(formData, jqForm, options){ 
		//alert('提交数据');
	} 
	// post-submit callback 
	function showResponse(responseText,statusText){
		//刷新附件管理页面；
		alert(responseText);
		var url="templates/V_upFile.php?qj="+$("#rightHand>div:visible #qj_upFile").val()+"&pzh="+$("#rightHand>div:visible #pzh_upFile").val();
		$(this).mytabs({strTitle:"附件管理",strUrl:url});
	} 
});
//-->
</script>