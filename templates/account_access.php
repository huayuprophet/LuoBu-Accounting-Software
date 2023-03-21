<?php 
require("../config.inc.php");
if(!$arrGroup["会计科目"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='GET'){
	
	//安全验证；
	$zd=mysqli_real_escape_string($mysqli,$_GET["zd"]);
	$tj=mysqli_real_escape_string($mysqli,$_GET["tj"]);
	$gjz=mysqli_real_escape_string($mysqli,$_GET["gjz"]);
	
	//构造查询SQL语句；
	switch($tj){
		case "包含":
			$strsql='select * from account where '.$zd.' like "%'.$gjz.'%" order by id asc';
			break;
		case "开头是":
		    $strsql='select * from account where '.$zd.' like "'.$gjz.'%" order by id asc';
			break;
		case "结尾是":
		    $strsql='select * from account where '.$zd.' like "%'.$gjz.'" order by id asc';
			break;
		case "等于":
			$strsql='select * from account where '.$zd.'="'.$gjz.'" order by id asc';
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
	
	//echo $strsql;
	
	$sql=$mysqli->query($strsql);
	$info=$sql->fetch_array(MYSQLI_BOTH);

	echo '<div align="left">';
	echo '<table id="tbl1_account_access" class="mytable">';
	
	if($info){ //如果存在数据；
	    echo '<thead><tr>
			  <td align="center">T</td>
			  <td>ID</td>
			  <td>名称</td>
			  <td>全称</td>
			  <td>分类</td>
			  <td>方向</td>
			  <td>状态</td>
			  <td>数量外币</td>
			  <td>现金流量</td>
			  <td>客户</td>
			  <td>个人</td>
			  <td>存货</td>
			  <td>供应商</td>
			  <td>部门</td>
			  <td>核算项目</td>
			  <td>录入</td>
			  <td>审核</td>
			  </tr></thead>';
		DO{
		   echo '<tr>';
		   echo '<td align="center">';
		   echo '<input type="checkbox" value="'.$info["id"].'" />';
		   echo '</td>';
		   echo '<td>'.$info["id"].'</td>';
		   echo '<td>'.$info["name"].'</td>';
		   echo '<td>'.$info["Fname"].'</td>';
		   echo '<td align="center">'.$info["t"].'</td>';
		   echo '<td align="center">'.$info["dc"].'</td>';
		   $strURL='id='.$info["id"];//只提供了参数部分；
		   echo '<td align="center">'.(($info["ty"]==-1)?'<a href="'.$strURL.'"><font color="red">停用</font></a>':'<a href="'.$strURL.'">正常</a>').'</td>';
		   echo '<td align="center">'.(($info["slwb"]==-1)?'<img src="templates/picture/yes.png" />':'').'</td>';
		   echo '<td align="center">'.(($info["xjll"]==-1)?'<img src="templates/picture/yes.png" />':'').'</td>';
		   echo '<td align="center">'.(($info["kh"]==-1)?'<img src="templates/picture/yes.png" />':'').'</td>';
		   echo '<td align="center">'.(($info["gr"]==-1)?'<img src="templates/picture/yes.png" />':'').'</td>';
		   echo '<td align="center">'.(($info["ch"]==-1)?'<img src="templates/picture/yes.png" />':'').'</td>';
		   echo '<td align="center">'.(($info["gys"]==-1)?'<img src="templates/picture/yes.png" />':'').'</td>';
		   echo '<td align="center">'.(($info["bm"]==-1)?'<img src="templates/picture/yes.png" />':'').'</td>';
		   echo '<td align="center">'.(($info["xm"]==-1)?'<img src="templates/picture/yes.png" />':'').'</td>';
		   echo '<td>'.$info["etr"].'</td>';
		   echo '<td>'.$info["chk"].'</td>';
		   echo '</tr>';
		}while($info=$sql->fetch_array(MYSQLI_BOTH));
		$sql->free();
		$mysqli->close();
	}else{
		echo '<tr>';
		echo '<td align="center">系统未检索到相关数据；<a href="javascript:openInsert()">新增</a></td>';
		echo '</tr>';
	}
	
	echo '</table>';  
	echo '</div>';
		  
}else{//非"提交"页面的初始化；
	echo '<div align="center"><table class="mytable"><tr><td align="center">请使用提交按钮检索相关数据；</td></tr></table></div>';
}
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//表格插件应用
	$("#rightHand>div:visible #tbl1_account_access").mytable();
	//分页跳转
	$("#rightHand>div:visible span").on("click","a",function(){
		$("#rightHand>div:visible .access2").load($(this).attr("href"));
		return false;
	});
	//会计科目信息停用操作
	$("#rightHand>div:visible #tbl1_account_access a").on("click",function(){
		strURL='templates/account_ty.php?'+$(this).attr("href");
		$.get(strURL,function(data,textStatus){
			alert(data);
			$("#rightHand>div:visible #cx").click();
			return false;
		});
		return false;
	});
});
//-->
</script>