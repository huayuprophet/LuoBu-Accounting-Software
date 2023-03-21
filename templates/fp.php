<?php
require("../config.inc.php");
if(!$arrGroup["发票管理"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//发票账簿页面操作按钮；
echo '<div class="access1">
	<input id="delete_fp" type="button" value="删除" />
	<input id="split_fp" type="button" value="拆分" />
	<input id="merge_fp" type="button" value="合并" />
	<input id="output_fp" type="button" value="导出" />
	</div>';

$objQj=new QJ();
$objQj->getQj('select distinct qj from v1 order by qj desc');

if(($_SERVER['REQUEST_METHOD']=='GET')&&isset($_GET["qj0"])&&isset($_GET["qj1"])){//提交查询页面；
	echo '<div align="left" class="access2">
		<form id="form_fp" autocomplete="off">
		<table>
		<tr>
		<td>数据：</td>
		<td>
			<select id="data_type_fp" name="data_type" size="1">';
				if($_GET["data_type"]=='rk'){
					echo '<option value="rk" selected="selected">入库流水</option><option value="ck">出库流水</option>';
				}else{
					echo '<option value="rk">入库流水</option><option value="ck" selected="selected">出库流水</option>';
				}
		echo '</select></td>
		<td>状态：</td>
		<td><select id="spli_type_fp" name="spli_type" size="1">';
		if($_GET["spli_type"]=='all'){
			echo '<option value="all" selected="selected">全部</option><option value="alldj">已开票</option><option value="allundj">未开票</option>';
		}elseif($_GET["spli_type"]=='alldj'){
			echo '<option value="all">全部</option><option value="alldj" selected="selected">已开票</option><option value="allundj">未开票</option>';
		}else{
			echo '<option value="all">全部</option><option value="alldj">已开票</option><option value="allundj" selected="selected">未开票</option>';
		}
		echo '</select></td></tr>
		<tr>
		<td>期间：</td>
		<td><select name="qj0" size="1">';
		foreach($objQj->arrayQj as $intqj){
			if($intqj==$_GET["qj0"]){
				echo '<option value="'.$intqj.'" selected="selected">'.$intqj.'</option>';
			}else{
				echo '<option value="'.$intqj.'">'.$intqj.'</option>';  
			}
		}
	echo '</select></td><td>—</td><td>
		<select name="qj1" size="1">';
		foreach($objQj->arrayQj as $intqj){
			if($intqj==$_GET["qj1"]){
				echo '<option value="'.$intqj.'" selected="selected">'.$intqj.'</option>';
			}else{
				echo '<option value="'.$intqj.'">'.$intqj.'</option>';  
			}
		}
	echo '</select></td></tr>
		<tr>
		<td>摘要：</td>
		<td colspan="3"><input name="zy" type="text" style="width:255px" value="'.$_GET["zy"].'" /></td>
		</tr>
		<tr>
		<td>客商名称：</td>
		<td colspan="3"><input name="ksF" type="text" style="width:255px" value="'.$_GET["ksF"].'" /></td>
		</tr>
		<tr>
		<td>存货名称：</td>
		<td colspan="3"><input name="chF" type="text" style="width:255px" value="'.$_GET["chF"].'"/></td>
		</tr>
		<tr>
		<td>项目名称：</td>
		<td colspan="3"><input name="xmF" type="text" style="width:255px" value="'.$_GET["xmF"].'"/></td>
		</tr>
		<tr>
		<td>发票日期：</td>
		<td colspan="3"><input name="fprq" type="date" style="width:255px" value="'.$_GET["fprq"].'" /></td>
		</tr>
		<tr>
		<tr>
		<td>发票号码：</td>
		<td colspan="3"><input name="fphm" type="text" style="width:255px" value="'.$_GET["fphm"].'" /></td>
		</tr>
		<tr>
		<td colspan="4" align="center"><input id="tj_fp" name="tj" type="button" value="提交" /></td>
		</tr>
		</table>
		</form>';
	
	//提交数据成功，先构造查询凭证表的SQL语句；
	$lx=mysqli_real_escape_string($mysqli,$_GET["data_type"]);
	$zt=mysqli_real_escape_string($mysqli,$_GET["spli_type"]);
	$qj0=mysqli_real_escape_string($mysqli,$_GET["qj0"]);
	$qj1=mysqli_real_escape_string($mysqli,$_GET["qj1"]);
	$zy=mysqli_real_escape_string($mysqli,$_GET["zy"]);
	$ksF=mysqli_real_escape_string($mysqli,$_GET["ksF"]);
	$chF=mysqli_real_escape_string($mysqli,$_GET["chF"]);
	$xmF=mysqli_real_escape_string($mysqli,$_GET["xmF"]);
	$fprq=mysqli_real_escape_string($mysqli,$_GET["fprq"]);
	$fphm=mysqli_real_escape_string($mysqli,$_GET["fphm"]);
	
	if($lx=='rk'){
		$strsql='select * from chin where (qj between "'.$qj0.'" and "'.$qj1.'")';
	}else{
		$strsql='select * from chout where (qj between "'.$qj0.'" and "'.$qj1.'")';
	}

	if($zt=='all'){
		//全部数据
	}elseif ($zt=='alldj') {
		//已开票数据
		$strsql.=' and fphm is not null';
	}else{
		//未开票数据
		$strsql.=' and fphm is null';
	}
	
	if($zy!=""){
		$strsql.=' and zy like "%'.$zy.'%"';
	}
	
	if($ksF!=""){
		if($lx=='rk'){
			$strsql.=' and gysF like "%'.$ksF.'%"';
		}else{
			$strsql.=' and khF like "%'.$ksF.'%"';
		}
	}

	if($chF!=""){
		$strsql.=' and chF like "%'.$chF.'%"';
	}

	if($xmF!=""){
		$strsql.=' and xmF like "%'.$xmF.'%"';
	}
	
	if($fprq!=""){
		$strsql.=' and fprq="'.$fprq.'"';
	}

	if($fphm!=""){
		$strsql.=' and fphm="'.$fphm.'"';
	}

	$strsql.=' order by childid,id';

	//echo $strsql;//测试SQL语句；
	
	$sql=$mysqli->query($strsql);
	
	$num_rows=$sql->num_rows;

	$info=$sql->fetch_array(MYSQLI_BOTH);
	
	echo '<hr width="98%" align="left">';
	echo '<div align="left">查找到 '.$num_rows.' 条符合条件的记录，选取金额：
		<input id="jrsum_fp" type="number" style="width:200px" value="0" readonly />
		发票日期：<input id="fprq_fp" type="date" style="width:200px"/>
		发票号码：<input id="fphm_fp" type="text" style="width:200px"/>
		<input id="edit_fp" type="button" value="生成凭证" /> <input id="kp_fp" type="button" value="开票" /> <input id="qxkp_fp" type="button" value="取消开票" />
		</div>';//显示查询的记录数；
	echo '<hr width="98%" align="left">';
	
	if($info){ //如果存在数据；
		echo '<div id="tbl1Div_fp" align="left">
			<table id="tbl1_fp" class="mytable">
			<thead><tr align="center">
			<td align="center"><input id="chk_fp" type="checkbox" /></td>
			<td>期间</td>
			<td>凭证号</td>
			<td>业务日期</td>
			<td>科目</td>
			<td>科目全称</td>';
			if($lx=='rk'){
				echo '<td>供应商</td><td>供应商名称</td>';
			}else{
				echo '<td>客户</td><td>客户名称</td>';
			}
		echo '<td>存货</td>
			<td>存货名称</td>
			<td>项目</td>
			<td>项目名称</td>
			<td>数量</td>
			<td>单价</td>
			<td>金额</td>
			<td>摘要</td>
			<td>录入日期</td>
			<td>录入人</td>
			<td>开票日期</td>
			<td>发票号码</td>
			<td>关联期间</td>
			<td>关联凭证号</td>
			<td>关联ID</td>
			</tr></thead><tbody>';
		$intI=1;//表格行号；
		$intJt=1;//根据凭证号递增自动递增，使不同凭证号的表格底色交替显示；
		$intPzh=$info["qj"].'and'.$info["pzh"];//初始化“期间and凭证号”变量；
		DO{
		   if($intPzh!=$info["qj"].'and'.$info["pzh"]){
			   $intJt++;//凭证号不同时自动递增；
		   }
		   if(($intJt%2)==1){
			   echo '<tr id='.$intI.' class="odd">';
		   }else{
			   echo '<tr id='.$intI.'>';
		   }
		   echo '<td align="center"><input type="checkbox" value="'.$info["id"].'" /></td>';
		   echo '<td align="center">'.$info["qj"].'</td>';
		   echo '<td align="center"><strong>'.$info["pzh"].'</strong></td>';
		   echo '<td align="center">'.$info["ywrq"].'</td>';
		   echo '<td>'.$info["km"].'</td>';
		   echo '<td>'.$info["kmF"].'</td>';
		   if($lx=='rk'){
			   echo '<td>'.$info["gys"].'</td>';
			   echo '<td>'.$info["gysF"].'</td>';
		   }else{
			   echo '<td>'.$info["kh"].'</td>';
			   echo '<td>'.$info["khF"].'</td>';
		   }
		   echo '<td>'.$info["ch"].'</td>';
		   echo '<td>'.$info["chF"].'</td>';
		   echo '<td>'.$info["xm"].'</td>';
		   echo '<td>'.$info["xmF"].'</td>';
		   echo '<td align="right">'.toMoney($info["slwb"],2).'</td>';
		   echo '<td align="right">'.toMoney($info["slwbF"],2).'</td>';
		   if($lx=='rk'){
				echo '<td align="right">'.toMoney($info["dr"],2).'</td>';
		   }else{
				echo '<td align="right">'.toMoney($info["cr"],2).'</td>';
		   }
		   echo '<td>'.$info["zy"].'</td>';
		   echo '<td>'.$info["lrrq"].'</td>';
		   echo '<td>'.$info["etr"].'</td>';
		   echo '<td>'.$info["fprq"].'</td>';
		   echo '<td>'.$info["fphm"].'</td>';
		   echo '<td>'.$info["fppzqj"].'</td>';
		   echo '<td>'.$info["fppzh"].'</td>';
		   echo '<td>'.$info["childid"].'</td>';
		   echo '</tr>';
		   $intI++;
		   $intPzh=$info["qj"].'and'.$info["pzh"];//重新指定期间和凭证号；
		}while($info=$sql->fetch_array(MYSQLI_BOTH));
		echo '</tbody></table></div>';
	}else{
		echo '<div align="left">未检索到相关数据；</div>';
	}
	echo '</div>';
		
}else{//非提交查询页面；
	echo '<div align="left" class="access2">
		<form id="form_fp" autocomplete="off">
		<table>
		<tr>
		<td>数据：</td>
		<td>
			<select id="data_type_fp" name="data_type" size="1">
				<option value="rk" selected="selected">入库流水</option>
				<option value="ck">出库流水</option>
			</select>
		</td>
		<td>状态：</td>
		<td>
			<select id="spli_type_fp" name="spli_type" size="1">
				<option value="all" selected="selected">全部</option>
				<option value="alldj">已开票</option>
				<option value="allundj">未开票</option>
			</select>
		</td>
		</tr>
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
		<td>摘要：</td>
		<td colspan="3"><input name="zy" type="text" style="width:255px" /></td>
		</tr>
		<tr>
		<td>客商名称：</td>
		<td colspan="3"><input name="ksF" type="text" style="width:255px" /></td>
		</tr>
		<tr>
		<td>存货名称：</td>
		<td colspan="3"><input name="chF" type="text" style="width:255px" /></td>
		</tr>
		<tr>
		<td>项目名称：</td>
		<td colspan="3"><input name="xmF" type="text" style="width:255px" /></td>
		</tr>
		<tr>
		<td>开票日期：</td>
		<td colspan="3"><input name="fprq" type="date" style="width:255px" /></td>
		</tr>
		<tr>
		<tr>
		<td>发票号码：</td>
		<td colspan="3"><input name="fphm" type="text" style="width:255px" /></td>
		</tr>
		<tr>
		<td colspan="4" align="center"><input id="tj_fp" name="tj" type="button" value="提交" /></td>
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
	//表格样式优化；
	$("#rightHand>div:visible #tbl1_fp").mytable({interlacedColor:false,selectedColor:false,clickedColor:true,columnWidth:true});
	//提交查询凭证条件；
	$("#rightHand>div:visible #tj_fp").click(function(){
		$.get("templates/fp.php",$("#form_fp").serialize(),function(data,textStatus){
			$("#rightHand>div:visible").html(data);
		});
		return false;
	});
	//操作控件限定；
	if($("#rightHand>div:visible #data_type_fp").val()=='rk'){
		$("#rightHand>div:visible #kp_fp,#qxkp_fp").prop("disabled",true);
	}else{
		$("#rightHand>div:visible #edit_fp").prop("disabled",true);
	}
	//删除入出库流水：流水的删除，首先保证未开票，未生成凭证，符合这个条件的流水删除后，取消对应凭证的审核；
	$("#rightHand>div:visible #delete_fp").click(function(){
		//检测是否选中凭证；
		var $len=$("#rightHand>div:visible tbody input[type='checkbox']:checked").length;
		if($len!=1){
			alert('请勾选唯一数据！');
			return false;
		}
		//确信是否删除；
		if(!confirm("您确信要删除该流水数据吗？")){
			return false;
		}
		//向后台提交删除请求；
		var $qj=$("#rightHand>div:visible tbody input[type='checkbox']:checked").parent("td").next().text();
		var $pzh=$("#rightHand>div:visible tbody input[type='checkbox']:checked").parent("td").next().next().text();
		$.get("templates/fp_delete.php",{lx : $("#rightHand>div:visible #data_type_fp").val(),qj : $qj,pzh : $pzh},function(data,textStatus){
			alert(data);
			//界面自动更新
			$("#rightHand>div:visible #tj_fp").click();
		});
		return false;
	});
	//拆分；
	$("#rightHand>div:visible #split_fp").click(function(){
		//检测是否选中凭证；
		var $len=$("#rightHand>div:visible tbody input[type='checkbox']:checked").length;
		if($len!=1){
			alert('请勾选唯一数据！');
			return false;
		}
		var $oldr=$("#rightHand>div:visible tbody input[type='checkbox']:checked").parents("tr").find("td").eq(12).text();
		var $newr=prompt("请输入拆分的数量:");
		if(!$.isNumeric($newr)){
			alert('输入的拆分数字格式有误！');
			return false;
		}
		if(eval($oldr)<=eval($newr)){
			alert('输入的拆分数字过大！');
			return false;
		}
		//向后台提交删除请求；
		var $id=$("#rightHand>div:visible tbody input[type='checkbox']:checked").val();
		$.get("templates/fp_split.php",{lx : $("#rightHand>div:visible #data_type_fp").val(),id : $id,slr : $newr},function(data,textStatus){
			alert(data);
			//界面自动更新
			$("#rightHand>div:visible #tj_fp").click();
		});
		return false;
	});
	//合并；
	$("#rightHand>div:visible #merge_fp").click(function(){
		//检测是否选中凭证；
		var $len=$("#rightHand>div:visible tbody input[type='checkbox']:checked").length;
		if($len<2){
			alert('请勾选至少两条数据！');
			return false;
		}
		//确信要进行数据合并；
		if(!confirm("您确信要合并选中的流水数据吗？")){
			return false;
		}
		var $strstartid=""//所有选中记录的第一条记录的id；
		var $strid="";//所有选中记录的id(不包括第一条记录的id)；
		var $sumsl=0;//合并数量求和；
		var $sumjr=0;//合并金额求和；
		var $result = true;//程序是否中止标记
		$("#rightHand>div:visible tbody input[type='checkbox']:checked").each(function(){
			if($strstartid==""){
				$strstartid=$(this).val();
				$sumsl=parseFloat($(this).parents("tr").find("td").eq(12).text().replace(",", ""));
				$sumjr=parseFloat($(this).parents("tr").find("td").eq(14).text().replace(",", ""));
			}else{
			    if($(this).parents("tr").find("td").eq(22).text()!=$strstartid){
					$result=false;
					return false;
				}
				if($strid==""){
					$strid=$(this).val();
				}else{
					$strid+=','+ $(this).val();
				}
				$sumsl+=parseFloat($(this).parents("tr").find("td").eq(12).text().replace(",", ""));
				$sumjr+=parseFloat($(this).parents("tr").find("td").eq(14).text().replace(",", ""));
			}
		});
		if (!$result){
			alert('合并数据类型不符！');
			return false;
		}
		//向后台提交删除请求；
		$.post("templates/fp_merge.php",{lx : $("#rightHand>div:visible #data_type_fp").val(),startid : $strstartid,id : $strid,sumsl : $sumsl,sumjr : $sumjr},function(data,textStatus){
			alert(data);
			//界面自动更新
			$("#rightHand>div:visible #tj_fp").click();
		});
		return false;
	});
	//导出所有凭证；
	$("#rightHand>div:visible #output_fp").click(function(){
 		$("#rightHand>div:visible #tbl1Div_fp").table2excel({
			exclude: ".noExl",
			name: "fp",
			filename: "fp"+ new Date().toISOString().replace(/[\-\:\.]/g, "")+".xls",
			fileext: ".xls",
			exclude_img: true,
			exclude_links: true,
			exclude_inputs: true
		});
	});
	//自动计算功能；
	$("#rightHand>div:visible tbody input[type='checkbox']").on("change",function(){
		var $sumjr=0;//合并金额求和；
		$("#rightHand>div:visible tbody input[type='checkbox']:checked").each(function(){
			$sumjr+=parseFloat($(this).parents("tr").find("td").eq(14).text().replace(",", ""));
		});
		$("#rightHand>div:visible #jrsum_fp").val($sumjr);
		$("#rightHand>div:visible #chk_fp").prop("checked",false);
	});
	//全选框；
	$("#rightHand>div:visible #chk_fp").on("change",function(){
		if($(this).prop("checked")){
			$("#rightHand>div:visible input[type='checkbox']").prop("checked",true);
		}else{
			$("#rightHand>div:visible input[type='checkbox']").prop("checked",false);
		}
		var $sumjr=0;//合并金额求和；
		$("#rightHand>div:visible tbody input[type='checkbox']:checked").each(function(){
			$sumjr+=parseFloat($(this).parents("tr").find("td").eq(14).text().replace(",", ""));
		});
		$("#rightHand>div:visible #jrsum_fp").val($sumjr);
	});
	//生成凭证；
	$("#rightHand>div:visible #edit_fp").click(function(){
		//非空检查；
		if($("#rightHand>div:visible #jrsum_fp").val()==0){
			alert('金额不能为零！');
			$("#rightHand>div:visible #jrsum_fp").focus();
			return false;
		}
		if($("#rightHand>div:visible #fprq_fp").val()==""){
			alert('发票日期不能为空！');
			$("#rightHand>div:visible #fprq_fp").focus();
			return false;
		}
		if($("#rightHand>div:visible #fphm_fp").val()==""){
			alert('发票号码不能为空！');
			$("#rightHand>div:visible #fphm_fp").focus();
			return false;
		}
		//数据准备；
		var $strid="";//所有选中记录的id；
		var $result = true;//程序是否中止标记
		var $jrsum=$("#rightHand>div:visible #jrsum_fp").val();//选取金额
		var $fprq=$("#rightHand>div:visible #fprq_fp").val();//发票日期
		var $fphm=$("#rightHand>div:visible #fphm_fp").val();//发票号码
		$("#rightHand>div:visible tbody input[type='checkbox']:checked").each(function(){
			if($(this).parents("tr").find("td").eq(18).text()!=''){
				 $result=false;
				 return false;
			}
			if($strid==""){
				$strid=$(this).val();
			}else{
				$strid+=','+ $(this).val();
			}
			//获取标记的供应商id与名称
			$gysinfo=$(this).parents("tr").find("td").eq(6).text()+"→"+$(this).parents("tr").find("td").eq(7).text();
		});
		if (!$result){
			alert('数据重复请求！');
			return false;
		}
		//设置模板凭证，采用这种方式指定凭证模板，方便简洁；
		if($("#id_Vbook").length==0){
			alert("请先打开“序时账簿”选取模板凭证！");
			return false;
		}
		if($("#id_Vbook").val()==''){
			alert("请先在“序时账簿”选取模板凭证！");
			return false;
		}else{
			$pzmb=$("#id_Vbook").val();
		}
		//发送数据；
		$.post("templates/fp_account.php",{lx : $("#rightHand>div:visible #data_type_fp").val(),id : $strid,jrsum : $jrsum,fprq : $fprq,fphm : $fphm,pzmb : $pzmb,gysinfo : $gysinfo},function(data,textStatus){
			var $yz=data.substring(0,4);
			if($yz!='验证失败'){
				//打开相应的会计凭证功能页面；
				var url='templates/V_1.php?handle=editV&'+data;
				$(this).mytabs({strTitle:"会计凭证",strUrl:url});
			}else{
				alert(data);
			}
		});
		return false;
	});
	//开票；
	$("#rightHand>div:visible #kp_fp").click(function(){
		//非空检查；
		if($("#rightHand>div:visible #jrsum_fp").val()==0){
			alert('金额不能为零！');
			$("#rightHand>div:visible #jrsum_fp").focus();
			return false;
		}
		if($("#rightHand>div:visible #fprq_fp").val()==""){
			alert('发票日期不能为空！');
			$("#rightHand>div:visible #fprq_fp").focus();
			return false;
		}
		if($("#rightHand>div:visible #fphm_fp").val()==""){
			alert('发票号码不能为空！');
			$("#rightHand>div:visible #fphm_fp").focus();
			return false;
		}
		//数据准备；
		var $strid="";//所有选中记录的id；
		var $result = true;//程序是否中止标记
		var $jrsum=$("#rightHand>div:visible #jrsum_fp").val();
		var $fprq=$("#rightHand>div:visible #fprq_fp").val();
		var $fphm=$("#rightHand>div:visible #fphm_fp").val();
		$("#rightHand>div:visible tbody input[type='checkbox']:checked").each(function(){
			if($(this).parents("tr").find("td").eq(18).text()!=''){
				 $result=false;
				 return false;
			}
			if($strid==""){
				$strid=$(this).val();
			}else{
				$strid+=','+ $(this).val();
			}
		});
		if (!$result){
			alert('数据重复请求！');
			return false;
		}
		//发送数据；
		$.post("templates/fp_account.php",{lx : $("#rightHand>div:visible #data_type_fp").val(),id : $strid,jrsum : $jrsum,fprq : $fprq,fphm : $fphm},function(data,textStatus){
			alert(data);
			//界面自动更新
			$("#rightHand>div:visible #tj_fp").click();
		});
		return false;
	});
	//取消开票；
	$("#rightHand>div:visible #qxkp_fp").click(function(){
		//非空检查；
		if($("#rightHand>div:visible #jrsum_fp").val()==0){
			alert('金额不能为零！');
			$("#rightHand>div:visible #jrsum_fp").focus();
			return false;
		}
		//数据准备；
		var $strid="";//所有选中记录的id；
		var $result = true;//程序是否中止标记
		var $jrsum=$("#rightHand>div:visible #jrsum_fp").val();
		var $fprq=$("#rightHand>div:visible #fprq_fp").val();
		var $fphm=$("#rightHand>div:visible #fphm_fp").val();
		$("#rightHand>div:visible tbody input[type='checkbox']:checked").each(function(){
			if($strid==""){
				$strid=$(this).val();
			}else{
				$strid+=','+ $(this).val();
			}
		});
		//发送数据；
		$.post("templates/fp_account.php",{lx : 'qxkp',id : $strid,jrsum : $jrsum,fprq : $fprq,fphm : $fphm},function(data,textStatus){
			alert(data);
			//界面自动更新
			$("#rightHand>div:visible #tj_fp").click();
		});
		return false;
	});
});
//-->
</script>