<?php 
require("../config.inc.php");
if(!$arrGroup["成本计算"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//必须是未记账的期间和满足条件的会计科目才能进行成本核算；
$strsql1='select distinct qj from v1 where acc is null order by qj';
$strsql2='select id,Fname from account where slwb=-1 and ch=-1';
$sql1=$mysqli->query($strsql1);
$sql2=$mysqli->query($strsql2);
$info1=$sql1->fetch_array(MYSQLI_BOTH);
$info2=$sql2->fetch_array(MYSQLI_BOTH);
echo '<form id="frmcost_cost">
	<table align="left">
		<tr><td align="center" colspan="2"><br /><h3>商贸类</h3></td></tr>
		<tr><td colspan="2"><hr /></td></tr>
		<tr><td align="left" colspan="2">注意事项：相关的会计科目仅带有数量和存货核算。</td></tr>
		<tr><td>请选择会计期间：</td><td><select id="qj1_cost" size="1">';
				if($info1){
					DO{
						echo '<option value="'.$info1[qj].'">'.$info1[qj].'</option>';
						}while($info1=$sql1->fetch_array(MYSQLI_BOTH));
				}else{
					echo '<option value="非法期间">无有效会计期间</option>';
				}
				echo '</select> <font color="red"><strong>*</strong></font></td></tr>
		<tr><td>请选择会计科目：</td><td><select id="km1_cost" size="1">';
			if($info2){
				DO{
					echo '<option value="'.$info2[id].'">'.$info2[id].'→'.$info2[Fname].'</option>';
					}while($info2=$sql2->fetch_array(MYSQLI_BOTH));
			}else{
				echo '<option value="非法科目">无有效会计科目</option>';
			}
			echo '</select></td></tr>';
		echo '<tr><td>存货起始ID：</td><td><input id="start1_cost" type="text" /></td></tr>';
		echo '<tr><td>存货终止ID：</td><td><input id="end1_cost" type="text" /></td></tr>';

	echo '<tr><td colspan="2"><hr /></td></tr><tr><td>借方新会计科目：</td><td><select id="kmdr1_cost" size="1">';
			$sql2=$mysqli->query($strsql2);
			$info2=$sql2->fetch_array(MYSQLI_BOTH);
			if($info2){
				DO{
					echo '<option value="'.$info2[id].'">'.$info2[id].'→'.$info2[Fname].'</option>';
					}while($info2=$sql2->fetch_array(MYSQLI_BOTH));
			}else{
				echo '<option value="非法科目">无有效会计科目</option>';
			}
			echo '</select></td></tr><tr><td>贷方新会计科目：</td><td><select id="kmcr1_cost" size="1">';
			$sql2=$mysqli->query($strsql2);
			$info2=$sql2->fetch_array(MYSQLI_BOTH);
			if($info2){
				DO{
					echo '<option value="'.$info2[id].'">'.$info2[id].'→'.$info2[Fname].'</option>';
					}while($info2=$sql2->fetch_array(MYSQLI_BOTH));
			}else{
				echo '<option value="非法科目">无有效会计科目</option>';
			}
			echo '</select> <font color="red"><strong>*</strong></font></td></tr><tr><td colspan="2"><hr /></td></tr>
		<tr><td align="center" colspan="2"><img id="wait1_pzsc_cost" style="display:none" src="picture/wait.gif" /><input id="ca_cost" type="button" value="生成凭证" /></td></tr>
		<tr><td colspan="2"><br /><br /></td></tr>
		<tr><td align="center" colspan="2"><h3>生产类：<font color="red"><strong>*</strong></font> 号控件为生产类专用</h3></td></tr>
	<tr><td align="center" colspan="2"><img id="wait2_pzsc_cost" style="display:none" src="picture/wait.gif" /><input id="js_cost" type="button" value="计算成本" /></td></tr>
	</table>
	</form>';
	
$sql1->free();
$sql2->free();
$mysqli->close();
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//会计科目下拉提示：
	$("#rightHand>div:visible #kmzp_cost").getSuggestBox({RequestURI:"templates/accountSearch.php",FindSource:"",Callback:function(arrtd){
		$("#rightHand>div:visible #kmzp_cost").val(arrtd[0]);
	}});
	//凭证生成按钮提交；
	$("#rightHand>div:visible #ca_cost").click(function(){
		//必需字段验证；
		if($("#qj1_cost").val()=="非法期间"){
			alert("会计期间不合法！");
			$("#qj1_cost").focus();
			return false;
		}
		if($("#km1_cost").val()=="非法科目"){
			alert("会计科目不合法！");
			$("#km1_cost").focus();
			return false;
		}
		if($("#kmdr1_cost").val()=="非法科目"){
			alert("会计科目不合法！");
			$("#kmdr1_cost").focus();
			return false;
		}
		if($("#kmcr1_cost").val()=="非法科目"){
			alert("会计科目不合法！");
			$("#kmcr1_cost").focus();
			return false;
		}
		//提交凭证生成命令；
		if(confirm("您确信要进行凭证生成操作吗？")){
			$("#wait1_pzsc_cost").show();
			var url="templates/costcount_1.php?qj1="+$("#qj1_cost").val()+"&km1="+$("#km1_cost").val()+"&ch1="+$("#start1_cost").val()+"&ch2="+$("#end1_cost").val()+"&mb1="+$("#kmdr1_cost").val()+"&mb2="+$("#kmcr1_cost").val();
			$.get(url,function(data,textStatus){
				alert(data);
				$("#wait1_pzsc_cost").hide();
			});
		}
	});
	//成本计算按钮提交；
	$("#rightHand>div:visible #js_cost").click(function(){
		//必需字段验证；
		if($("#qj1_cost").val()=="非法期间"){
			alert("会计期间不合法！");
			$("#qj1_cost").focus();
			return false;
		}
		if($("#kmcr1_cost").val()=="非法科目"){
			alert("会计科目不合法！");
			$("#kmcr1_cost").focus();
			return false;
		}
		//提交凭证生成命令；
		if(confirm("您确信对｛"+$("#qj1_cost").val()+"｝期的｛"+$("#kmcr1_cost").find("option:selected").text()+"｝进行成本计算吗？")){
			$("#wait2_pzsc_cost").show();
			var url="templates/costcount_2.php?qj1="+$("#qj1_cost").val()+"&km1="+$("#km1_cost").val()+"&ch1="+$("#start1_cost").val()+"&ch2="+$("#end1_cost").val()+"&mb1="+$("#kmdr1_cost").val()+"&mb2="+$("#kmcr1_cost").val();
			$.get(url,function(data,textStatus){
				alert(data);
				$("#wait2_pzsc_cost").hide();
			});
		}
	});

})
//-->
</script>