<?php
require("../config.inc.php");
if(!$arrGroup["项目账表"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
//项目对应已使用会计科目选取框；
$col=mysqli_real_escape_string($mysqli,$_POST["xmName"]);
$strSqlAccCount="select distinct id,Fname from account where id in(select distinct km from v1 where $col is not null)";
$sqlAccCount=$mysqli->query($strSqlAccCount);
while($infoAccCount=$sqlAccCount->fetch_array(MYSQLI_BOTH)){
	echo '<input type="checkbox" name="km_Vbook_xmyrb[]" class="accCount_Vbook_xmyrb" value="'.$infoAccCount['id'].'" checked>'.$infoAccCount["id"].'—'.$infoAccCount["Fname"].'</br>';
}
?>