<?php
//页面 index.html（id="footerinformation"）的 div 获取有效的登录信息，包括正确的登录或者页面刷新时均取此页面数据；
session_start();
if(isset($_SESSION["db"])&&isset($_SESSION["userid"])&&isset($_SESSION["usergroup"])){
	echo '<script type="text/javascript">
			$(function(){ 
				$("#leftHand,#splitLine").show();
				$("#leftHand2").load("menu.html");
			});
		</script>';
	echo '<img src="picture/menu/s3.png" /> '.$_SESSION["username"].'，你好！';
	echo '｛<img src="picture/menu/s6.png" />'.$_SESSION["strCompanyName"].' <img src="picture/menu/s4.png" /> '.$_SESSION["userid"].' <img src="picture/menu/s2.png" /> '.$_SESSION["usergroup"].'｝';
	echo '｛<img src="picture/company.png" /> WEB系统开发框架 <img src="picture/browser.png" />通用财务软件系统<img src="picture/update.png" /> 版本：V4.0｝';
}else{
	echo '<script type="text/javascript">
			$(function(){ 
				$("#leftHand,#splitLine").hide();
				$("#leftHand2").empty();
				$("#splitLineMenu").text("");
				$("#tabMenu>li").remove();
				$("#rightHand>div").remove();
				$("#login").click();
			});
		</script>';
	echo '未登录';
}
?>