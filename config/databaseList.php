<?php

set_time_limit(180);

try{
	//禁止缓存；
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pramga: no-cache");

	require_once("../config/conf.php");
	
	//数据库密码验证；
	if(!preg_match("/^.{8,}$/",$_GET["rootpwd"])){
		exit('mysql 密码,长度不少于8位！');
	}
	
	$xml_TE=simplexml_load_file('trial_error0.xml');
	
	//数据库链接；
	$mysqli=@new mysqli("127.0.0.1","root",$_GET["rootpwd"]);
	
	//时区设置；
	date_default_timezone_set("PRC");
	$currentDate=date("Y-m-d");
	
	// Works as of PHP 5.2.9 and 5.3.0.
	if($mysqli->connect_error){
		//向服务器请求登录出错的次数；
		$hasNote=false;
		foreach($xml_TE->children() as $layer){
			if($layer->ips==getIp()){
				if((int)$layer->nums < 3){
					$layer->dts=$currentDate;
					$layer->nums=(int)$layer->nums+1;
					$xml_TE->asXML('trial_error0.xml');
					exit('Login：密码错误，'.$layer->nums.'(出错次数)/3(总次数)');
				}else{
					exit('Login：密码错误超出3次，系统锁死!');
				}
				$hasNote=true;
			}
		}
		if(!$hasNote){
			$xmlsub=$xml_TE->addChild('sub');
			$xmlsub->addChild('ips',getIp());
			$xmlsub->addChild('dts',$currentDate);
			$xmlsub->addChild('nums',1);
			$xml_TE->saveXML('trial_error0.xml');
			exit('Login：密码错误，1(出错次数)/3(总次数)');
		}
		//die(mysqli_connect_error());
	}else{
		//向服务器验证是否有锁死的情况；
		$i=0;
		foreach($xml_TE->sub as $sub){
			if($sub->ips==getIp()){
				if((int)$sub->nums == 3){
					exit('Login：密码错误超出3次，系统锁死!');
				}else{
					unset($xml_TE->sub[$i]);
					$xml_TE->asXML('trial_error0.xml');
					break;
				}
			}
			$i++;
		}
	}
	
	/*
	//db.xml文件中的数据展示；
	$xml=simplexml_load_file('db.xml');
	$xmlarray=array();
	//将$xml转化成键值对应的数组，然后排序，最后写入下拉控件中；
	foreach($xml->children() as $layer_one){
		$xmlarray["$layer_one->tbl"]="$layer_one->name";
	}
	asort($xmlarray);
	*/

	$strsql='select id,dbname from superac.dbinfo order by id';
	$sql=$mysqli->query($strsql);

	echo '<hr width="800">';
	
	echo '<table id="dbList_databaseList" class="mytable">';
	echo '<thead><tr><td>数据库</td><td>数据库标签</td><td>备份↑</td><td>还原↓</td><td>修改√</td><td>删除×</td></tr></thead>';
	/*
	foreach($xmlarray as $k=>$v){ 
		echo '<tr><td>'.$k.'</td><td>'.$v.'</td>';
		echo '<td align="center"><a name="bf" href="#" tags="'.$k.'">备份</a></td>';
		echo '<td align="center"><a name="hy" href="#" tags="'.$k.'">还原</a></td>';
		echo '<td align="center"><a name="xg" href="#" tags="'.$k.'" txts="'.$v.'">修改</a></td>';
		echo '<td align="center"><a name="sc" href="#" tags="'.$k.'">删除</a></td></tr>';
	}
	*/

	if($sql){
		while($info=$sql->fetch_array(MYSQLI_BOTH)){
			echo '<tr><td>'.$info["id"].'</td><td>'.$info["dbname"].'</td>';
			echo '<td align="center"><a name="bf" href="#" tags="'.$info["id"].'">备份</a></td>';
			echo '<td align="center"><a name="hy" href="#" tags="'.$info["id"].'">还原</a></td>';
			echo '<td align="center"><a name="xg" href="#" tags="'.$info["id"].'" txts="'.$info["dbname"].'">修改</a></td>';
			echo '<td align="center"><a name="sc" href="#" tags="'.$info["id"].'">删除</a></td></tr>';
		}
	}

	echo '</table>';
	
	$mysqli->close();
	$mysqli = null;
	
	//写一个待提交的form，主要记录还原数据库的相关信息；
	echo '<hr width="800">';
	
	echo '<form id="bf_databaseList" action="config/databasebfhy.php" method="post">
		<table>
		<tr><td><input type="hidden" id="rootpwd_databaseList" name="rootpwd_databaseList" value="" /></td></tr>
		<tr><td>
		将要还原数据库：<input type="text" id="dbname_databaseList" name="dbname_databaseList" readonly="readonly" value="" />
		</td></tr>
		<tr><td>请选择还原文件：<input type="file" id="file_databaseList" name="file_databaseList"></td></tr>
		<tr><td><img id="wait_databaseList" class="hide" src="picture/wait.gif" /><input name="tj_databaseList" type="submit" value="马上还原数据库" /></td></tr>
		</table>
		</form>';
	
	echo '<table>
		<tr><td>1、</td><td>备份或还原操作后请耐心等待系统提示！</td></tr>
		<tr><td>2、</td><td>删除数据库不会删除服务器对应的文件！</td></tr>
		<tr><td>3、</td><td>为了安全起见，建议删除数据库前先进行备份！</td></tr>
		</table>';
	
}catch(Exception $e){
    echo 'Caught exception: ',$e->getMessage(),"\n";
}
?>

<script type="text/javascript">
$(function(){
	$("#popbox #dbList_databaseList").mytable({interlacedColor:true,selectedColor:true,clickedColor:true,columnWidth:false});
	
	//数据库备份；
	$("#popbox #dbList_databaseList a[name=bf]").on("click",function(){
		//前置等待
		$(this).prepend('<img class="waits_databaseList" src="picture/wait.gif" />');
		$.get("config/databasebf.php",{
			dbname : $(this).attr("tags"),
			rootpwd : $("#popbox #rootpwd_database").val()
		},function(data,textStatus){
			$("#popbox .waits_databaseList").remove();
			alert(data);
		});
	});
	
	//数据库还原；
	$("#popbox #dbList_databaseList a[name=hy]").on("click",function(){
		//自动更新将要还原的数据库所需信息；
		$("#popbox #rootpwd_databaseList").val($("#popbox #rootpwd_database").val());
		$("#popbox #dbname_databaseList").val($(this).attr("tags"));
	});
	
	//异步提交Form信息；
	var options = { 
	beforeSubmit:showRequest, // pre-submit callback
	success:showResponse, // post-submit callback
	}; 
	$("#popbox #bf_databaseList").ajaxForm(options); 
	// pre-submit callback 
	function showRequest(formData, jqForm, options){
		if($("#popbox #rootpwd_database").val()==""){
			alert("请输入请求密码！");
			return false;
		}
		if($("#popbox #dbname_databaseList").val()==""){
			alert("请选择将要还原数据库！");
			return false;
		}
		if($("#popbox #file_databaseList").val()==""){
			alert("请选择还原文件！");
			return false;
		}
		if(!confirm("您确信要还原【"+$("#popbox #dbname_databaseList").val()+"】数据库吗？")){
			return false;
		}
		//显示等待
		$("#popbox #wait_databaseList").show();
	} 
	// post-submit callback
	function showResponse(responseText, statusText){
		//关闭等待
		$("#popbox #wait_databaseList").hide();
		alert(responseText);
	}
	
	//修改数据库中超级数据表信息；
	$("#popbox #dbList_databaseList a[name=xg]").on("click",function(){
		var r=prompt("请输入新的标签名称:",$(this).attr("txts"));
		if(r){
			$.post("config/databaseEdit.php",{
				id:$(this).attr("tags"),
				name:r,
				rootpwd:$("#popbox #rootpwd_database").val()
			},function(data,textStatus){
				$("#popbox #getdblist_database").click();
				alert(data);
			});
		}
	});
	
	//删除数据库及超级账套中的数据库信息；
	$("#popbox #dbList_databaseList a[name=sc]").on("click",function(){
		//删除前提醒；
		if(!confirm("您确信要删除【"+$(this).attr("tags")+"】数据库吗？")){
			return false;
		}
		//前置等待
		$(this).prepend('<img class="waits_databaseList" src="picture/wait.gif" />');
		$.get("config/databaseDropSubmit.php",{
			dbname : $(this).attr("tags"),
			rootpwd : $("#popbox #rootpwd_database").val()
		},function(data,textStatus){
			$("#popbox .waits_databaseList").remove();
			alert(data);
			$("#popbox #getdblist_database").click();
		});
	});
});
</script>