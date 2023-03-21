<?php
require("../config.inc.php");
if(!$arrGroup["项目账表"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if(($_SERVER['REQUEST_METHOD']=='POST')&&isset($_POST["qj0"])&&isset($_POST["qj1"])){
	//项目名称转换；
	switch($_POST["xmName"]){
		case "xjll":
			$col='xjll';
			$colValue='现金流量';
			break;
		case "kh":
			$col='kh';
			$colValue='客户';
			break;
		case "gr":
			$col='gr';
			$colValue='个人';
			break;
		case "ch":
			$col='ch';
			$colValue='存货';
			break;
		case "gys":
			$col='gys';
			$colValue='供应商';
			break;
		case "bm":
			$col='bm';
			$colValue='部门';
			break;
		case "xm":
			$col='xm';
			$colValue='核算项目';
			break;
		default:
			break;
	}
	//查询字段；
	switch($_POST["serach"]){
		case "id":
			$serach1='ID';
			break;
		case "name":
			$serach1='名称';
			break;
		default:
			break;
	}
	//查询条件；	
	switch($_POST["cp"]){
		case 1:
			$cp1='包含';
			break;
		case 2:
			$cp1='等于';
			break;
		case 3:
			$cp1='开关是';
			break;
		case 4:
			$cp1='结尾是';
			break;
		default:
			break;
	}
	//是否含有数量外币列；
	switch($_POST["slwb"]){
		case 0:
			$slwb1='不含数量外币列';
			break;
		case 1:
			$slwb1='含数量外币列';
			break;
		default:
			break;
	}
	//会计科目字符串；
	//print_r($_POST["km_Vbook_xmyrb"]);
	$kmkm='';
	foreach($_POST["km_Vbook_xmyrb"] as $value){
		if($kmkm==''){
			$kmkm = $value;
		}else{
			$kmkm .=','.$value;
		}
	}
	
	//权限限定，与基础资料菜单名称是否勾选相对应，采用echo而不采用exit是允许后面的js代码能够写出来；
	if(@!$arrGroup[$colValue]){
		echo '<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>';
	}else{
		//提交数据成功，先构造查询凭证表的SQL语句，然后将查询到的相关数据写入table；
		$qj0=mysqli_real_escape_string($mysqli,$_POST["qj0"]);
		$qj1=mysqli_real_escape_string($mysqli,$_POST["qj1"]);
		$keyword=mysqli_real_escape_string($mysqli,$_POST["keyword"]);
		
		$strsql7='期间：'.$qj0.'-'.$qj1.'；项目：'.$colValue.'；科目：'.$kmkm.'；'.$serach1.'->'.$cp1.'->'.$keyword.'；'.$slwb1;
		
		echo '<div align="left">
			<a id="output_xmyrb" href="#">导出数据</a>|
			<a id="print_xmyrb" href="#">打印数据</a>
			</div></br>';
		
		echo '<div id="div1_xmyrb" align="left">';
			
		//调用生成项目余额表的存储过程，生成xmyrb临时表；
		$mysqli->query("call xmyrbs('".$qj0."','".$qj1."','".$col."','".$_POST["serach"]."','".$_POST["cp"]."','".$keyword."')");
		
		$strsql='select km,kma,xm,xms,dc,sum(slwb0) as slwb0,sum(dr0) as dr0,sum(cr0) as cr0,sum(ds1) as ds1,sum(dr1) as dr1,sum(cs1) as cs1,sum(cr1) as cr1 from xmyrb where km in ('.$kmkm.') group by km,xm order by km,xm;';
		//echo $strsql;
		
		$sql=$mysqli->query($strsql);
		$info=$sql->fetch_array(MYSQLI_BOTH);

		if($info){ //如果存在数据；
			//汇总金额计算变量；
			$qcyr=0;
			$sumdr=0;
			$sumcr=0;
			$qmyr=0;
			if($_POST["slwb"]==0){//不含数量外币列；
				echo '<table id="tbl1_xmyrb" align="left">
					<caption>
					<h1>项目余额表</h1>
					<div align="left">会计主体：<u>'.$_SESSION["strCompanyName"].'</u></div>
					<div align="left">查询条件：<u>'.$strsql7.'</u></div>
					</caption>';
				echo '<thead><tr>
					<td align="center">会计科目</td>
					<td align="center">科目全称</td>
					<td align="center">项目代码</td>
					<td align="center">项目名称</td>
					<td align="center">方向</td>
					<td align="center">期初余额</td>
					<td align="center">借方金额</td>
					<td align="center">贷方金额</td>
					<td align="center">期末余额</td>
					</tr></thead><tbody>';
				do{
					if($info["dc"]=="借方"){
						$strget='templates/Vbook_xmyrbsub.php?km='.$info["km"].'&slwb=a&yr='.@round($info["dr0"]-$info["cr0"],2).'&qj0='.$_POST["qj0"].'&qj1='.$_POST["qj1"].'&xm='.$_POST["xmName"].'&xms='.$info["xm"];
						echo '<tr>';
						echo '<td align="left">'.$info["km"].'</td>';	
						echo '<td align="left">'.$info["kma"].'</td>';
						echo '<td align="left"><a href='.$strget.' target="_blank">'.$info["xm"].'</a></td>';	
						echo '<td align="left">'.$info["xms"].'</td>';
						echo '<td align="center">'.$info["dc"].'</td>';
						echo '<td align="right">'.@toMoney($info["dr0"]-$info["cr0"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["dr1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["cr1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["dr0"]-$info["cr0"]+$info["dr1"]-$info["cr1"],2).'</td>';
						echo '</tr>';
						$qcyr+=$info["dr0"]-$info["cr0"];
						$sumdr+=$info["dr1"];
						$sumcr+=$info["cr1"];
						$qmyr+=$info["dr0"]-$info["cr0"]+$info["dr1"]-$info["cr1"];
					}else{
						$strget='templates/Vbook_xmyrbsub.php?km='.$info["km"].'&slwb=a&yr='.@round($info["cr0"]-$info["dr0"],2).'&qj0='.$_POST["qj0"].'&qj1='.$_POST["qj1"].'&xm='.$_POST["xmName"].'&xms='.$info["xm"];
						echo '<tr>';
						echo '<td align="left">'.$info["km"].'</td>';	
						echo '<td align="left">'.$info["kma"].'</td>';
						echo '<td align="left"><a href='.$strget.' target="_blank">'.$info["xm"].'</a></td>';	
						echo '<td align="left">'.$info["xms"].'</td>';
						echo '<td align="center">'.$info["dc"].'</td>';
						echo '<td align="right">'.@toMoney($info["cr0"]-$info["dr0"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["dr1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["cr1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["cr0"]-$info["dr0"]+$info["cr1"]-$info["dr1"],2).'</td>';
						echo '</tr>';
						$qcyr+=$info["cr0"]-$info["dr0"];
						$sumdr+=$info["dr1"];
						$sumcr+=$info["cr1"];
						$qmyr+=$info["cr0"]-$info["dr0"]+$info["cr1"]-$info["dr1"];
					}
				}while($info=$sql->fetch_array(MYSQLI_BOTH));
				echo '</tbody><tfoot><tr>
				<td></td>
				<td></td>
				<td align="center">小计：</td>
				<td></td>
				<td></td>
				<td align="right">'.@toMoney($qcyr,2).'</td>
				<td align="right">'.@toMoney($sumdr,2).'</td>
				<td align="right">'.@toMoney($sumcr,2).'</td>
				<td align="right">'.@toMoney($qmyr,2).'</td>
				</tr></tfoot>';
			}else{//包含数量外币列；
				echo '<table id="tbl2_xmyrb" align="left">
					<caption>
					<h1>项目余额表</h1>
					<div align="left">会计主体：<u>'.$_SESSION["strCompanyName"].'</u></div>
					<div align="left">查询条件：<u>'.$strsql7.'</u></div>
					</caption>';
				echo '<thead><tr>
					<td align="center">会计科目</td>
					<td align="center">科目全称</td>
					<td align="center">项目代码</td>
					<td align="center">项目名称</td>
					<td align="center">方向</td>
					<td align="center">期初数量</td>
					<td align="center">期初单价</td>
					<td align="center">期初余额</td>
					<td align="center">借方数量</td>
					<td align="center">借方单价</td>
					<td align="center">借方金额</td>
					<td align="center">贷方数量</td>
					<td align="center">贷方单价</td>
					<td align="center">贷方金额</td>
					<td align="center">期末数量</td>
					<td align="center">期末单价</td>
					<td align="center">期末余额</td>
					</tr></thead><tbody>';
				do{
					if($info["dc"]=="借方"){
						$strget='templates/Vbook_xmyrbsub.php?km='.$info["km"].'&slwb='.$info["slwb0"].'&yr='.@round($info["dr0"]-$info["cr0"],2).'&qj0='.$_POST["qj0"].'&qj1='.$_POST["qj1"].'&xm='.$_POST["xmName"].'&xms='.$info["xm"];
						echo '<tr>';
						echo '<td align="left">'.$info["km"].'</td>';	
						echo '<td align="left">'.$info["kma"].'</td>';
						echo '<td align="left"><a href='.$strget.' target="_blank">'.$info["xm"].'</a></td>';	
						echo '<td align="left">'.$info["xms"].'</td>';
						echo '<td align="center">'.$info["dc"].'</td>';
						echo '<td align="center">'.@round($info["slwb0"],2).'</td>';
						echo '<td align="right">'.@toMoney(($info["dr0"]-$info["cr0"])/$info["slwb0"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["dr0"]-$info["cr0"],2).'</td>';
						echo '<td align="center">'.@round($info["ds1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["dr1"]/$info["ds1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["dr1"],2).'</td>';
						echo '<td align="center">'.@round($info["cs1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["cr1"]/$info["cs1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["cr1"],2).'</td>';
						echo '<td align="center">'.@round($info["slwb0"]+$info["ds1"]-$info["cs1"],2).'</td>';
						echo '<td align="right">'.@toMoney(($info["dr0"]-$info["cr0"]+$info["dr1"]-$info["cr1"])/($info["slwb0"]+$info["ds1"]-$info["cs1"]),2).'</td>';
						echo '<td align="right">'.@toMoney($info["dr0"]-$info["cr0"]+$info["dr1"]-$info["cr1"],2).'</td>';
						echo '</tr>';
						$qcyr+=$info["dr0"]-$info["cr0"];
						$sumdr+=$info["dr1"];
						$sumcr+=$info["cr1"];
						$qmyr+=$info["dr0"]-$info["cr0"]+$info["dr1"]-$info["cr1"];
					}else{
						$strget='templates/Vbook_xmyrbsub.php?km='.$info["km"].'&slwb='.$info["slwb0"].'&yr='.@round($info["cr0"]-$info["dr0"],2).'&qj0='.$_POST["qj0"].'&qj1='.$_POST["qj1"].'&xm='.$_POST["xmName"].'&xms='.$info["xm"];
						echo '<tr>';
						echo '<td align="left">'.$info["km"].'</td>';	
						echo '<td align="left">'.$info["kma"].'</td>';
						echo '<td align="left"><a href='.$strget.' target="_blank">'.$info["xm"].'</a></td>';	
						echo '<td align="left">'.$info["xms"].'</td>';
						echo '<td align="center">'.$info["dc"].'</td>';
						echo '<td align="center">'.@round($info["slwb0"],2).'</td>';
						echo '<td align="right">'.@toMoney(($info["cr0"]-$info["dr0"])/$info["slwb0"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["cr0"]-$info["dr0"],2).'</td>';
						echo '<td align="center">'.@round($info["ds1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["dr1"]/$info["ds1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["dr1"],2).'</td>';
						echo '<td align="center">'.@round($info["cs1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["cr1"]/$info["cs1"],2).'</td>';
						echo '<td align="right">'.@toMoney($info["cr1"],2).'</td>';
						echo '<td align="center">'.@round($info["slwb0"]-$info["ds1"]+$info["cs1"],2).'</td>';
						echo '<td align="right">'.@toMoney(($info["cr0"]-$info["dr0"]+$info["cr1"]-$info["dr1"])/($info["slwb0"]-$info["ds1"]+$info["cs1"]),2).'</td>';
						echo '<td align="right">'.@toMoney($info["cr0"]-$info["dr0"]+$info["cr1"]-$info["dr1"],2).'</td>';
						echo '</tr>';
						$qcyr+=$info["cr0"]-$info["dr0"];
						$sumdr+=$info["dr1"];
						$sumcr+=$info["cr1"];
						$qmyr+=$info["cr0"]-$info["dr0"]+$info["cr1"]-$info["dr1"];
					}
				}while($info=$sql->fetch_array(MYSQLI_BOTH));
				echo '</tbody><tfoot><tr>
				<td></td>
				<td></td>
				<td align="center">小计：</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td align="right">'.toMoney($qcyr,2).'</td>
				<td></td>
				<td></td>
				<td align="right">'.toMoney($sumdr,2).'</td>
				<td></td>
				<td></td>
				<td align="right">'.toMoney($sumcr,2).'</td>
				<td></td>
				<td></td>
				<td align="right">'.toMoney($qmyr,2).'</td>
				</tr></tfoot>';
			}
			echo '</table></div>';
		}else{
			echo '<div align="left">未检索到相关数据；</div>';
		}
		$sql->free();
		$mysqli->close();
	}
}
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//表格插件应用
	$("#rightHand>div:visible #tbl1_xmyrb").mytable({interlacedColor:true,selectedColor:true,clickedColor:true,columnWidth:true});//不含数量外币列的表格；
	$("#rightHand>div:visible #tbl2_xmyrb").mytable({interlacedColor:true,selectedColor:true,clickedColor:true,columnWidth:true});//包含数量外币列的表格；

	//表格排序插件应用
	$("#rightHand>div:visible .mytable").tablesorter({
		widgets        : ['zebra', 'columns'],
		usNumberFormat : true,
		sortReset      : true,
		sortRestart    : true
	});
	//点击获取明细账；
	$("#rightHand>div:visible #tbl1_xmyrb a").click(function(){
		$(this).mytabs({strTitle:"项目明细账",strUrl:""});
		return false;
	});
	$("#rightHand>div:visible #tbl2_xmyrb a").click(function(){
		$(this).mytabs({strTitle:"项目明细账",strUrl:""});
		return false;
	});
	//导出数据；
	$("#rightHand>div:visible #output_xmyrb").click(function(){
 		$("#rightHand>div:visible #tbl1_xmyrb").table2excel({
			exclude: ".noExl",
			name: "Book",
			filename: "Book"+ new Date().toISOString().replace(/[\-\:\.]/g,"")+".xls",
			fileext: ".xls",
			exclude_img: true,
			exclude_links: true,
			exclude_inputs: true
		});
		$("#rightHand>div:visible #tbl2_xmyrb").table2excel({
			exclude: ".noExl",
			name: "Book",
			filename: "Book"+ new Date().toISOString().replace(/[\-\:\.]/g,"")+".xls",
			fileext: ".xls",
			exclude_img: true,
			exclude_links: true,
			exclude_inputs: true
		});
	});
	//打印；
	$("#rightHand>div:visible #print_xmyrb").click(function(){
		$("#rightHand>div:visible #div1_xmyrb").print(/*options*/);
	});
});
//-->
</script>