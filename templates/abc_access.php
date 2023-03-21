<?php 
/*
成型模板，无需修改，直接套用；
自动生成页面，展示字段所有信息；
该模板的请求来源于 abc.php 查询提交；
*/
require("../config.inc.php");
if(!$arrGroup[$_REQUEST["rht"]]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='GET'){
	
	//安全验证；
	$tbl=mysqli_real_escape_string($mysqli,$_GET["tbl"]);
	$zd=mysqli_real_escape_string($mysqli,$_GET["zd"]);
	$tj=mysqli_real_escape_string($mysqli,$_GET["tj"]);
	$gjz=mysqli_real_escape_string($mysqli,$_GET["gjz"]);
	
	//构造查询SQL语句；
	switch($tj){
		case "包含":
			$strsql='select * from '.$tbl.' where '.$zd.' like "%'.$gjz.'%"';
			break;
		case "等于":
			$strsql='select * from '.$tbl.' where '.$zd.'="'.$gjz.'"';
			break;
		case "大于":
			$strsql='select * from '.$tbl.' where '.$zd.'>"'.$gjz.'"';
			break;
		case "大于等于":
			$strsql='select * from '.$tbl.' where '.$zd.'>="'.$gjz.'"';
			break;
		case "小于":
			$strsql='select * from '.$tbl.' where '.$zd.'<"'.$gjz.'"';
			break;
		case "小于等于":
			$strsql='select * from '.$tbl.' where '.$zd.'<="'.$gjz.'"';
			break;
		case "开头是":
		    $strsql='select * from '.$tbl.' where '.$zd.' like "'.$gjz.'%"';
			break;
		case "结尾是":
		    $strsql='select * from '.$tbl.' where '.$zd.' like "%'.$gjz.'"';
			break;
		default://非以上结果的处理；
		    echo '<div>get取值不正确</div>';
	}
	
	//=================================数据分页代码（开始）=================================
	$page=isset($_GET['page'])?$_GET['page']:1;//页码数
	//echo '页码数：'.$page.',';
	$limitStart=$page==1?0:$arrPublicVar["tableRows"]*($page-1);//SQL语句中limit起始数
	//echo 'limit起始数'.$limitStart.'<br/>';
	$totalRecords=mysqli_num_rows($mysqli->query($strsql));//总页数
	$p=new Page($totalRecords,$arrPublicVar["tableRows"],$page,$arrPublicVar["tableRows"]);
	$strsql.=' limit '.$limitStart.','.$arrPublicVar["tableRows"];//在常用strsql语句后面加入limit语句
	echo $totalRecords==0?"":$p->showPages(1);//自动生成导航条
	//=================================数据分页代码（结束）=================================
	
	//echo '<br/>'.$strsql;
	
	$sql=$mysqli->query($strsql);
	
	//获取表列相关信息；
	$strsqlrow='show full columns from '.$tbl.' from '.$_SESSION["db"];
	$sqlrow=$mysqli->query($strsqlrow);
	$num_rows=$mysqli->affected_rows;

	echo '<div align="left"><table id="'.$tbl.'" class="mytable">';
	
	echo '<thead><tr><td align="center">T</td>';
		while($inforow=$sqlrow->fetch_array(MYSQLI_BOTH)){
			echo '<td>'.$inforow["Comment"].'</td>';
		}
	echo '</tr></thead>';
	
	while($info=$sql->fetch_array(MYSQLI_BOTH)){
		//mysql_data_seek($sqlrow,0); //指针复位
		echo '<tr><td align="center"><input type="checkbox" value="'.$info[0].'" /></td>';
		for ($x=0;$x<$num_rows;$x++) {
			echo '<td>'.$info[$x].'</td>';
		}
		echo '</tr>';
	}
	
	echo '</table></div>';
	
	$sql->free();
	$sqlrow->free();
	$mysqli->close();
}else{//非"提交"页面的初始化；
	echo '<div align="center"><table class="mytable"><tr><td align="center">请使用提交按钮检索相关数据；</td></tr></table></div>';
}
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//表格插件应用
	$("#rightHand>div:visible .mytable").mytable({a:true,b:true,c:true,d:true});
	//分页跳转
	$("#rightHand>div:visible span").on("click","a",function(){
		$("#rightHand>div:visible .access2").load($(this).attr("href"));
		return false;
	});
});
//-->
</script>