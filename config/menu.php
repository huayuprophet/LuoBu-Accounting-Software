<?php
//引用配置文件；
require("../config.inc.php");
//将权限数组转化成json格式数据；
echo json_encode($arrGID);
?>