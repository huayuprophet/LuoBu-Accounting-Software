<?php
/*config文件系统全局变量：主要运用于数据备的创建与备份文件存储*/
$fileName0='D:/AccSoft_Data/';//项目外存放文件总地址；

/*系统函数：获取客户端正确的IP地址*/
function getIp(){
	if (@$_SERVER["HTTP_CLIENT_IP"] && strcasecmp(@$_SERVER["HTTP_CLIENT_IP"], "unknown")) {
		$ip = @$_SERVER["HTTP_CLIENT_IP"];
	} else {
		if (@$_SERVER["HTTP_X_FORWARDED_FOR"] && strcasecmp(@$_SERVER["HTTP_X_FORWARDED_FOR"], "unknown")) {
			$ip = @$_SERVER["HTTP_X_FORWARDED_FOR"];
		} else {
			if ($_SERVER["REMOTE_ADDR"] && strcasecmp($_SERVER["REMOTE_ADDR"], "unknown")) {
				$ip = $_SERVER["REMOTE_ADDR"];
			} else {
				if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'],
						"unknown")
				) {
					$ip = $_SERVER['REMOTE_ADDR'];
				} else {
					$ip = "unknown";
				}
			}
		}
	}
	return ($ip);
}
?>