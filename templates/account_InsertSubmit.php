<?php
require("../config.inc.php");
if(!$arrGroup["会计科目"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='POST' && ($_POST["insert"]=='新增' || $_POST["insert"]=='复制')){

	$id=mysqli_real_escape_string($mysqli,trim($_POST["id"]));
	$name=mysqli_real_escape_string($mysqli,trim($_POST["name"]));
	
	//会计科目代码必须是数字；
	if(!preg_match("/^\d{4,12}$/",$id)){
		exit('ID必须为4-12位的数字！');
	}
	
	//验证会计科目的长度是否正确；
	$array1=array(4,6,8,10,12);
	if(!in_array(strlen($id),$array1,true)){
		echo '新增失败；原因：ID长度不正确；';
		exit();
	}
	
	//验证科目名称是否包含匹配字符；
	foreach($fieldValidation as $yz){
		if(substr_count($name,$yz)!=0){
			echo '验证失败；原因：名称中不能出现 '.$yz.' 字符';
			exit();
		}
	}
	
	//新增前检查ID是否存在上级；
	if(strlen($id)>4){
		$strsql='select * from account where id="'.substr($id,0,strlen($id)-2).'"';
		$sql=$mysqli->query($strsql);
		$afr=$sql->num_rows;
		if($afr==0){
			echo '新增失败；原因：ID不存在上级；';
			exit();
		}
		$sql->free();
	}

	//新增前检查ID对应的上级是否被V1表中的km字段使用；
	if(strlen($id)>4){
		$strsql='select * from v1 where km="'.substr($id,0,strlen($id)-2).'"';
		$sql=$mysqli->query($strsql);
		$afr=$sql->num_rows;
		if($afr!=0){
			echo '新增失败；原因：ID对应的上级已被凭证表使用；';
			exit();
		}
		$sql->free();
	}

	//新增数据开始；
	$strsql='insert into account(id,name,Fname,t,dc,slwb,xjll,kh,gr,ch,gys,bm,xm,etr)';
	$strsql=$strsql.' values("'.$id.'","'.$name.'","'.$name.'","'.$_POST["t"].'","'.$_POST["dc"].'","'.$_POST["slwb"].'","'.$_POST["xjll"].'",';
	$strsql=$strsql.'"'.$_POST["kh"].'","'.$_POST["gr"].'","'.$_POST["ch"].'","'.$_POST["gys"].'","'.$_POST["bm"].'","'.$_POST["xm"].'","'.$_SESSION["username"].'")';

	$insert=$mysqli->query($strsql);

	if($insert){ //新增执行成功；
		echo '新增成功；';
		
		//语句开始：该代码自动更新会计科目全称；
		$sqlabc=$mysqli->query('select id,name,Fname from account order by id asc');
		$infoabc=$sqlabc->fetch_array(MYSQLI_BOTH);
		if($infoabc){
			do{
				if(strlen($infoabc["id"])!=4){
					//echo substr($infoabc[id],0,strlen($infoabc[id])-2).'<br>';
					$sqlF=$mysqli->query('select Fname from account where id="'.substr($infoabc["id"],0,strlen($infoabc["id"])-2).'"');
					$infoF=$sqlF->fetch_array(MYSQLI_BOTH);
					$Fname=$infoF["Fname"].'→'.$infoabc["name"];
					//echo $Fname.'<br>';
					$mysqli->query('update account set Fname="'.$Fname.'" where id="'.$infoabc["id"].'"');	
				}
			}while($infoabc=$sqlabc->fetch_array(MYSQLI_BOTH));
			echo '自动更新会计科目成功！';
		}
		$sqlabc->free();//语句结束；
		   
	}else{
		echo '新增失败；';
	}
	$mysqli->close();
	
}else{
	echo '无效的参数调用，请使用正确的方式打开页面！';
}
?>