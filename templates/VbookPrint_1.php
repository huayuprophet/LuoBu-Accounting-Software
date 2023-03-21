<?php
require("../config.inc.php");
if(!$arrGroup["凭证打印"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if(($_SERVER['REQUEST_METHOD']=='GET')&&isset($_GET["qj"])&&isset($_GET["pzh"])&&isset($_GET["xh"])){//生成查询页面；
	
	echo '<h2>欢迎使用入库单据打印功能(遵循先贷方后借方反向规则)<br/><input id="print_VbookPrint_1" type="button" value="打印预览" /></h2>';
	
	//生成数据成功，先构造查询凭证表的SQL语句，然后将查询到的相关数据写入table；
	$qj=mysqli_real_escape_string($mysqli,$_GET["qj"]);
	$pzh=mysqli_real_escape_string($mysqli,$_GET["pzh"]);
	$xh=mysqli_real_escape_string($mysqli,$_GET["xh"]);
	
	$strsql='select ywrq,lrrq,qj,pzh,xh,zy,km,kmF,dr,cr,slwb,slwbF,gys,gysF,ch,chF,etr,chk,acc,fj from V1 where qj="'.$qj.'" and pzh="'.$pzh.'" order by xh desc';
	
	$sql=$mysqli->query($strsql);
    $info=$sql->fetch_array(MYSQLI_BOTH);
	
	$strgys='供应商：'.$info["gysF"];//供应商信息变量；
	$strywrq='收货日期：'.$info["ywrq"];//业务日期变量；
	$strdh='单号：'.$info["qj"].'-'.$info["pzh"];//单号信息变量，期间+凭证号；
	
	echo '<div id="printDJ_VbookPrint_1">';
	
	$i=1;//序号；
	$sumSlwb=0;$sumDr=0;//数量及金额合计；
	
	abcV1:
	
	$j=1;//行号；
	
	$info=$sql->fetch_array(MYSQLI_BOTH);
	
	//刚好存货记录没有了，也到了换行节点时的处理；
	if(is_null($info["km"])){
		goto abcV3;
	}
	
	echo '<table class="myVprint_VbookPrint_1">';
	
	echo '<tr><td colspan="6" align="center"></br><h7>'.$_SESSION["strCompanyName"].'</h7></td></tr>';
	
	echo '<tr><td colspan="6" align="center"><font size="6">入库单据</font></td></tr>';
	
	//表头相关信息；
	echo '<tr><td colspan="3" align="left">'.$strgys.'</td><td colspan="2" align="left">'.$strywrq.'</td><td align="center">'.$strdh.'</td></tr>';
	
	echo '<tr class="trV_Print1 tv1">';
	echo '<td align="center">序号</td>';
	echo '<td align="center">名称及型号</td>';
	echo '<td align="center">数量</td>';
	echo '<td align="center">净单价</td>';
	echo '<td align="center">净金额</td>';
	echo '<td align="center">备注</td>';
	echo '</tr>';
	
	do{
		if(is_null($info["km"])){
			//不足5格时补空格；
			abcV2:
			if($j<6){
				echo '<tr class="trV_Print1">';
				echo '<td align="center">'.$i.'</td><td></td><td></td><td></td><td></td><td></td>';
				echo '</tr>';
				$i++;
				$j++;
				goto abcV2;
			}
		}else{
			echo '<tr class="trV_Print1">';
			echo '<td align="center">'.$i.'</td>';
			echo '<td>'.$info["chF"].'</td>';
			echo '<td align="center">'.$info["slwb"].'</td>';
			echo '<td align="center">'.round($info["slwbF"],2).'</td>';
			echo '<td align="center">'.round($info["dr"],2).'</td>';
			echo '<td>'.$info["zy"].'</td>';
			echo '</tr>';
			$sumSlwb+=$info["slwb"];
			$sumDr+=$info["dr"];
		}
		
		$strVfoot='<tr><td colspan="2">制单：'.$info["etr"].'&nbsp;&nbsp;&nbsp;审核：'.$info["chk"].'</td><td colspan="2">送货：</td><td align="right">收货：</td><td></td></tr>';
		
		if($i%5==0){
			echo '<tr class="trV_Print1 tv1">';
			echo '<td align="center"></td>';
			echo '<td align="center">小计：</td>';
			echo '<td align="center">'.round($sumSlwb,2).'</td>';
			echo '<td align="center"></td>';
			echo '<td align="center">'.round($sumDr,2).'</td>';
			echo '<td align="center"></td>';
			echo '</tr>';
			
			echo $strVfoot;
			
			echo '<tr>';
			echo '<td colspan="6">注：白联存根，红联客户，黄联仓库。<br/></td>';
			echo '</tr>';
			echo '</table>';
			echo '<div style="page-break-after:always;"></div>';//此处加了自动分页符；
			$i++;
			$j++;
			goto abcV1;
		}
		
		$i++;
		$j++;
		
	}while($info=$sql->fetch_array(MYSQLI_BOTH));
	
	//不足5格时补空格；
	abcV4:
	if($j<6){
		echo '<tr class="trV_Print1">';
		echo '<td align="center">'.$i.'</td><td></td><td></td><td></td><td></td><td></td>';
		echo '</tr>';
		$i++;
		$j++;
		goto abcV4;
	}
			
	echo '<tr class="trV_Print1 tv1">';
	echo '<td align="center"></td>';
	echo '<td align="center">小计：</td>';
	echo '<td align="center">'.round($sumSlwb,2).'</td>';
	echo '<td align="center"></td>';
	echo '<td align="center">'.round($sumDr,2).'</td>';
	echo '<td align="center"></td>';
	echo '</tr>';
	
	echo $strVfoot;
	
	echo '<tr">';
	echo '<td colspan="6">注：白联存根，红联客户，黄联仓库。</td>';
	echo '</tr>';
	echo '</table>';
	
	abcV3:
	
}else{
	echo '<h2>欢迎使用入库单据打印功能页面<br/>未传入合法参数！</h2>';
}
echo '</div>';
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//打印；
	$("#print_VbookPrint_1").click(function(){
		$("#printDJ_VbookPrint_1").print(/*options*/);
	});
});
//-->
</script>