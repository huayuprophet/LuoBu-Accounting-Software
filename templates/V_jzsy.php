<?php
require("../config.inc.php");
if(!$arrGroup["序时账簿"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if(($_SERVER['REQUEST_METHOD']=='POST')&&isset($_POST["qj"])){//提交查询页面；

	$qj=mysqli_real_escape_string($mysqli,$_POST["qj"]);
	
	//结转前必须保证该期间内的所有凭证都已审核；
	$sql=$mysqli->query('select * from v1 where qj="'.$qj.'" and chk is null');
	$afr=$sql->num_rows;
	if($afr>0){
		echo '结转失败，原因：该期间'.$qj.'存在未审核的凭证！';
		exit();
	}
	$sql->free();
	
	//取出凭证表中最大ID值；
	$sql=$mysqli->query('select max(id) as idMax from v1');
	$info=$sql->fetch_array(MYSQLI_BOTH);
	if($info){
		$id=$info["idMax"]+1;
	}else{
		$id=1;
	}
	$sql->free();
	
	//echo $id.'<br />';
	
	//取出指定期间最大凭证号；
	$sql=$mysqli->query('select max(pzh) as pzhMax from v1 where qj="'.$qj.'"');
	$info=$sql->fetch_array(MYSQLI_BOTH);
	if($info){
		$pzh=$info["pzhMax"]+1;
	}else{
		$pzh=1;
	}
	$sql->free();
	
	//echo $pzh.'<br />';
	
	//取出指定期间的第一天和最后一天；
	$dates=substr($qj,0,4).'-'.substr($qj,4,2).'-1';
	$dates=@date('Y-m-d', strtotime(date('Y-m-01', strtotime($dates)) . ' +1 month -1 day'));
	
	//echo $dates.'<br />';
	
	//取出损益科目代码最小值与最大值；
	$sql=$mysqli->query('select min(id) as id1,max(id) as id2 from account where t="利润"');
	$info=$sql->fetch_array(MYSQLI_BOTH);
	if($info){
		$id1=$info["id1"];
		$id2=$info["id2"];
	}
	$sql->free();
	
	//echo $id1.'<br />';
	//echo $id2.'<br />';
	
	//取出需要结转的数据；(需要构造带条件的分组查询语句)
	$strsql='select qj,km,kmF,sum(slwb) as slwb,sum(slwbF) as slwbF,sum(dr) as dr,sum(cr) as cr from v1';
	$strsql=$strsql.' where km between "'.$id1.'" and "'.$id2.'" and qj="'.$qj.'" group by kmF;';
	//echo $strsql.'<br />';;
	$sql=$mysqli->query($strsql);
	$info=$sql->fetch_array(MYSQLI_BOTH);
	$i=1;//自动结转时凭证分录的序号；
	if($info){
		do{
			if($info["dr"]==0){
				$strsqlInsert="insert into v1(id,ywrq,lrrq,qj,pzh,xh,zy,km,kmF,slwb,slwbF,dr,cr,etr)";
				$strsqlInsert=$strsqlInsert." values('".$id."','".$dates."','".$dates."','".$qj."','".$pzh."','".$i."','结转损益','".$info["km"]."','".$info["kmF"]."',";
				$strsqlInsert=$strsqlInsert."'".$info["slwb"]."','".$info["slwbF"]."','".round($info["cr"],2)."','0','".$_SESSION["username"]."')";
			}else{
				$strsqlInsert="insert into v1(id,ywrq,lrrq,qj,pzh,xh,zy,km,kmF,slwb,slwbF,dr,cr,etr)";
				$strsqlInsert=$strsqlInsert." values('".$id."','".$dates."','".$dates."','".$qj."','".$pzh."','".$i."','结转损益','".$info["km"]."','".$info["kmF"]."',";
				$strsqlInsert=$strsqlInsert."'".$info["slwb"]."','".$info["slwbF"]."','0','".round($info["dr"],2)."','".$_SESSION["username"]."')";
			}
			$mysqli->query($strsqlInsert);
			$id++;
			$i++;
		}while($info=$sql->fetch_array(MYSQLI_BOTH));
		echo '结转成功：'.$qj.'；请打开结转凭证进行修正；';
	}else{
		echo '结转失败，原因：无结转数据：'.$qj;	
	}
}else{

	$strsql='select distinct qj from v1 where chk is not null and acc is null order by qj';
	$sql=$mysqli->query($strsql);
	$info=$sql->fetch_array(MYSQLI_BOTH);
	if($info){//如果满足条件的记录存在；
		echo '<form id="frmjzsy" name="frm1" action="templates/V_jzsy.php" method="post">';
	echo '<div align="center">
	      <table>
		  <tr height="60px" valign="bottom">
		  <td>结转期间：</td>
		  <td>
		  <select name="qj" size="1" require>';
	DO{
		echo '<option value="'.$info["qj"].'">'.$info["qj"].'</option>';
		}while($info=$sql->fetch_array(MYSQLI_BOTH));
	echo '</select>
		  </td>
		  <td><input type="submit" value="下一步" /></td>
		  </tr>
		  <tr height="60px" valign="bottom">
		  <td colspan="3">
		  <font color="red">1>该功能自动结转指定期间损益；</font>
		  </td>
		  </tr>
		  <tr height="60px" valign="bottom">
		  <td colspan="3">
		  <font color="red">2>结转成功后请对凭证进行修改；</font>
		  </td>
		  </tr>
		  </table>
		  </div>';
	echo '</form>';
	}else{
		echo '系统未检测到有结转损益的期间；';	
	}
	$sql->free();
	$mysqli->close();
	
	echo '<script language="javascript" type="text/javascript">
		<!--
		$(function(){
			//新增或保存
			var options = { 
				beforeSubmit:showRequest, // pre-submit callback
				success:showResponse, // post-submit callback
			}; 
			$("#popbox #frmjzsy").ajaxForm(options); 
		});
		// pre-submit callback 
		function showRequest(formData, jqForm, options){ 

		} 
		// post-submit callback 
		function showResponse(responseText, statusText){
			alert(responseText);
			$("#popbox div.popupclose").trigger("click")
			$("#rightHand>div:visible #tj_Vbook").click();
		}
		//-->
		</script>';
}
?>