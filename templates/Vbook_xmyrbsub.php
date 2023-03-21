<?php
require("../config.inc.php");
if(!$arrGroup["项目账表"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if(($_SERVER['REQUEST_METHOD']=='GET')&&isset($_GET["km"])){
	//项目名称转换；
	switch($_GET["xm"]){
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
	
	$qj0=mysqli_real_escape_string($mysqli,$_GET["qj0"]);
	$qj1=mysqli_real_escape_string($mysqli,$_GET["qj1"]);
	$km=mysqli_real_escape_string($mysqli,$_GET["km"]);
	$xms=mysqli_real_escape_string($mysqli,$_GET["xms"]);
	
	//本期小计，本期累计变量定义；
	$sumDs=0;//本期小计
	$sumDr=0;
	$sumCs=0;
	$sumCr=0;
	$sumDsAll=0;//本期累计
	$sumDrAll=0;
	$sumCsAll=0;
	$sumCrAll=0;
	
	//查询会计科目名称及借贷方向；
	$strsql_1='select Fname,dc from account where id='.$km.'';
	//echo $strsql_1.'<br>';
	$sql_1=$mysqli->query($strsql_1);
	$info_1=$sql_1->fetch_array(MYSQLI_BOTH);
	if($info_1){
		$strName=$info_1["Fname"];//会计科目名称
		$strDC=$info_1["dc"];//会计科目借贷方
	}
	
	//构造查询语句，只需要从数据库中找出本期数据即可；
	$strsql='select ywrq,qj,pzh,zy,km,'.$col.' as xm,'.$col.'F as xms,slwb,dr,cr from v1';
	$strsql=$strsql.' where (qj between '.$qj0.' and '.$qj1.') and km='.$km.' and '.$col.'='.$xms.' order by qj,pzh,xh';
	//echo $strsql.'<br>';
	$sql=$mysqli->query($strsql);
    $info=$sql->fetch_array(MYSQLI_BOTH);
    if($info){ //如果存在数据；
	    if($_GET["slwb"]=='a'){//不含有数量外币列；
			echo '<table>
			<tr><td>
			<a id="output_xmyrbsub" href="#">导出数据</a>|<a id="print_xmyrbsub" href="#">打印数据</a>
			</td></tr>
			</table>
			<div id="div1_xmyrbsub">
			<table id="tbl1_xmyrbsub" class="mytable" border="1" cellspacing="0">
			<caption><font size="5">项目余额明细表</font><br/>会计主体：<u>'.$_SESSION["strCompanyName"].'</u></caption>
			<thead><tr>
			<td align="center">业务日期</td>
			<td align="center">期间</td>
			<td align="center">凭证号</td>
			<td align="center">摘要</td>
			<td align="center">科目</td>
			<td align="center">全称</td>
			<td align="center">项目</td>
			<td align="center">项目名称</td>
			<td align="center">方向</td>
			<td align="center">借方金额</td>
			<td align="center">贷方金额</td>
			<td align="center">期末余额</td>
			</tr></thead>';
			echo '<tr class="odd">
			<td></td>
			<td></td>
			<td></td>
			<td>上期结转</td>
			<td>'.$info["km"].'</td>
			<td>'.$strName.'</td>
			<td>'.$info["xm"].'</td>
			<td>'.$info["xms"].'</td>
			<td align="center">'.$strDC.'</td>
			<td></td>
			<td></td>
			<td align="right">'.@toMoney($_GET["yr"],2).'</td>
			</tr>';
			$yr=$_GET["yr"];
			$qjStr=$info["qj"];
			do{
				//如果上一期间不等于本次期间数据，写入“本期小计”和“本期累计”行；
				if($qjStr!=$info["qj"]){
					echo '<tr class="odd">
					  <td></td>
					  <td align="center">'.$qjStr.'</td>
					  <td></td>
					  <td>本期小计</td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td align="right">'.@toMoney($sumDr,2).'</td>
					  <td align="right">'.@toMoney($sumCr,2).'</td>
					  <td></td>
					  </tr>';
					echo '<tr class="odd">
					  <td></td>
					  <td align="center">'.$qjStr.'</td>
					  <td></td>
					  <td>本期累计</td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td align="right">'.@toMoney($sumDrAll,2).'</td>
					  <td align="right">'.@toMoney($sumCrAll,2).'</td>
					  <td></td>
					  </tr>';
					  //每一期结束后本期小计清零；
					  $sumDr=0;
					  $sumCr=0;
				}
				//写入当期数据；
				if($strDC=="借方"){
					$yr+=$info["dr"]-$info["cr"];
					echo '<tr>';
					echo '<td align="left">'.$info["ywrq"].'</td>';
					echo '<td align="center">'.$info["qj"].'</td>';
					echo '<td align="center"><a href="qj='.$info["qj"].'&pzh='.$info["pzh"].'">'.$info["pzh"].'</a></td>';
					echo '<td align="left">'.$info["zy"].'</td>';
					echo '<td align="left">'.$info["km"].'</td>';
					echo '<td align="left">'.$strName.'</td>';
					echo '<td align="left">'.$info["xm"].'</td>';
					echo '<td align="left">'.$info["xms"].'</td>';
					echo '<td align="center">'.$strDC.'</td>';
					echo '<td align="right">'.@toMoney($info["dr"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr"],2).'</td>';
					echo '<td align="right">'.@toMoney($yr,2).'</td>';
					echo '</tr>';
				}else{
					$yr+=$info["cr"]-$info["dr"];
					echo '<tr>';
					echo '<td align="left">'.$info["ywrq"].'</td>';
					echo '<td align="center">'.$info["qj"].'</td>';
					echo '<td align="center"><a href="qj='.$info["qj"].'&pzh='.$info["pzh"].'">'.$info["pzh"].'</a></td>';
					echo '<td align="left">'.$info["zy"].'</td>';
					echo '<td align="left">'.$info["km"].'</td>';
					echo '<td align="left">'.$strName.'</td>';
					echo '<td align="left">'.$info["xm"].'</td>';
					echo '<td align="left">'.$info["xms"].'</td>';
					echo '<td align="center">'.$strDC.'</td>';
					echo '<td align="right">'.@toMoney($info["dr"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr"],2).'</td>';
					echo '<td align="right">'.@toMoney($yr,2).'</td>';
					echo '</tr>';
				}
				$qjStr=$info["qj"];
				$sumDr+=$info["dr"];//本期小计
				$sumCr+=$info["cr"];
				$sumDrAll+=$info["dr"];//本期累计
				$sumCrAll+=$info["cr"];
			}while($info=$sql->fetch_array(MYSQLI_BOTH));
			//写入最后一期合计数据；
			echo '<tr class="odd">
				  <td></td>
				  <td align="center">'.$qjStr.'</td>
				  <td></td>
				  <td>本期小计</td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td align="right">'.@toMoney($sumDr,2).'</td>
				  <td align="right">'.@toMoney($sumCr,2).'</td>
				  <td></td>
				  </tr>';
			echo '<tr class="odd">
				  <td></td>
				  <td align="center">'.$qjStr.'</td>
				  <td></td>
				  <td>本期累计</td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td align="right">'.@toMoney($sumDrAll,2).'</td>
				  <td align="right">'.@toMoney($sumCrAll,2).'</td>
				  <td></td>
				  </tr>';
			echo '</table></div>';
		}else{//含数量外币列；
			echo '<table>
			<tr><td>
			<a id="output_xmyrbsub" href="#">导出数据</a>|<a id="print_xmyrbsub" href="#">打印数据</a>
			</td></tr>
			</table>
			<div id="div1_xmyrbsub">
			<table id="tbl2_xmyrbsub" class="mytable" border="1" cellspacing="0">
			<caption><font size="5">项目余额明细表</font><br/>会计主体：<u>'.$_SESSION["strCompanyName"].'</u></caption>
			<thead><tr>
			<td align="center">业务日期</td>
			<td align="center">期间</td>
			<td align="center">凭证号</td>
			<td align="center">摘要</td>
			<td align="center">科目</td>
			<td align="center">全称</td>
			<td align="center">项目</td>
			<td align="center">项目名称</td>
			<td align="center">方向</td>
			<td align="center">借方数量</td>
			<td align="center">借方单价</td>
			<td align="center">借方金额</td>
			<td align="center">贷方数量</td>
			<td align="center">贷方单价</td>
			<td align="center">贷方金额</td>
			<td align="center">期末数量</td>
			<td align="center">期末单价</td>
			<td align="center">期末余额</td>
			</tr></thead>';
			echo '<tr class="odd">
			<td></td>
			<td></td>
			<td></td>
			<td>上期结转</td>
			<td>'.$info["km"].'</td>
			<td>'.$strName.'</td>
			<td>'.$info["xm"].'</td>
			<td>'.$info["xms"].'</td>
			<td align="center">'.$strDC.'</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td align="center">'.@round($_GET["slwb"],2).'</td>
			<td align="right">'.@toMoney($_GET["yr"]/$_GET["slwb"],2).'</td>
			<td align="right">'.@toMoney($_GET["yr"],2).'</td>
			</tr>';
			$slwb=$_GET["slwb"];
			$yr=$_GET["yr"];
			$qjStr=$info["qj"];
			do{
				//如果上一期间不等于本次期间数据，写入“本期小计”和“本期累计”行；
				if($qjStr!=$info["qj"]){
					echo '<tr class="odd">
					  <td></td>
					  <td align="center">'.$qjStr.'</td>
					  <td></td>
					  <td>本期小计</td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td align="center">'.@round($sumDs,2).'</td>
					  <td align="right">'.@toMoney($sumDr/$sumDs,2).'</td>
					  <td align="right">'.@toMoney($sumDr,2).'</td>
					  <td align="center">'.@round($sumCs,2).'</td>
					  <td align="right">'.@toMoney($sumCr/$sumCs,2).'</td>
					  <td align="right">'.@toMoney($sumCr,2).'</td>
					  <td></td>
					  <td></td>
					  <td></td>
					  </tr>';
					echo '<tr class="odd">
					  <td></td>
					  <td align="center">'.$qjStr.'</td>
					  <td></td>
					  <td>本期累计</td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td align="center">'.@round($sumDsAll,2).'</td>
					  <td align="right">'.@toMoney($sumDrAll/$sumDsAll,2).'</td>
					  <td align="right">'.@toMoney($sumDrAll,2).'</td>
					  <td align="center">'.@round($sumCsAll,2).'</td>
					  <td align="right">'.@toMoney($sumCrAll/$sumCsAll,2).'</td>
					  <td align="right">'.@toMoney($sumCrAll,2).'</td>
					  <td></td>
					  <td></td>
					  <td></td>
					  </tr>';
					  //每一期结束后本期小计清零；
					  $sumDs=0;
					  $sumDr=0;
					  $sumCs=0;
					  $sumCr=0;
				}
				//写入当期数据；
				if($strDC=="借方"){
					if($info["dr"]==0){
						$ds=0;
						$cs=$info["slwb"];
					}else{
						$ds=$info["slwb"];
						$cs=0;
					}
					$slwb+=$ds-$cs;
					$yr+=$info["dr"]-$info["cr"];
					echo '<tr>';
					echo '<td align="left">'.$info["ywrq"].'</td>';
					echo '<td align="center">'.$info["qj"].'</td>';
					echo '<td align="center"><a href="qj='.$info["qj"].'&pzh='.$info["pzh"].'">'.$info["pzh"].'</a></td>';
					echo '<td align="left">'.$info["zy"].'</td>';
					echo '<td align="left">'.$info["km"].'</td>';
					echo '<td align="left">'.$strName.'</td>';
					echo '<td align="left">'.$info["xm"].'</td>';
					echo '<td align="left">'.$info["xms"].'</td>';
					echo '<td align="center">'.$strDC.'</td>';
					echo '<td align="center">'.@round($ds,2).'</td>';
					echo '<td align="right">'.@toMoney($info["dr"]/$ds,2).'</td>';
					echo '<td align="right">'.@toMoney($info["dr"],2).'</td>';
					echo '<td align="center">'.@round($cs,2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr"]/$cs,2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr"],2).'</td>';
					echo '<td align="center">'.@round($slwb,2).'</td>';
					echo '<td align="right">'.@toMoney($yr/$slwb,2).'</td>';
					echo '<td align="right">'.@toMoney($yr,2).'</td>';
					echo '</tr>';
				}else{
					if($info["dr"]==0){
						$ds=0;
						$cs=$info["slwb"];
					}else{
						$ds=$info["slwb"];
						$cs=0;
					}
					$slwb+=-$ds+$cs;
					$yr+=-$info["dr"]+$info["cr"];
					echo '<tr>';
					echo '<td align="left">'.$info["ywrq"].'</td>';
					echo '<td align="center">'.$info["qj"].'</td>';
					echo '<td align="center"><a href="qj='.$info["qj"].'&pzh='.$info["pzh"].'">'.$info["pzh"].'</a></td>';
					echo '<td align="left">'.$info["zy"].'</td>';
					echo '<td align="left">'.$info["km"].'</td>';
					echo '<td align="left">'.$strName.'</td>';
					echo '<td align="left">'.$info["xm"].'</td>';
					echo '<td align="left">'.$info["xms"].'</td>';
					echo '<td align="center">'.$strDC.'</td>';
					echo '<td align="center">'.@round($ds,2).'</td>';
					echo '<td align="right">'.@toMoney($info["dr"]/$ds,2).'</td>';
					echo '<td align="right">'.@toMoney($info["dr"],2).'</td>';
					echo '<td align="center">'.@round($cs,2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr"]/$cs,2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr"],2).'</td>';
					echo '<td align="center">'.@round($slwb,2).'</td>';
					echo '<td align="right">'.@toMoney($yr/$slwb,2).'</td>';
					echo '<td align="right">'.@toMoney($yr,2).'</td>';
					echo '</tr>';
				}
				$qjStr=$info["qj"];
				$sumDs+=$ds;//本期小计
				$sumDr+=$info["dr"];
				$sumCs+=$cs;
				$sumCr+=$info["cr"];
				$sumDsAll+=$ds;//本期累计
				$sumDrAll+=$info["dr"];
				$sumCsAll+=$cs;
				$sumCrAll+=$info["cr"];
			}while($info=$sql->fetch_array(MYSQLI_BOTH));
			//写入最后一期合计数据；
			echo '<tr class="odd">
				  <td></td>
				  <td align="center">'.$qjStr.'</td>
				  <td></td>
				  <td>本期小计</td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td align="center">'.@round($sumDs,2).'</td>
				  <td align="right">'.@toMoney($sumDr/$sumDs,2).'</td>
				  <td align="right">'.@toMoney($sumDr,2).'</td>
				  <td align="center">'.@round($sumCs,2).'</td>
				  <td align="right">'.@toMoney($sumCr/$sumCs,2).'</td>
				  <td align="right">'.@toMoney($sumCr,2).'</td>
				  <td></td>
				  <td></td>
				  <td></td>
				  </tr>';
			echo '<tr class="odd">
				  <td></td>
				  <td align="center">'.$qjStr.'</td>
				  <td></td>
				  <td>本期累计</td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td align="center">'.@round($sumDsAll,2).'</td>
				  <td align="right">'.@toMoney($sumDrAll/$sumDsAll,2).'</td>
				  <td align="right">'.@toMoney($sumDrAll,2).'</td>
				  <td align="center">'.@round($sumCsAll,2).'</td>
				  <td align="right">'.@toMoney($sumCrAll/$sumCsAll,2).'</td>
				  <td align="right">'.@toMoney($sumCrAll,2).'</td>
				  <td></td>
				  <td></td>
				  <td></td>
				  </tr>';
			echo '</table>';
		}
		$sql_1->free();
		$sql->free();
		$mysqli->close();
	}else{
		echo '<div>未检索到当前期间的相关数据；</div>';
	}
}else{
	echo '项目明细账不允许不带参数进入!';
}
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//表格插件应用
	$("#rightHand>div:visible #tbl1_xmyrbsub,#rightHand>div:visible #tbl2_xmyrbsub").mytable({a:false,b:false,c:true});
	//获取凭证；
	$("#rightHand>div:visible #tbl1_xmyrbsub a,#rightHand>div:visible #tbl2_xmyrbsub a").click(function(){
		//打开相应的会计凭证功能页面；
		var url="templates/V_1.php?handle=editV&"+$(this).attr("href");
		$(this).mytabs({strTitle:"会计凭证",strUrl:url});
		return false;
	});
	//导出数据；
	$("#rightHand>div:visible #output_xmyrbsub").click(function(){
 		$("#rightHand>div:visible #tbl1_xmyrbsub,#rightHand>div:visible #tbl2_xmyrbsub").table2excel({
			exclude: ".noExl",
			name: "Book",
			filename: "Book"+ new Date().toISOString().replace(/[\-\:\.]/g, "")+".xls",
			fileext: ".xls",
			exclude_img: true,
			exclude_links: true,
			exclude_inputs: true
		});
	});
	//打印；
	$("#rightHand>div:visible #print_xmyrbsub").click(function(){
		$("#rightHand>div:visible #div1_xmyrbsub").print(/*options*/);
	});
});
//-->
</script>