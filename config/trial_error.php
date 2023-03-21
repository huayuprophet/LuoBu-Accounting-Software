<?php
require("../config.inc.php");
if($_SESSION["userid"]!='1001'){
	if(!$arrGroup['用户分组']){
		exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
	}
}

try{
	
	//禁止缓存；
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pramga: no-cache");
	
	//登录错误信息表中的数据展示；
	echo '系统登录错误（最大6次）↓';
	$xml_1=simplexml_load_file('trial_error1.xml');
	echo '<table id="dbList1_trial_error">';
	echo '<thead><tr><td>IP地址</td><td>登录日期</td><td>错误次数</td><td>删除×</td></tr></thead>';
	foreach($xml_1->sub as $subs){
		echo '<tr><td>'.$subs->ips.'</td><td>'.$subs->dts.'</td><td>'.$subs->nums.'</td>';
		echo '<td align="center"><a name="sc1_trial_error" href="#" tags="'.$subs->ips.'">删除</a></td></tr>';
	}
	echo '</table>';
	
	echo '数据库密码错误（最大3次）↓';
	$xml_0=simplexml_load_file('trial_error0.xml');
	echo '<table id="dbList0_trial_error">';
	echo '<thead><tr><td>IP地址</td><td>登录日期</td><td>错误次数</td><td>删除×</td></tr></thead>';
	foreach($xml_0->sub as $subs){
		echo '<tr><td>'.$subs->ips.'</td><td>'.$subs->dts.'</td><td>'.$subs->nums.'</td>';
		echo '<td align="center"><a name="sc0_trial_error" href="#" tags="'.$subs->ips.'">删除</a></td></tr>';
	}
	echo '</table>';
	
}catch(Exception $e){
    echo 'Caught exception: ',$e->getMessage(),"\n";
}
?>

<script type="text/javascript">
$(function(){
	$("#popbox #dbList1_trial_error").mytable({interlacedColor:true,selectedColor:true,clickedColor:true,columnWidth:false});
	$("#popbox #dbList0_trial_error").mytable({interlacedColor:true,selectedColor:true,clickedColor:true,columnWidth:false});
	
	//删除 xml 文件中的对应的数据信息；
	$("#popbox #dbList1_trial_error a[name=sc1_trial_error]").on("click",function(){
		//删除前提醒；
		if(!confirm("您确信要删除【"+$(this).attr("tags")+"】错误登录信息吗？")){
			return false;
		}
		$.get("config/trial_error_1.php",{
			ips : $(this).attr("tags"),
		},function(data,textStatus){
			alert(data);
		});
	});
	$("#popbox #dbList0_trial_error a[name=sc0_trial_error]").on("click",function(){
		//删除前提醒；
		if(!confirm("您确信要删除【"+$(this).attr("tags")+"】错误登录信息吗？")){
			return false;
		}
		$.get("config/trial_error_0.php",{
			ips : $(this).attr("tags"),
		},function(data,textStatus){
			alert(data);
		});
	});
});
</script>