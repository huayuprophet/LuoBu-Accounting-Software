<?php
require("../config.inc.php");
if(!$arrGroup["会计科目"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if($_SERVER['REQUEST_METHOD']=='POST' && $_POST["edit"]=='修改'){

	$id=mysqli_real_escape_string($mysqli,trim($_POST["id"]));
	$name=mysqli_real_escape_string($mysqli,trim($_POST["name"]));
	
	//会计科目代码必须是数字；
	if(!preg_match("/^\d{4,12}$/",$id)){
		exit('ID必须为4-12位的数字！');
	}
	
	//验证会计科目的长度是否正确；
	$array1=array(4,6,8,10,12);
	if(!in_array(strlen($id),$array1,true)){
		echo '修改失败；原因：ID长度不正确；';
		exit();
	}
	
	//验证科目名称是否包含匹配字符；
	foreach($fieldValidation as $yz){
		if(substr_count($name,$yz)!=0){
			echo '验证失败；原因：名称中不能出现 '.$yz.' 字符';
			exit();
		}
	}
	
	//获取数据修改前名称字段值；
	$strsql='select name from account where id="'.$id.'"';
	$oldsql=$mysqli->query($strsql);
	$olddata=$oldsql->fetch_array(MYSQLI_BOTH);

	//修改前检查该记录是否已经审核；
	$strsql='select * from account where id="'.$id.'" and chk is not null';
	$afr=mysqli_num_rows($mysqli->query($strsql));
	if($afr>0){
		echo '修改失败；原因：该科目已经被审核；';
		exit();
	}
		
	//保存数据；
	$strsql='update account set name="'.$name.'",Fname="'.$name.'",t="'.$_POST["t"].'",dc="'.$_POST["dc"].'",';
	$strsql=$strsql.'slwb="'.$_POST["slwb"].'",xjll="'.$_POST["xjll"].'",kh="'.$_POST["kh"].'",gr="'.$_POST["gr"].'",ch="'.$_POST["ch"].'",';
	$strsql=$strsql.'gys="'.$_POST["gys"].'",bm="'.$_POST["bm"].'",xm="'.$_POST["xm"].'",etr="'.$_SESSION["username"].'" where id="'.$id.'"';

	$update=$mysqli->query($strsql);

	if($update){ //如果存在数据；
		echo '修改成功；';
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
			echo "\n".'自动更新会计科目成功；';
		}
		$sqlabc->free();//语句结束；
		
		//该代码自动更新凭证表和记账表中会计科目信息；
		//更新凭证表中的相关数据；
		@$strsql='update v1 set kmF=replace(kmF,"'.$olddata["name"].'","'.$_POST["name"].'") where km like "'.$_POST["id"].'%"';
		//echo '<br>'.$strsql;
		$mysqli->query($strsql);
		$afr=$mysqli->affected_rows;
		if($afr>0){
			echo "\n".'凭证表更新成功：共有'.$afr.'条记录受到影响；';
		}else{
			echo "\n".'凭证表未更新，原因：该会计科目凭证表未使用；';
		}
		
		//更新记账表中的相关数据；
		@$strsql='update v2 set kmF=replace(kmF,"'.$olddata["name"].'","'.$_POST["name"].'") where km like "'.$_POST["id"].'%"';
		//echo '<br>'.$strsql;
		$mysqli->query($strsql);
		$afr=$mysqli->affected_rows;
		if($afr>0){
			echo "\n".'记账表更新成功：共有'.$afr.'条记录受到影响；';
		}else{
			echo "\n".'记账表未更新，原因：该会计科目记账表未使用；';
		}
	}else{
		echo '修改失败；';
	}
			
	$mysqli->close();
	
}else{
	echo '无效的参数调用，请使用正确的方式打开页面！';
}
?>