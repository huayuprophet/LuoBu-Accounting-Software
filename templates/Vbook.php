<?php
require("../config.inc.php");
if(!$arrGroup["序时账簿"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//序时账簿页面操作按钮；
echo '<div class="access1">
	<input id="id_Vbook" type="hidden" />
	<input id="add_Vbook" type="button" value="新增" />
	<input id="edit_Vbook" type="button" value="修改" />
	<input id="delete_Vbook" type="button" value="删除" />
	<input id="copy_Vbook" type="button" value="复制" />
	<input id="check_Vbook" type="button" value="审核" />
	<input id="output_Vbook" type="button" value="导出" />
	<input id="profit_Vbook" type="button" value="结转损益" />
	<input id="check_all_Vbook" type="button" value="全部审核" />
	<input id="check_unall_Vbook" type="button" value="全部反审核" />
	<input id="record_Vbook" type="button" value="记账" />
	<input id="recordoff_Vbook" type="button" value="反记账" />
	<a id="init_Vbook" href="#" target="black"><input type="button" value="入库单据" title="遵循先借后贷原则" /></a>
	<a id="gout_Vbook" href="#" target="black"><input type="button" value="出库单据" title="标记行为最后打印行" /></a>
	<a id="file_Vbook" href="#" target="black"><input type="button" value="附件管理" /></a>
	</div>';

$objQj=new QJ();
$objQj->getQj('select distinct qj from v1 order by qj desc');

if(($_SERVER['REQUEST_METHOD']=='GET')&&isset($_GET["qj0"])&&isset($_GET["qj1"])){//提交查询页面；
	echo '<div align="left" class="access2">
		<form id="form_Vbook" autocomplete="off">
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
		<td colspan="4" align="center"><input id="tj_Vbook" name="tj" type="button" value="提交" /></td>
		</tr>
		</table>
		</form>';
	
	//提交数据成功，先构造查询凭证表的SQL语句，然后将查询到的相关数据写入table；
	$qj0=mysqli_real_escape_string($mysqli,$_GET["qj0"]);
	$qj1=mysqli_real_escape_string($mysqli,$_GET["qj1"]);
	
	$strsql='select distinct concat(qj,pzh) from V1 where (qj between "'.$qj0.'" and "'.$qj1.'")';
	
	if(($_GET["pzh0"]!="")&&($_GET["pzh1"]=="")){
		@$strsql.=' and pzh>="'.$_GET["pzh0"].'"';
	}else if(($_GET["pzh0"]=="")&&($_GET["pzh1"]!="")){
	    @$strsql.=' and pzh<="'.$_GET[pzh1].'"';
	}else if(($_GET["pzh0"]!="")&&($_GET["pzh1"]!="")){
		@$strsql.=' and (pzh between "'.$_GET["pzh0"].'" and "'.$_GET["pzh1"].'")';
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
	
	$num_rows=$sql->num_rows;

	$info=$sql->fetch_array(MYSQLI_BOTH);
	
	echo '<hr width="98%" align="left">';
	echo '<div align="left">查找到 '.$num_rows.' 条符合条件的记录，单击选择凭证后可进行凭证按钮操作；</div>';//显示查询的记录数；
	echo '<hr width="98%" align="left">';
	
	if($info){ //如果存在数据；
		echo '<div id="tbl1Div_Vbook" align="left">
			<table id="tbl1_Vbook" class="mytable">
			<thead><tr align="center">
			<td>T</td>
			<td>业务日期</td>
			<td>录入日期</td>
			<td>期间</td>
			<td>凭证号</td>
			<td>附件</td>
			<td>序号</td>
			<td>摘要</td>
			<td>科目</td>
			<td>科目全称</td>
			<td>借方</td>
			<td>贷方</td>
			<td>录入</td>
			<td>审核</td>
			<td>记账</td>
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
		   echo '<td></td>';
		   echo '<td align="center">'.$info["ywrq"].'</td>';
		   echo '<td align="center">'.$info["lrrq"].'</td>';
		   echo '<td align="center">'.$info["qj"].'</td>';
		   echo '<td align="center"><strong>'.$info["pzh"].'</strong></td>';
		   echo '<td align="center">'.$info["fj"].'</td>';
		   echo '<td align="center">'.$info["xh"].'</td>';
		   echo '<td>'.$info["zy"].'</td>';
		   echo '<td>'.$info["km"].'</td>';
		   echo '<td>'.$info["kmF"].'</td>';
		   echo '<td align="right">'.($info["dr"]==0?"":toMoney($info["dr"],2)).'</td>';
		   echo '<td align="right">'.($info["cr"]==0?"":toMoney($info["cr"],2)).'</td>';
		   echo '<td>'.$info["etr"].'</td>';
		   echo '<td>'.$info["chk"].'</td>';
		   echo '<td>'.$info["acc"].'</td>';
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
		<form id="form_Vbook" autocomplete="off">
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
		<td colspan="4" align="center"><input id="tj_Vbook" name="tj" type="button" value="提交" /></td>
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
	$("#rightHand>div:visible #tbl1_Vbook").mytable({interlacedColor:false,selectedColor:false,clickedColor:true,columnWidth:true});
	//会计科目下拉提示：
	$("#rightHand>div:visible input[name=kmF]").getSuggestBox({RequestURI:"templates/accountSearch.php",FindSource:"",Callback:function(arrtd){
		$("#rightHand>div:visible input[name=kmF]").val(arrtd[1]);
	}});
	//提交查询凭证条件；
	$("#rightHand>div:visible #tj_Vbook").click(function(){
		$.get("templates/Vbook.php",$("#form_Vbook").serialize(),function(data,textStatus){
			$("#rightHand>div:visible").html(data);
		});
		
		return false;
	});
	//单击选取序时账簿中的凭证；
	$("#rightHand>div:visible #tbl1_Vbook tbody>tr").click(function(){
		//单击标记选择；
		if($(this).hasClass("clicked")){
			$("#rightHand>div:visible #tbl1_Vbook tr").removeClass("clicked");//去单击标色；
			$("#rightHand>div:visible #tbl1_Vbook tr td span").remove();//去单击星号；
			//找出关键变量，向上和向下查找符合条件的行；
			var rn=$(this).index()+1;
			var qj=$(this).find("td:eq(3)").text();
			var pzh=$(this).find("td:eq(4)").text();
			//向上查找；
			for($x=rn;$x>0;$x--){
				$currentRow = $("#rightHand>div:visible #tbl1_Vbook tr").eq($x);
				if($currentRow.find("td:eq(3)").text()==qj&&$currentRow.find("td:eq(4)").text()==pzh){
					$currentRow.addClass("clicked");
				}else{
					break;//减少程序计算量；
				}
			}
			//向下查找；
			var rnmax=$("#rightHand>div:visible #tbl1_Vbook tr").length;
			for($y=rn;$y<rnmax;$y++){
				$currentRow = $("#rightHand>div:visible #tbl1_Vbook tr").eq($y);
				if($currentRow.find("td:eq(3)").text()==qj&&$currentRow.find("td:eq(4)").text()==pzh){
					$currentRow.addClass("clicked");
				}else{
					break;//减少程序计算量；
				}
			}
			$(this).find("td").eq(0).append("<span>★</span>");//加单击星号；
		}else{
			$(this).find("td>span").remove();
		}
		//按标记赋值；
		if($("#rightHand>div:visible #tbl1_Vbook tr td span").length>0){
			var $elementtd=$("#rightHand>div:visible #tbl1_Vbook tr:has(span)").find("td");
			$("#rightHand>div:visible #id_Vbook").val('qj='+$elementtd.eq(3).text()+'&pzh='+$elementtd.eq(4).text()+'&xh='+$elementtd.eq(6).text());
		}else{
			$("#rightHand>div:visible #id_Vbook").val('');
		}
		//eq(13)是审核列；
		if($(this).find("td").eq(13).text()==''){
			$("#rightHand>div:visible #edit_Vbook").val("修改");
			$("#rightHand>div:visible #delete_Vbook").attr("disabled",false);
			$("#rightHand>div:visible #check_Vbook").val("审核");
		}else{
			$("#rightHand>div:visible #edit_Vbook").val("只读");
			$("#rightHand>div:visible #delete_Vbook").attr("disabled",true);
			$("#rightHand>div:visible #check_Vbook").val("反审核");
		}
		//eq(14)是记账列；
		if($(this).find("td").eq(14).text()==''){
			$("#rightHand>div:visible #check_Vbook").attr("disabled",false);
		}else{
			$("#rightHand>div:visible #check_Vbook").attr("disabled",true);
		}
		
		return false;
	});
	//新增凭证；
	$("#rightHand>div:visible #add_Vbook").click(function(){
		$(this).mytabs({strTitle:"会计凭证",strUrl:"templates/V_1.php"});
	});
	//修改凭证；
	$("#rightHand>div:visible #edit_Vbook").click(function(){
		//检测是否选中凭证；
		if($("#rightHand>div:visible #id_Vbook").val()==''){
			alert("请先选取凭证！");
			return false;
		}
		//打开相应的会计凭证功能页面；
		var url="templates/V_1.php?handle=editV&"+$("#rightHand>div:visible #id_Vbook").val();
		$(this).mytabs({strTitle:"会计凭证",strUrl:url});
	});
	//删除凭证；
	$("#rightHand>div:visible #delete_Vbook").click(function(){
		//检测是否选中凭证；
		if($("#rightHand>div:visible #id_Vbook").val()==''){
			alert("请先选取凭证！");
			return false;
		}
		//删除前提醒；
		if(!confirm("您确信要删除该凭证吗？")){
			return false;
		}
		var url="templates/V_drop.php?handle=deleteV&"+$("#rightHand>div:visible #id_Vbook").val();
		$.get(url,function(data,textStatus){
			alert(data);
			$("#rightHand>div:visible #tj_Vbook").click();
		});
		return false;
	});
	//复制凭证；
	$("#rightHand>div:visible #copy_Vbook").click(function(){
		//检测是否选中凭证；
		if($("#rightHand>div:visible #id_Vbook").val()==''){
			alert("请先选取凭证！");
			return false;
		}
		//打开相应的会计凭证功能页面；
		var url="templates/V_1.php?handle=copyV&"+$("#rightHand>div:visible #id_Vbook").val();
		$(this).mytabs({strTitle:"会计凭证",strUrl:url});
	});
	//审核凭证；
	$("#rightHand>div:visible #check_Vbook").click(function(){
		//审核或反审核凭证前需关闭会计凭证维护界面
		if($("#tabMenu>li:contains('会计凭证')").length==1){
			alert("请先关闭会计凭证功能！");
			return false;
		}
		//检测是否选中凭证；
		if($("#rightHand>div:visible #id_Vbook").val()==''){
			alert("请先选取凭证！");
			return false;
		}
		var url="templates/V_check.php?handle=checkV&"+$("#rightHand>div:visible #id_Vbook").val();
		$.get(url,function(data,textStatus){
			if(data=='ys'){
				$("#rightHand>div:visible #tj_Vbook").click();
				alert('审核成功！')
			}else if(data=='ns'){
				$("#rightHand>div:visible #tj_Vbook").click();
				alert('反审核成功！')
			}else{
				alert(data);
			}
		});
		return false;
	});
	//全部审核；
	$("#rightHand>div:visible #check_all_Vbook").click(function(){
		//审核或反审核凭证前需关闭会计凭证维护界面
		if($("#tabMenu>li:contains('会计凭证')").length==1){
			alert("请先关闭会计凭证功能！");
			return false;
		}
		//检测表格中是否有数据；
		if($("#rightHand>div:visible #tbl1_Vbook tr").length==0){
			alert("无数据！");
			return false;
		}
		//循环出表格中的数据；
		var arrV=[];
		$("#rightHand>div:visible #tbl1_Vbook tr").each(function(){
			var vph=$(this).find("td").eq(3).text()+"-"+$(this).find("td").eq(4).text();
			arrV.push(vph);
		})
		arrV.shift();//删除数组中的第一个元素；
		$.unique(arrV);//删除数组中的重复元素；
		//alert(arrV);
		//此处采用post方法传递参数，防止数据过多使用get方式造成数据的丢失；
		//var url="templates/V_check_all.php?handle=checkV&data_checkV="+arrV;
		var url="templates/V_check_all.php";
		$.post(url,{
			handle : 'checkV',
			data_checkV : arrV
		},function(data,textStatus){
			alert(data);
			$("#rightHand>div:visible #tj_Vbook").click();
		});
		return false;
	});
	//全部反审核；
		$("#rightHand>div:visible #check_unall_Vbook").click(function(){
		//审核或反审核凭证前需关闭会计凭证维护界面
		if($("#tabMenu>li:contains('会计凭证')").length==1){
			alert("请先关闭会计凭证功能！");
			return false;
		}
		//检测表格中是否有数据；
		if($("#rightHand>div:visible #tbl1_Vbook tr").length==0){
			alert("无数据！");
			return false;
		}
		//循环出表格中的数据；
		var arrV=[];
		$("#rightHand>div:visible #tbl1_Vbook tr").each(function(){
			var vph=$(this).find("td").eq(3).text()+"-"+$(this).find("td").eq(4).text();
			arrV.push(vph);
		})
		arrV.shift();//删除数组中的第一个元素；
		$.unique(arrV);//删除数组中的重复元素；
		//alert(arrV);
		//此处采用post方法传递参数，防止数据过多使用get方式造成数据的丢失；
		//var url="templates/V_check_all.php?handle=checkV&data_checkV="+arrV;
		var url="templates/V_check_unall.php";
		$.post(url,{
			handle : 'checkV',
			data_checkV : arrV
		},function(data,textStatus){
			alert(data);
			$("#rightHand>div:visible #tj_Vbook").click();
		});
		return false;
	});
	//导出所有凭证；
	$("#rightHand>div:visible #output_Vbook").click(function(){
 		$("#rightHand>div:visible #tbl1_Vbook").table2excel({
			exclude: ".noExl",
			name: "Book",
			filename: "Book"+ new Date().toISOString().replace(/[\-\:\.]/g, "")+".xls",
			fileext: ".xls",
			exclude_img: true,
			exclude_links: true,
			exclude_inputs: true
		});
	});
	//结转损益；
	$("#rightHand>div:visible #profit_Vbook").click(function(){
		$("#popbox").popup({title:"结转损益:",width:"500",height:"300"}).load("templates/V_jzsy.php");
	});
	//凭证记账；
	$("#rightHand>div:visible #record_Vbook").click(function(){
		$("#popbox").popup({title:"凭证记账:",width:"500",height:"300"}).load("templates/V_account_1.php");
	});
	//凭证反记账；
	$("#rightHand>div:visible #recordoff_Vbook").click(function(){
		$("#popbox").popup({title:"凭证反记账:",width:"500",height:"300"}).load("templates/V_accountNodo_1.php");
	});
	//入库单据；
	$("#rightHand>div:visible #init_Vbook").click(function(){
		//检测是否选中凭证；
		if($("#rightHand>div:visible #id_Vbook").val()==''){
			alert("请先选取凭证！");
			return false;
		}
		var url="templates/VbookPrint_1.php?"+$("#rightHand>div:visible #id_Vbook").val();
		//$("#rightHand>div:visible #init_Vbook").attr("href",url);
		$(this).mytabs({strTitle:"入库单据",strUrl:url});
		return false;
	});
	//出库单据；
	$("#rightHand>div:visible #gout_Vbook").click(function(){
		//检测是否选中凭证；
		if($("#rightHand>div:visible #id_Vbook").val()==''){
			alert("请先选取凭证！");
			return false;
		}
		var url="templates/VbookPrint_2.php?"+$("#rightHand>div:visible #id_Vbook").val();
		//$("#rightHand>div:visible #gout_Vbook").attr("href",url);
		$(this).mytabs({strTitle:"出库单据",strUrl:url});
		return false;
	});
	//附件管理；
	$("#rightHand>div:visible #file_Vbook").click(function(){
		//检测是否选中凭证；
		if($("#rightHand>div:visible #id_Vbook").val()==''){
			alert("请先选取凭证！");
			return false;
		}
		var url="templates/V_upFile.php?"+$("#rightHand>div:visible #id_Vbook").val();
		//$("#rightHand>div:visible #file_Vbook").attr("href",url);
		$(this).mytabs({strTitle:"附件管理",strUrl:url});
		return false;
	});
});
//-->
</script>