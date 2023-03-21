<?php
require("../config.inc.php");
if(!$arrGroup["会计凭证"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

//提取核算项目的类
class pro{
	public $proName;
	public function xmhs($id){
		global $mysqli;
	    $sql=$mysqli->query('select slwb,xjll,kh,gr,ch,gys,bm,xm from account where id="'.$id.'"');
		$info=$sql->fetch_array(MYSQLI_BOTH);
		if($info["slwb"]==-1){
			$this->proName='数量外币';
		}
		if($info["xjll"]==-1){
			$this->proName=$this->proName.'|现金流量';
		}
		if($info["kh"]==-1){
			$this->proName=$this->proName.'|客户';
		}
		if($info["gr"]==-1){
			$this->proName=$this->proName.'|个人';
		}
		if($info["ch"]==-1){
			$this->proName=$this->proName.'|存货';
		}
		if($info["gys"]==-1){
			$this->proName=$this->proName.'|供应商';
		}
		if($info["bm"]==-1){
			$this->proName=$this->proName.'|部门';
		}
		if($info["xm"]==-1){
			$this->proName=$this->proName.'|核算项目';
		}
		if(substr($this->proName,0,1)=="|"){
			$this->proName=substr($this->proName,1,strlen($this->proName)-1);
		}
		if(strlen($this->proName)>0){
			return $this->proName;
		}else{
			return "空白";
		}
	}
}

$searchText=mysqli_real_escape_string($mysqli,$_GET["searchText"]);
$strSql='select id,Fname,t,dc from account where (id like "'.$searchText.'%" or Fname like "%'.$searchText.'%") and ty=1 order by id asc';
	
//=====================数据分页代码（开始）=====================
$page=isset($_GET['page'])?$_GET['page']:1;//页码数
//echo '页码数：'.$page.',';
$limitStart=$page==1?0:$arrPublicVar["comboRows"]*($page-1);//SQL语句中limit起始数
//echo 'limit起始数'.$limitStart.'<br/>';
$totalRecords=mysqli_num_rows($mysqli->query($strSql));//总页数
$p=new Page($totalRecords,10,$page,$arrPublicVar["comboRows"]);
//=====================数据分页代码（结束）=====================

$strSql=$strSql.' limit '.$limitStart.','.$arrPublicVar["comboRows"];//在常用strSql语句后面加入limit语句
//echo $strSql;
$sql=$mysqli->query($strSql);
$info=$sql->fetch_array(MYSQLI_BOTH);
echo '<table id="tbl2_V1" class="mytable">';
if($info){ //如果存在数据；
    DO{
	   echo '<tr onclick="trClick(this)">';
	   echo '<td>'.$info["id"].'</td>';
	   echo '<td>'.$info["Fname"].'</td>';
	   $xmName=new pro();
	   echo '<td>'.$xmName->xmhs($info["id"]).'</td>';
	   echo '<td>'.$info["dc"].'</td>';
	   echo '<td>'.$info["t"].'</td>';
	   echo '</tr>';
	}while($info=$sql->fetch_array(MYSQLI_BOTH));
	//写最后一行内容；
	echo '<tr>';
	echo '<td colspan="5">';
	echo '<div style="float:left">'.$p->showPages(2).'</div>';
	echo '</td>';
	echo '</tr>';
}else{//如果不存在数据，直接写最后一行；
    //写最后一行内容；
	echo '<tr>';
	echo '<td>';
	echo '<div style="float:left">无满足条件的记录</div>';
	echo '</td>';
	echo '</tr>';
}
echo '</table>';

$sql->free();
$mysqli->close();
?>