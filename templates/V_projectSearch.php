<?php
require("../config.inc.php");
if(!$arrGroup["会计凭证"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//将请求的项目名称转换成实际表名；
switch($_GET["searchTable"]){
	case "现金流量":
	    $tblName="jc_xjll";
	    break;
	case "客户":
	    $tblName="jc_kh";
	    break;
	case "个人":
	    $tblName="jc_gr";
	    break;
	case "存货":
	    $tblName="jc_ch";
	    break;
	case "供应商":
	    $tblName="jc_gys";
	    break;
	case "部门":
	    $tblName="jc_bm";
	    break;
	case "核算项目":
	    $tblName="jc_xm";
	    break;
	default: 
	    $tblName="";
	    break;
}

$searchText=mysqli_real_escape_string($mysqli,$_GET["searchText"]);
$mysqli->query("call xmz('".$_GET["searchKM"]."','".$_GET["searchFX"]."','".$tblName."','".$searchText."')");

//获取符合条件记录总数的SQL语句；
$strSql='select dm from ps where dm in(select id from '.$tblName.' where ty=1) group by dm,dmf';

//=====================数据分页代码（开始）=====================
$page=isset($_GET['page'])?$_GET['page']:1;//页码数
//echo '页码数：'.$page.',';
$limitStart=$page==1?0:$arrPublicVar["comboRows"]*($page-1);//SQL语句中limit起始数
//echo 'limit起始数'.$limitStart.'<br/>';
$totalRecords=mysqli_num_rows($mysqli->query($strSql));//总页数
$p=new Page($totalRecords,10,$page,$arrPublicVar["comboRows"]);
//=====================数据分页代码（结束）=====================

//在常用strSql语句后面加入limit语句;
$strSql='select dm as id,dmf as name,sum(sl) as sl,round(sum(je),2) as je from ps where dm in(select id from '.$tblName.' where ty=1) group by dm,dmf order by id desc limit '.$limitStart.','.$arrPublicVar["comboRows"];
//$strSql='select id,name from '.$tblName.' where id like "'.$_GET[searchText].'%" or name like "%'.$_GET[searchText].'%" order by id asc';
//echo $strSql;
$sql=$mysqli->query($strSql);
$info=$sql->fetch_array(MYSQLI_BOTH);
echo '<table border="1" cellspacing="0" bgcolor="white" id="tbl3_V1" class="mytable" style="float:left; margin:0px; padding:0px;">';
if($info){ //如果存在数据；
    do{
		echo '<tr title="'.$_GET["searchTable"].'" onclick="trClickXM(this)">';
		echo '<td>'.$info["id"].'</td>';
		echo '<td>'.$info["name"].'</td>';
		echo '<td align="center">'.$info["sl"].'</td>';
		echo '<td align="right">'.toMoney($info["sl"]==0?0:$info["je"]/$info["sl"],2).'</td>';
		echo '<td align="right">'.toMoney($info["je"],2).'</td>';
		echo '</tr>';
	}while($info=$sql->fetch_array(MYSQLI_BOTH));
	//写最后一行内容；
	echo '<tr>';
	echo '<td colspan="5">';
	echo '<div style="float:left">'.$p->showPages(3).'</div>';
	echo '</td>';
	echo '</tr>';
}else{//如果不存在数据，直接写最后一行；
    //写最后一行内容；
	echo '<tr>';
	echo '<td>';
	echo '<div style="float:left">无满足条件的记录</div>';
	echo '</td>';
	echo '</tr>';
}
echo '</table>';

$sql->free();
$mysqli->close();
?>