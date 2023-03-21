<?php
require("../config.inc.php");
if(!$arrGroup["科目账表"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if(($_SERVER['REQUEST_METHOD']=='GET')&&isset($_GET["km"])){
	//提交查询页面；
	$qj0=mysqli_real_escape_string($mysqli,$_GET["qj0"]);
	$qj1=mysqli_real_escape_string($mysqli,$_GET["qj1"]);
	$km=mysqli_real_escape_string($mysqli,$_GET["km"]);
	$jz=mysqli_real_escape_string($mysqli,$_GET["jz"]);
	
	//本期小计，本期累计变量定义；
	$sumDs=0;//本期小计
	$sumDr=0;
	$sumCs=0;
	$sumCr=0;
	$sumDsAll=0;//本期累计
	$sumDrAll=0;
	$sumCsAll=0;
	$sumCrAll=0;
	
	//取出与会计科目前面数值相似的最后一条id信息；
	$strsqlkm='select id from account where id like "'.$km.'%" order by id desc';
	$sqlkm=$mysqli->query($strsqlkm);
    $infokm=$sqlkm->fetch_array(MYSQLI_BOTH);
	//调用生成科目余额表的存储过程，生成kmyrb临时表；
	//kmyrbs存储过程参数：起始期间，终止期间，起始科目，终止科目，是否记账，是否包含项目，是否统计期初数；
	$mysqli->query("call kmyrbs('".$qj0."','".$qj1."','".$km."','".$infokm["id"]."','".$jz."','0','0')");
	//查询临时表数据；
	$strsql='select ywrq,lrrq,qj,pzh,zy,km,kma,dc,ds1,dr1,cs1,cr1 from kmyrb where km='.$km.' and t=1 order by qj,pzh,xh';
	$sql=$mysqli->query($strsql);
	$info=$sql->fetch_array(MYSQLI_BOTH);
    if($sql->num_rows>0){ //如果存在数据；
	    if($_GET["slwb"]=='a'){//不含有数量外币列；
			echo '<table>
			<tr><td>
			<a id="output_kmyrbsub" href="#">导出数据</a>|<a id="print_kmyrbsub" href="#">打印数据</a>
			</td></tr>
			</table>
			<div id="div1_kmyrbsub">
			<table id="tbl1_kmyrbsub">
			<caption><font size="5">科目余额明细表</font><br/>会计主体：<u>'.$_SESSION["strCompanyName"].'</u></caption>
			<thead><tr>
			<td align="center">业务日期</td>
			<td align="center">录入日期</td>
			<td align="center">期间</td>
			<td align="center">凭证号</td>
			<td align="center">摘要</td>
			<td align="center">科目</td>
			<td align="center">全称</td>
			<td align="center">方向</td>
			<td align="center">本期借方</td>
			<td align="center">本期贷方</td>
			<td align="center">期末余额</td>
			</tr></thead>';
			echo '<tr class="odd">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td>上期结转</td>
			<td>'.$info["km"].'</td>
			<td>'.$info["kma"].'</td>
			<td align="center">'.$info["dc"].'</td>
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
					  <td></td>
					  <td align="center">'.$qjStr.'</td>
					  <td></td>
					  <td>本期小计</td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td align="right">'.@toMoney($sumDr,2).'</td>
					  <td align="right">'.@toMoney($sumCr,2).'</td>
					  <td></td>
					  </tr>';
					echo '<tr class="odd">
					  <td></td>
					  <td></td>
					  <td align="center">'.$qjStr.'</td>
					  <td></td>
					  <td>本期累计</td>
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
				if($info["dc"]=="借方"){
					$yr+=$info["dr1"]-$info["cr1"];
					echo '<tr>';
					echo '<td align="left">'.$info["ywrq"].'</td>';
					echo '<td align="left">'.$info["lrrq"].'</td>';
					echo '<td align="center">'.$info["qj"].'</td>';
					echo '<td align="center"><a href="qj='.$info["qj"].'&pzh='.$info["pzh"].'">'.$info["pzh"].'</a></td>';
					echo '<td align="left">'.$info["zy"].'</td>';
					echo '<td align="left">'.$info["km"].'</td>';
					echo '<td align="left">'.$info["kma"].'</td>';
					echo '<td align="center">'.$info["dc"].'</td>';
					echo '<td align="right">'.@toMoney($info["dr1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr1"],2).'</td>';
					echo '<td align="right">'.@toMoney($yr,2).'</td>';
					echo '</tr>';
				}else{
					$yr+=$info["cr1"]-$info["dr1"];
					echo '<tr>';
					echo '<td align="left">'.$info["ywrq"].'</td>';
					echo '<td align="left">'.$info["lrrq"].'</td>';
					echo '<td align="center">'.$info["qj"].'</td>';
					echo '<td align="center"><a href="qj='.$info["qj"].'&pzh='.$info["pzh"].'">'.$info["pzh"].'</a></td>';
					echo '<td align="left">'.$info["zy"].'</td>';
					echo '<td align="left">'.$info["km"].'</td>';
					echo '<td align="left">'.$info["kma"].'</td>';
					echo '<td align="center">'.$info["dc"].'</td>';
					echo '<td align="right">'.@toMoney($info["dr1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr1"],2).'</td>';
					echo '<td align="right">'.@toMoney($yr,2).'</td>';
					echo '</tr>';
				}
				$qjStr=$info["qj"];
				$sumDr+=$info["dr1"];//本期小计
				$sumCr+=$info["cr1"];
				$sumDrAll+=$info["dr1"];//本期累计
				$sumCrAll+=$info["cr1"];
			}while($info=$sql->fetch_array(MYSQLI_BOTH));
			//写入最后一期合计数据；
			echo '<tr class="odd">
				  <td></td>
				  <td></td>
				  <td align="center">'.$qjStr.'</td>
				  <td></td>
				  <td>本期小计</td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td align="right">'.@toMoney($sumDr,2).'</td>
				  <td align="right">'.@toMoney($sumCr,2).'</td>
				  <td></td>
				  </tr>';
			echo '<tr class="odd">
				  <td></td>
				  <td></td>
				  <td align="center">'.$qjStr.'</td>
				  <td></td>
				  <td>本期累计</td>
				  <td></td>
				  <td></td>
				  <td></td>
				  <td align="right">'.@toMoney($sumDrAll,2).'</td>
				  <td align="right">'.@toMoney($sumCrAll,2).'</td>
				  <td></td>
				  </tr>';
			echo '</table></div>';
		}else{//包含数量外币列；
			echo '<table>
			<tr><td>
			<a id="output_kmyrbsub" href="#">导出数据</a>|<a id="print_kmyrbsub" href="#">打印数据</a>
			</td></tr>
			</table>
			<div id="div1_kmyrbsub">
			<table id="tbl2_kmyrbsub">
			<caption><font size="5">科目余额明细表</font><br/>会计主体：<u>'.$_SESSION["strCompanyName"].'</u></caption>
			<thead><tr>
			<td align="center">业务日期</td>
			<td align="center">录入日期</td>
			<td align="center">期间</td>
			<td align="center">凭证号</td>
			<td align="center">摘要</td>
			<td align="center">科目</td>
			<td align="center">全称</td>
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
			<td></td>
			<td>上期结转</td>
			<td>'.$info["km"].'</td>
			<td>'.$info["kma"].'</td>
			<td align="center">'.$info["dc"].'</td>
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
					  <td></td>
					  <td align="center">'.$qjStr.'</td>
					  <td></td>
					  <td>本期小计</td>
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
					  <td></td>
					  <td align="center">'.$qjStr.'</td>
					  <td></td>
					  <td>本期累计</td>
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
				if($info["dc"]=="借方"){
					$slwb+=$info["ds1"]-$info["cs1"];
					$yr+=$info["dr1"]-$info["cr1"];
					echo '<tr>';
					echo '<td align="left">'.$info["ywrq"].'</td>';
					echo '<td align="left">'.$info["lrrq"].'</td>';
					echo '<td align="center">'.$info["qj"].'</td>';
					echo '<td align="center"><a href="qj='.$info["qj"].'&pzh='.$info["pzh"].'">'.$info["pzh"].'</a></td>';
					echo '<td align="left">'.$info["zy"].'</td>';
					echo '<td align="left">'.$info["km"].'</td>';
					echo '<td align="left">'.$info["kma"].'</td>';
					echo '<td align="center">'.$info["dc"].'</td>';
					echo '<td align="center">'.@round($info["ds1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["dr1"]/$info["ds1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["dr1"],2).'</td>';
					echo '<td align="center">'.@round($info["cs1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr1"]/$info["cs1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr1"],2).'</td>';
					echo '<td align="center">'.@round($slwb,2).'</td>';
					echo '<td align="right">'.@toMoney($yr/$slwb,2).'</td>';
					echo '<td align="right">'.@toMoney($yr,2).'</td>';
					echo '</tr>';
				}else{
					$slwb+=$info["cs1"]-$info["ds1"];
					$yr+=$info["cr1"]-$info["dr1"];
					echo '<tr>';
					echo '<td align="left">'.$info["ywrq"].'</td>';
					echo '<td align="left">'.$info["lrrq"].'</td>';
					echo '<td align="center">'.$info["qj"].'</td>';
					echo '<td align="center"><a href="qj='.$info["qj"].'&pzh='.$info["pzh"].'">'.$info["pzh"].'</a></td>';
					echo '<td align="left">'.$info["zy"].'</td>';
					echo '<td align="left">'.$info["km"].'</td>';
					echo '<td align="left">'.$info["kma"].'</td>';
					echo '<td align="center">'.$info["dc"].'</td>';
					echo '<td align="center">'.@round($info["ds1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["dr1"]/$info["ds1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["dr1"],2).'</td>';
					echo '<td align="center">'.@round($info["cs1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr1"]/$info["cs1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr1"],2).'</td>';
					echo '<td align="center">'.@round($slwb,2).'</td>';
					echo '<td align="right">'.@toMoney($yr/$slwb,2).'</td>';
					echo '<td align="right">'.@toMoney($yr,2).'</td>';
					echo '</tr>';
				}
				$qjStr=$info["qj"];
				$sumDs+=$info["ds1"];//本期小计
				$sumDr+=$info["dr1"];
				$sumCs+=$info["cs1"];
				$sumCr+=$info["cr1"];
				$sumDsAll+=$info["ds1"];//本期累计
				$sumDrAll+=$info["dr1"];
				$sumCsAll+=$info["cs1"];
				$sumCrAll+=$info["cr1"];
			}while($info=$sql->fetch_array(MYSQLI_BOTH));
			//写入最后一期合计数据；
			echo '<tr class="odd">
				  <td></td>
				  <td></td>
				  <td align="center">'.$qjStr.'</td>
				  <td></td>
				  <td>本期小计</td>
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
				  <td></td>
				  <td align="center">'.$qjStr.'</td>
				  <td></td>
				  <td>本期累计</td>
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
		$sqlkm->free();
		$sql->free();
		$mysqli->close();
	}else{
		echo '<div>未检索到当前期间的相关数据；</div>';
	}
}else{
	echo '科目明细账不允许不带参数进入!';
}
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//表格插件应用
	$("#rightHand>div:visible #tbl1_kmyrbsub,#rightHand>div:visible #tbl2_kmyrbsub").mytable({a:false,b:false,c:true,d:true});
	//获取凭证；
	$("#rightHand>div:visible #tbl1_kmyrbsub a,#rightHand>div:visible #tbl2_kmyrbsub a").click(function(){
		//打开相应的会计凭证功能页面；
		var url="templates/V_1.php?handle=editV&"+$(this).attr("href");
		$(this).mytabs({strTitle:"会计凭证",strUrl:url});
		return false;
	});
	//导出数据；
	$("#rightHand>div:visible #output_kmyrbsub").click(function(){
 		$("#rightHand>div:visible #tbl1_kmyrbsub,#rightHand>div:visible #tbl2_kmyrbsub").table2excel({
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
	$("#rightHand>div:visible #print_kmyrbsub").click(function(){
		$("#rightHand>div:visible #div1_kmyrbsub").print(/*options*/);
	});
});
//-->
</script>