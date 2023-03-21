<?php
require("../config.inc.php");
if(!$arrGroup["科目账表"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

$objQj=new QJ();
$objQj->getQj('select distinct qj from v1 order by qj desc');

if(($_SERVER['REQUEST_METHOD']=='POST')&&isset($_POST["qj0"])&&isset($_POST["qj1"])){
	//提交查询页面；
	echo '<form id="form_kmyrb" method="post" autocomplete="off">
	<table>
	<tr>
	<td>期间：</td>
	<td>
	<select name="qj0" size="1">';
		foreach($objQj->arrayQj as $intqj){
			if($intqj==$_POST["qj0"]){
				echo '<option value="'.$intqj.'" selected="selected">'.$intqj.'</option>';
			}else{
				echo '<option value="'.$intqj.'">'.$intqj.'</option>';  
			}
		}
		echo '</select>
	</td>
	<td>—</td>
	<td>
	<select name="qj1" size="1">';
		foreach($objQj->arrayQj as $intqj){
			if($intqj==$_POST["qj1"]){
				echo '<option value="'.$intqj.'" selected="selected">'.$intqj.'</option>';
			}else{
				echo '<option value="'.$intqj.'">'.$intqj.'</option>';  
			}
		}
		echo '</select>
	</td>
	</tr>
	<tr>
	<td>会计科目：</td>
	<td><input name="km0" type="text" style="width:120px" value="'.$_POST["km0"].'" /></td>
	<td>—</td>
	<td><input name="km1" type="text" style="width:120px" value="'.$_POST["km1"].'" /></td>
	</tr>';
	
	echo '<tr>
	      <td>包含未记账：</td>';
	if($_POST["jz"]==1){
		echo '<td colspan="3">
			<select name="jz">
			<option value="1" selected="selected">是</option>
			<option value="0">否</option>
			</select>
			</td>';
	}else{
		echo '<td colspan="3">
			<select name="jz">
			<option value="0" selected="selected">否</option>
			<option value="1">是</option>
			</select>
			</td>';
	}
	echo '</tr>';
	
	echo '<tr>
	      <td>显示数量外币：</td>';
	if($_POST["slwb"]==1){
		echo '<td colspan="3">
			<select name="slwb">
			<option value="1" selected="selected">是</option>
			<option value="0">否</option>
			</select>
			</td>';
	}else{
		echo '<td colspan="3">
			<select name="slwb">
			<option value="0" selected="selected">否</option>
			<option value="1">是</option>
			</select>
			</td>';
	}
	echo '</tr>';
	
	if($_POST["xm"]==1){
		echo '<tr>
	        <td>包含项目明细：</td>
		    <td colspan="3">
			<select name="xm">
			<option value="1" selected="selected">是</option>
			<option value="0">否</option>
			</select>
			</td>
			</tr>';
		echo '<tr>
			<td>查找项目名称：</td>
			<td colspan="3">
			<input name="xms" type="text" style="width:160px" value="'.$_POST["xms"].'" />
			</td>
			</tr>';
	}else{
		echo '<tr>
	        <td>包含项目明细：</td>
			<td colspan="3">
			<select name="xm">
			<option value="0" selected="selected">否</option>
			<option value="1">是</option>
			</select>
			</td>
			</tr>';
		echo '<tr>
			<td>查找项目名称：</td>
			<td colspan="3">
			<input name="xms" type="text" style="width:160px" value="'.$_POST["xms"].'" />
			</td>
			</tr>';
	}
	echo '<tr>
	<td colspan="4" align="center"><input id="tj_kmyrb" name="tj" type="button" value="提交" /></td>
	</tr>
	</table>
	</form>';
	
	//提交数据成功，先构造查询凭证表的SQL语句，然后将查询到的相关数据写入table；
	$qj0=mysqli_real_escape_string($mysqli,$_POST["qj0"]);
	$qj1=mysqli_real_escape_string($mysqli,$_POST["qj1"]);
	$km0=mysqli_real_escape_string($mysqli,$_POST["km0"]);
	$km1=mysqli_real_escape_string($mysqli,$_POST["km1"]);
	$xms=mysqli_real_escape_string($mysqli,$_POST["xms"]);
	
	$strsql7='期间：'.$qj0.'→'.$qj1.'|会计科目'.$km0.'→'.$km1.'|包含未记账：'.(($_POST["jz"]==0)?'否':'是').'|数量外币：'.(($_POST["slwb"]==0)?'否':'是').'|项目明细：'.(($_POST["xm"]==0)?'否':'是').'|项目名称：'.$xms;
	
	echo '<div align="left">
		<a id="output_kmyrb" href="#">导出数据</a>|
		<a id="print_kmyrb" href="#">打印数据</a>
		</div>';
	
	echo '<div id="div1_kmyrb" align="left">';
	
	//调用生成科目余额表的存储过程，生成kmyrb临时表；
	//kmyrbs存储过程参数：起始期间，终止期间，起始科目，终止科目，是否记账，是否包含项目，是否统计期初数：（1表示统计期初数）；
	//是否统计期初数仅用于查询科目明细时使用，查询科目明细时，期初数由本界面期初数通过POST给值；
	$mysqli->query("call kmyrbs('".$qj0."','".$qj1."','".$km0."','".$km1."','".$_POST["jz"]."','".$_POST["xm"]."','1')");
	//如果包含项目明细，则用字段t来分组区分那些是科目数据，那些是项目数据；
	if($_POST["xm"]==1 and $xms!=''){//模糊查找项目名称；
		$strsql='select km,kma,dc,sum(slwb0) as slwb0,sum(dr0) as dr0,sum(cr0) as cr0,sum(ds1) as ds1,sum(dr1) as dr1,sum(cs1) as cs1,sum(cr1) as cr1 from kmyrb';
		$strsql.=' where kma like "%'.$xms.'%" group by km,kma,dc,t order by km,kma;';
	}else{
		$strsql='select km,kma,dc,sum(slwb0) as slwb0,sum(dr0) as dr0,sum(cr0) as cr0,sum(ds1) as ds1,sum(dr1) as dr1,sum(cs1) as cs1,sum(cr1) as cr1 from kmyrb';
		$strsql.=' group by km,kma,dc,t order by km,kma;';
	}
	
	$sql=$mysqli->query($strsql);
	
	if($sql->num_rows>0){ //如果存在数据；
		if($_POST["slwb"]==1){
			//含数量外币列，显示表格的id="tbl2_kmyrb"；
			echo '<table id="tbl2_kmyrb" align="left">
				<caption>
				<h1>科目余额表</h1>
				<div align="left">会计主体：<u>'.$_SESSION["strCompanyName"].'</u></div>
				<div align="left">查询条件：<u>'.$strsql7.'</u></div>
				</caption>';
			echo '<thead><tr>
					<td align="center">会计科目</td>
					<td align="center">科目全称</td>
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
					</tr></thead>';
		}else{
			//不含数量外币列，显示表格的id="tbl1_kmyrb"；
			echo '<table id="tbl1_kmyrb" align="left">
				<caption>
				<h1>科目余额表</h1>
				<div align="left">会计主体：<u>'.$_SESSION["strCompanyName"].'</u></div>
				<div align="left">查询条件：<u>'.$strsql7.'</u></div>
				</caption>';
			echo '<thead><tr>
					<td align="center">会计科目</td>
					<td align="center">科目全称</td>
					<td align="center">方向</td>
					<td align="center">期初余额</td>
					<td align="center">借方金额</td>
					<td align="center">贷方金额</td>
					<td align="center">期末余额</td>
					</tr></thead>';
		}
		
		while($info=$sql->fetch_array(MYSQLI_BOTH)){
			if($_POST["slwb"]==1){//含数量外币列；
				if($info["dc"]=="借方"){
					$strurl='templates/Vbook_kmyrbsub.php?qj0='.$_POST["qj0"].'&qj1='.$_POST["qj1"].'&km='.$info["km"].'&slwb='.$info["slwb0"].'&yr='.@round($info["dr0"]-$info["cr0"],2).'&jz='.$_POST["jz"];
					echo '<tr>';
					if(substr_count($info["kma"],"|")==0){
						echo '<td align="left"><a href='.$strurl.'>'.$info["km"].'</a></td>';
					}else{
						echo '<td align="left">'.$info["km"].'</td>';	
					}
					echo '<td align="left">'.$info["kma"].'</td>';
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
				}else{
					$strurl='templates/Vbook_kmyrbsub.php?qj0='.$_POST["qj0"].'&qj1='.$_POST["qj1"].'&km='.$info["km"].'&slwb='.$info["slwb0"].'&yr='.@round($info["cr0"]-$info["dr0"],2).'&jz='.$_POST["jz"];
					echo '<tr>';
					if(substr_count($info["kma"],"|")==0){
						echo '<td align="left"><a href='.$strurl.'>'.$info["km"].'</a></td>';
					}else{
						echo '<td align="left">'.$info["km"].'</td>';
					}
					echo '<td align="left">'.$info["kma"].'</td>';
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
				}
			}else{//不包含数量外币列；
				if($info["dc"]=="借方"){
					$strurl='templates/Vbook_kmyrbsub.php?qj0='.$_POST["qj0"].'&qj1='.$_POST["qj1"].'&km='.$info["km"].'&slwb=a&yr='.@round($info["dr0"]-$info["cr0"],2).'&jz='.$_POST["jz"];
					echo '<tr>';
					if(substr_count($info["kma"],"|")==0){
						echo '<td align="left"><a href='.$strurl.'>'.$info["km"].'</a></td>';
					}else{
						echo '<td align="left">'.$info["km"].'</td>';	
					}
					echo '<td align="left">'.$info["kma"].'</td>';
					echo '<td align="center">'.$info["dc"].'</td>';
					echo '<td align="right">'.@toMoney($info["dr0"]-$info["cr0"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["dr1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["dr0"]-$info["cr0"]+$info["dr1"]-$info["cr1"],2).'</td>';
					echo '</tr>';
				}else{
					$strurl='templates/Vbook_kmyrbsub.php?qj0='.$_POST["qj0"].'&qj1='.$_POST["qj1"].'&km='.$info["km"].'&slwb=a&yr='.@round($info["cr0"]-$info["dr0"],2).'&jz='.$_POST["jz"];
					echo '<tr>';
					if(substr_count($info["kma"],"|")==0){
						echo '<td align="left"><a href='.$strurl.'>'.$info["km"].'</a></td>';
					}else{
						echo '<td align="left">'.$info["km"].'</td>';
					}
					echo '<td align="left">'.$info["kma"].'</td>';
					echo '<td align="center">'.$info["dc"].'</td>';
					echo '<td align="right">'.@toMoney($info["cr0"]-$info["dr0"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["dr1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr1"],2).'</td>';
					echo '<td align="right">'.@toMoney($info["cr0"]-$info["dr0"]+$info["cr1"]-$info["dr1"],2).'</td>';
					echo '</tr>';
				}
			}
			
		}
		echo '</table></div>';
	}else{
		echo '<div align="left">未检索到相关数据；</div>';
	}
}else{//非提交查询页面；
	echo '<form id="form_kmyrb" method="post" autocomplete="off">
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
	<td>—</td>
	<td>
	<select name="qj1" size="1">';
		foreach($objQj->arrayQj as $intqj){
		  echo '<option value="'.$intqj.'">'.$intqj.'</option>';
		}
		echo '</select>
	</td>
	</tr>
	<tr>
	<td>会计科目：</td>
	<td><input name="km0" type="text" style="width:120px" /></td>
	<td>—</td>
	<td><input name="km1" type="text" style="width:120px" /></td>
	</tr>
	<tr>
	<td>包含未记账：</td>
	<td colspan="3">
	<select name="jz">
	<option value="1" selected="selected">是</option>
	<option value="0">否</option>
	</select>
	</td>
	</tr>
	<tr>
	<td>显示数量外币：</td>
	<td colspan="3">
	<select name="slwb">
	<option value="0" selected="selected">否</option>
	<option value="1">是</option>
	</select>
	</td>
	</tr>
	<tr>
	<td>包含项目明细：</td>
	<td colspan="3">
	<select name="xm">
	<option value="0" selected="selected">否</option>
	<option value="1">是</option>
	</select>
	</td>
	</tr>
	<tr>
	<td>查找项目名称：</td>
	<td colspan="3">
	<input name="xms" type="text" style="width:160px" />
	</td>
	</tr>
	<tr>
	<td colspan="4" align="center"><input id="tj_kmyrb" name="tj" type="button" value="提交" /></td>
	</tr>
	</table>
	</form>';
}
unset($objQj);
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//表格插件应用
	$("#rightHand>div:visible #tbl1_kmyrb").mytable();
	$("#rightHand>div:visible #tbl2_kmyrb").mytable();
	//会计科目下拉提示：
	$("#rightHand>div:visible input[name=km0]").getSuggestBox({RequestURI:"templates/accountSearch.php",FindSource:"",Callback:function(arrtd){
		$("#rightHand>div:visible input[name=km0]").val(arrtd[0]);
	}});
	//会计科目下拉提示：
	$("#rightHand>div:visible input[name=km1]").getSuggestBox({RequestURI:"templates/accountSearch.php",FindSource:"",Callback:function(arrtd){
		$("#rightHand>div:visible input[name=km1]").val(arrtd[0]);
	}});
	//提交查询凭证条件；
	$("#rightHand>div:visible #tj_kmyrb").click(function(){
		$.post("templates/Vbook_kmyrb.php",$("#rightHand>div:visible #form_kmyrb").serialize(),function(data,textStatus){
			$("#rightHand>div:visible").html(data);
		});
		return false;
	});
	//点击获取明细账；
	$("#rightHand>div:visible #tbl1_kmyrb a").click(function(){
		$(this).mytabs({strTitle:"科目明细账",strUrl:""});
		return false;
	});
	$("#rightHand>div:visible #tbl2_kmyrb a").click(function(){
		$(this).mytabs({strTitle:"科目明细账",strUrl:""});
		return false;
	});
	//导出数据；
	$("#rightHand>div:visible #output_kmyrb").click(function(){
 		$("#rightHand>div:visible #tbl1_kmyrb,#rightHand>div:visible #tbl2_kmyrb").table2excel({
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
	$("#rightHand>div:visible #print_kmyrb").click(function(){
		$("#rightHand>div:visible #div1_kmyrb").print(/*options*/);
	});
});
//-->
</script>