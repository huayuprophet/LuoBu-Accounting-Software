<?php
require("../config.inc.php");
if(!$arrGroup["凭证打印"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//凭证打印预览按钮；
echo '<div class="access1">
	<input id="print_VbookPrint" type="button" value="打印预览" />&nbsp;
	</div>';
	
$objQj=new QJ();
$objQj->getQj('select distinct qj from v1 order by qj desc');

if(($_SERVER['REQUEST_METHOD']=='GET')&&isset($_GET["qj0"])&&isset($_GET["qj1"])){//生成查询页面；
	echo '<div align="left" class="access2">
		<form id="form_VbookPrint">
		<table>
		<tr>
		<td>期间：</td>
		<td>
		<select name="qj0" size="1">';
		foreach($objQj->arrayQj as $intqj){
			if($intqj==$_GET["qj0"]){
				echo '<option value="'.$intqj.'" selected="selected">'.$intqj.'</option>';
			}else{
				echo '<option value="'.$intqj.'">'.$intqj.'</option>';  
			}
		}
	echo '</select>
		</td>
		<td align="center">—</td>
		<td>
		<select name="qj1" size="1">';
		foreach($objQj->arrayQj as $intqj){
			if($intqj==$_GET["qj1"]){
				echo '<option value="'.$intqj.'" selected="selected">'.$intqj.'</option>';
			}else{
				echo '<option value="'.$intqj.'">'.$intqj.'</option>';  
			}
		}
	echo '</select>
		</td>
		</tr>
		<tr>
		<td>凭证号：</td>
		<td><input name="pzh0" type="text" style="width:88px" value="'.$_GET["pzh0"].'" /></td>
		<td align="center">—</td>
		<td><input name="pzh1" type="text" style="width:88px" value="'.$_GET["pzh1"].'" /></td>
		</tr>
		<tr>
		<td>摘要：</td>
		<td colspan="3"><input name="zy" type="text" style="width:255px" value="'.$_GET["zy"].'" /></td>
		</tr>
		<tr>
		<td>科目全称：</td>
		<td colspan="3"><input name="kmF" type="text" style="width:255px" value="'.$_GET["kmF"].'" /></td>
		</tr>
		<tr>
		<td>借贷金额：</td>
		<td colspan="3"><input name="DC" type="text" style="width:255px" value="'.$_GET["DC"].'" /></td>
		</tr>
		<tr>
		<td>录入：</td>
		<td>';
		//是否开放查阅所有凭证功能；
		if($arrGroup["开放查阅"]){
			echo '<input name="etr" type="text" style="width:96px" value="'.$_GET["etr"].'"/>';
		}else{
			echo '<input name="etr" type="text" style="width:96px" value="'.$arrPublicVar["username"].'" readonly />';
		}
	echo '</td>
		<td align="center">审核：</td>
		<td><abbr title="１、是：已审核；２、否：未审核；３、审核人名称"><input name="chk" type="text" style="width:96px" value="'.$_GET["chk"].'"/></abbr></td>
		</tr>
		<tr>
		<td colspan="4" align="center"><input id="tj_VbookPrint" name="tj" type="button" value="生成" /></td>
		</tr>
		</table>
		</form>';
	
	//生成数据成功，先构造查询凭证表的SQL语句，然后将查询到的相关数据写入table；
	$qj0=mysqli_real_escape_string($mysqli,$_GET["qj0"]);
	$qj1=mysqli_real_escape_string($mysqli,$_GET["qj1"]);
	
	$strsql='select distinct concat(qj,pzh) from V1 where (qj between "'.$qj0.'" and "'.$qj1.'")';
	
	if(($_GET["pzh0"]!="")&&($_GET["pzh1"]=="")){
		$strsql.=' and pzh>="'.$_GET["pzh0"].'"';
	}else if(($_GET["pzh0"]=="")&&($_GET["pzh1"]!="")){
	    $strsql.=' and pzh<="'.$_GET["pzh1"].'"';
	}else if(($_GET["pzh0"]!="")&&($_GET["pzh1"]!="")){
		$strsql.=' and (pzh between "'.$_GET["pzh0"].'" and "'.$_GET["pzh1"].'")';
	}
	
	if($_GET["zy"]!=""){
		$strsql.=' and zy like "%'.$_GET["zy"].'%"';
	}
	
	if($_GET["kmF"]!=""){
		$strsql.=' and kmF like "%'.$_GET["kmF"].'%"';
	}
	
	if($_GET["DC"]!=""){
		$strsql.=' and (dr="'.$_GET["DC"].'" or cr="'.$_GET["DC"].'")';
	}
	
	if($_GET["etr"]!=""){
		$strsql.=' and etr="'.$_GET["etr"].'"';
	}
	
	if($_GET["chk"]=="是"){
		$strsql.=' and chk is not null';
	}else if($_GET["chk"]=="否"){
		$strsql.=' and chk is null';
	}else if($_GET["chk"]!=""){
		$strsql.=' and chk="'.$_GET["chk"].'"';
	}
	
	//echo $strsql;//测试SQL语句；
	
	$strsql='select ywrq,lrrq,qj,pzh,xh,zy,km,kmF,dr,cr,etr,chk,acc,fj from V1 where concat(qj,pzh) in('.$strsql.') order by qj,pzh,xh';
	
	$sql=$mysqli->query($strsql);
    $info=$sql->fetch_array(MYSQLI_BOTH);

	if($info){

		$recordNumber=0;    //统计凭证的记录条数；
		$pageNumber=1;      //统计凭证的页数；
		$trNumber=1;        //统计凭证行数；
		$nextV=$info["pzh"];//初始凭证号；
		$sumDr=0;$sumCr=0;  //凭证借贷和；
		$strVfoot;			//凭证底部信息；
	
		$sqlRecordNumber=$mysqli->query('select count(*) as QPNUM from V1 where qj='.$info["qj"].' and pzh='.$info["pzh"].'');
		$infoRecordNumber=$sqlRecordNumber->fetch_array(MYSQLI_BOTH);
		$recordNumber=ceil($infoRecordNumber["QPNUM"]/5);
		$sqlRecordNumber->free();
					
		echo '<hr align="left">';
	
		echo '<div id="printV_VbookPrint" align="center">';
	
		echo '<table class="myVprint">';
	
		echo '<tr><td colspan="5" align="center"><h1>会计凭证</h1></td></tr>';
	
		echo '<tr><td colspan="4" align="left">编制单位：'.$_SESSION["strCompanyName"].'</td>
			<td align="center">凭证号：'.$info["pzh"].'</td></tr>';
	
		echo '<tr><td colspan="2" align="left"><font size="3">录入日期：'.$info["lrrq"].'</font></td>';
		echo '<td align="center"><font size="3">业务日期：'.$info["ywrq"].'</font></td>';
		echo '<td colspan="2" align="right"><font size="3">附件：'.$info["fj"].' ; 页码：'.$pageNumber.'/'.$recordNumber.'</font></td></tr>';
	
		echo '<tr class="trV_Print tv">';
		echo '<td align="center">序号</td>';
		echo '<td align="center">摘要</td>';
		echo '<td align="center">科目全称</td>';
		echo '<td align="center">借方</td>';
		echo '<td align="center">贷方</td>';
		echo '</tr>';
	
		do{
	
			if($nextV!=$info["pzh"]){//凭证号前后不一致时执行下面代码；
		
				abcV1:
			
				$pageNumber=0;
			
				//刚好5行，不用补行；
				if($trNumber==6){
					echo '<tr class="trV_Print tv">';
					echo '<td></td><td></td>';
					echo '<td align="center">小计：</td>';
					echo '<td align="right">'.number_format($sumDr,2).'</td>';
					echo '<td align="right">'.number_format($sumCr,2).'</td>';
					echo '</tr>';
					$sumDr=0;
					$sumCr=0;
				}
			
				//少于5行的，需要补行；
				if($trNumber<=5){
					echo '<tr class="trV_Print"><td align="center"></td><td></td><td></td><td></td><td></td></tr>';
					$trNumber++;
					if($trNumber==6){
						echo '<tr class="trV_Print tv">';
						echo '<td></td><td></td>';
						echo '<td align="center">小计：</td>';
						echo '<td align="right">'.number_format($sumDr,2).'</td>';
						echo '<td align="right">'.number_format($sumCr,2).'</td>';
						echo '</tr>';
						$sumDr=0;
						$sumCr=0;
						goto abcV3;
					}
					goto abcV1;
				}
			
				abcV3:
			
				if($info){//如果往下还存在记录，继续执行下面的代码；
			
					echo $strVfoot;
				
					echo '</table>';
					echo '<div style="page-break-after:always;"></div>';//此处加了自动分页符；
					echo '<table class="myVprint">';
				
					//凭证号是否发生变化；
					if($nextV!=$info["pzh"]){
						$pageNumber=0;
						$sqlRecordNumber=$mysqli->query('select count(*) as QPNUM from V1 where qj='.$info["qj"].' and pzh='.$info["pzh"].'');
						$infoRecordNumber=$sqlRecordNumber->fetch_array(MYSQLI_BOTH);
						$recordNumber=ceil($infoRecordNumber["QPNUM"]/5);
						$sqlRecordNumber->free();
					}
				
					$pageNumber++;
					$trNumber=1;
				
					echo '<tr><td colspan="5" align="center"><h1>会计凭证</h1></td></tr>';
				
					echo '<tr><td colspan="4" align="left">编制单位：'.$_SESSION["strCompanyName"].'</td>
						<td align="center">凭证号：'.$info["pzh"].'</td></tr>';
				
					echo '<tr><td colspan="2" align="left"><font size="3">录入日期：'.$info["lrrq"].'</font></td>';
					echo '<td align="center"><font size="3">业务日期：'.$info["ywrq"].'</font></td>';
					echo '<td colspan="2" align="right"><font size="3">附件：'.$info["fj"].' ; 页码：'.$pageNumber.'/'.$recordNumber.'</font></td></tr>';
				
					echo '<tr class="trV_Print tv">';
					echo '<td align="center">序号</td>';
					echo '<td align="center">摘要</td>';
					echo '<td align="center">科目全称</td>';
					echo '<td align="center">借方</td>';
					echo '<td align="center">贷方</td>';
					echo '</tr>';
				
				}else{//如果往下不存在记录，结束循环，写上结尾；
					echo $strVfoot;
					echo '</table>';
					echo '</div>';
					goto abcV4;
				}
			}
		
			//凭证号前后一致时执行下面的代码；
		
			echo '<tr class="trV_Print">';
			echo '<td align="center">'.$info["xh"].'</td>';
			echo '<td>'.cut($info["zy"],27).'</td>';
			echo '<td>'.cut($info["kmF"],100).'</td>';
			echo '<td align="right">'.($info["dr"]==0?"":number_format($info["dr"],2)).'</td>';
			echo '<td align="right">'.($info["cr"]==0?"":number_format($info["cr"],2)).'</td>';
			echo '</tr>';
		
			$trNumber++;
		
			$nextV=$info["pzh"];//后续凭证号；
		
			$sumDr+=$info["dr"];
			$sumCr+=$info["cr"];
		
			$strVfoot='<tr><td colspan="2" align="left">制单：'.$info["etr"].'</td>';
			$strVfoot.='<td align="center">审核：'.$info["chk"].'</td>';
			$strVfoot.='<td colspan="2" align="center">记账：'.$info["acc"].'</td></tr>';
		
			if($trNumber==6){
				echo '<tr class="trV_Print tv">';
				echo '<td></td><td></td>';
				echo '<td align="center">小计：</td>';
				echo '<td align="right">'.number_format($sumDr,2).'</td>';
				echo '<td align="right">'.number_format($sumCr,2).'</td>';
				echo '</tr>';
				$info=$sql->fetch_array(MYSQLI_BOTH);
				if(isset($info["pzh"])){
					if($nextV!=$info["pzh"]){
						$sumDr=0;
						$sumCr=0;
					}
				}
				goto abcV3;
			}

		}while($info=$sql->fetch_array(MYSQLI_BOTH));
	
		abcV2:
		if($trNumber<=5){
			echo '<tr class="trV_Print"><td align="center"></td><td></td><td></td><td></td><td></td></tr>';
			$trNumber++;
			goto abcV2;
		}
	
		echo '<tr class="trV_Print tv">';
		echo '<td></td><td></td>';
		echo '<td align="center">小计：</td>';
		echo '<td align="right">'.number_format($sumDr,2).'</td>';
		echo '<td align="right">'.number_format($sumCr,2).'</td>';
		echo '</tr>';
	
		echo $strVfoot;
			
		echo '</table>';
		echo '</div>';
	
		abcV4:

	}else{
		echo '未找到相关凭证数据！';
	}
	
}else{//非生成查询页面；
	echo '<div align="left" class="access2">
		<form id="form_VbookPrint">
		<table>
		<tr>
		<td>期间：</td>
		<td>
		<select name="qj0" size="1">';
		foreach($objQj->arrayQj as $intqj){
		  echo '<option value="'.$intqj.'">'.$intqj.'</option>';
		}
	echo '</select>
		</td>
		<td align="center">—</td>
		<td>
		<select name="qj1" size="1">';
		foreach($objQj->arrayQj as $intqj){
		  echo '<option value="'.$intqj.'">'.$intqj.'</option>';
		}
	echo '</select>
		</td>
		</tr>
		<tr>
		<td>凭证号：</td>
		<td><input name="pzh0" type="text" style="width:88px" /></td>
		<td align="center">—</td>
		<td><input name="pzh1" type="text" style="width:88px" /></td>
		</tr>
		<tr>
		<td>摘要：</td>
		<td colspan="3"><input name="zy" type="text" style="width:255px" /></td>
		</tr>
		<tr>
		<td>科目全称：</td>
		<td colspan="3"><input name="kmF" type="text" style="width:255px" /></td>
		</tr>
		<tr>
		<td>借贷金额：</td>
		<td colspan="3"><input name="DC" type="text" style="width:255px" /></td>
		</tr>
		<tr>
		<td>录入：</td>
		<td>';
		//是否开放查阅所有凭证功能；
		if($arrGroup["开放查阅"]){
			echo '<input name="etr" type="text" style="width:96px" />';
		}else{
			echo '<input name="etr" type="text" style="width:96px" value="'.$arrPublicVar["username"].'" readonly />';
		}
	echo '</td>
		<td align="center">审核：</td>
		<td><abbr title="１、是：已审核；２、否：未审核；３、审核人名称"><input name="chk" type="text" style="width:96px" /></abbr></td>
		</tr>
		<tr>
		<td colspan="4" align="center"><input id="tj_VbookPrint" name="tj" type="button" value="生成" /></td>
		</tr>
		</table>
		</form>
		</div>';
}
unset($objQj);
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//生成查询凭证条件；
	$("#rightHand>div:visible #tj_VbookPrint").click(function(){
		$.get("templates/VbookPrint.php",$("#rightHand>div:visible #form_VbookPrint").serialize(),function(data,textStatus){
			$("#rightHand>div:visible").html(data);
		});
		return false;
	});
	//打印；
	$("#rightHand>div:visible #print_VbookPrint").click(function(){
		$("#rightHand>div:visible #printV_VbookPrint").print(/*options*/);
	});
});
//-->
</script>