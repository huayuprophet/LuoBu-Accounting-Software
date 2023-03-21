<?php

//会话开始；
session_start();

//定义页面执行的时间为180秒；
set_time_limit(180);

//禁止绶存；
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pramga: no-cache");

/*
当在主机服务器上时，开启错误报告。
当主机以外的机器访问时，关闭所有错误报告；
*/
if(getIp()=='127.0.0.1'){
	error_reporting(-1);//打开所有错误报告；
}else{
	error_reporting(0);//关闭所有错误报告；
}

//全局数组变量；
$arrPublicVar=array();

/*
数据库连接参数设置；
如果mysql密码发生更改，请修改$arrPublicVar["pwd"]值；
*/
$arrPublicVar["server"]='127.0.0.1';
$arrPublicVar["user"]='root';
$arrPublicVar["pwd"]='12345678';

//定义签名 key ,用户可以自行设置 SIGN_KEY 值；
define("SIGN_KEY","lxj209");

//面向对象风格
$mysqli=@new mysqli($arrPublicVar["server"], $arrPublicVar["user"], $arrPublicVar["pwd"]);

//连接发生错误，中止程序的执行；
if($mysqli->connect_error) die('ConnectError');

//设置默认客户端字符集；
$mysqli->set_charset("utf8");
//时区设置；
date_default_timezone_set("PRC");
//设置下拉表格每页显示记录数;
$arrPublicVar["comboRows"]=10;
//设置分页表格每页显示记录数;
$arrPublicVar["tableRows"]=25;
//设置服务器时间变量；
$arrPublicVar["currentDate"]=date("Y-m-d");

//字符串验证正则表达示数组，String Validator;
$arraySV=array(
	"num4"=>array("/^\d{4}$/","四位数字组成的字符串"),
	"az09"=>array("/^[A-Za-z0-9]+$/","字母和数字组成的字符串"),
	"pwd"=>array("/^.{6,10}$/","密码必须为长度6-10位字符！"),
	"date"=>array("/^\d{4}-\d{1,2}-\d{1,2}$/","日期正确格式：yyyy-mm-dd"),
	"tel"=>array("/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/","手机号码格式不正确！"),
	"money"=>array("/(^([-]?)[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^([-]?)(0){1}$)|(^([-]?)[0-9]\.[0-9]([0-9])?$)/","金额小数位只能是两位！")
	);

//登录界面向后台【superac】超级账套请求数据库信息；
if(isset($_REQUEST["keywordSuggestBox"]) && $_REQUEST["findSource"]=="db_login"){
	//取出前端传来的关键字；
	$searchText=mysqli_real_escape_string($mysqli,$_REQUEST["keywordSuggestBox"]);
	//构建sql查询语句；
	$strSql='select id,dbname from superac.dbinfo where id like "'.$searchText.'%" or dbname like "%'.$searchText.'%" order by id asc';
	//执行sql语句
	$sql=$mysqli->query($strSql);
	//定义一个数组；
	$array=array();
	//循环数组，这里仅使用关联数组：MYSQLI_ASSOC；
	while($info=$sql->fetch_array(MYSQLI_ASSOC)){
		//向数组中添加数组，形成一个二维数组；
		array_push($array,$info);
	}
	$sql->free();
	$mysqli->close();
	//生成json数据并中止程序执行；
	exit(json_encode($array));
}

//登录验证，验证用户名及密码等信息，非登录时验证访问路径、会话变量等信息；
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST["db"]) && isset($_POST["user"]) && isset($_POST["pwd"])){
	$db=mysqli_real_escape_string($mysqli,$_POST["db"]);
	$user=mysqli_real_escape_string($mysqli,$_POST["user"]);
	$pwd=md5(mysqli_real_escape_string($mysqli,$_POST["pwd"]));
	//从超级账套中验证账套同用户是否匹配；
	$sql=$mysqli->query('select * from superac.dbuser where dbid="'.$db.'" and userid="'.$user.'"');
	$info=$sql->fetch_array(MYSQLI_BOTH);
	if(!$info){
		exit("Login：账套请求失败!");
	}
	//从超级账套中验证账户登录的账号与密码是否正确；
	$sql=$mysqli->query('select * from superac.user where id="'.$user.'" and password="'.$pwd.'"');
	$info=$sql->fetch_array(MYSQLI_BOTH);
	$xml=simplexml_load_file('config/trial_error1.xml');
	if($info){
		//向服务器验证是否有锁死的情况；
		$i=0;
		foreach($xml->sub as $sub){
			if($sub->ips==getIp()){
				if((int)$sub->nums == 6){
					exit('Login：用户或密码错误超出6次，系统锁死!');
				}else{
					unset($xml->sub[$i]);
					$xml->asXML('config/trial_error1.xml');
					break;
				}
			}
			$i++;
		}
		//选择要操作的数据库；
		$mysqli->select_db($db);
		//用户合法时，设置全局变量并继续程序执行；
		$_SESSION["db"]=$db;
		$_SESSION["userid"]=$info["id"];
		$_SESSION["username"]=$info["name"];
		$_SESSION["usergroup"]=$info["Gname"];
		$_SESSION["url_key"]=$_SERVER["HTTP_REFERER"];
		//登录成功，生成签名；
		$post["SESSION_ID"]=session_id();
		$post["USER_IP_ADDRESS"]=getIp();
		$post["HTTP_USER_AGENT"]=$_SERVER["HTTP_USER_AGENT"];
		ksort($post,SORT_REGULAR);
		$_SESSION["token"]=sign($post);
	}else{
		//向服务器请求登录出错的次数；
		$hasNote=false;
		foreach($xml->children() as $layer){
			if($layer->ips==getIp()){
				if((int)$layer->nums < 6){
					$layer->dts=$arrPublicVar["currentDate"];
					$layer->nums=(int)$layer->nums+1;
					$xml->asXML('config/trial_error1.xml');
					exit('Login：用户或密码错误，'.$layer->nums.'(出错次数)/6(总次数)');
				}else{
					exit('Login：用户或密码错误超出6次，系统锁死!');
				}
				$hasNote=true;
			}
		}
		if(!$hasNote){
			$xmlsub=$xml->addChild('sub');
			$xmlsub->addChild('ips',getIp());
			$xmlsub->addChild('dts',$arrPublicVar["currentDate"]);
			$xmlsub->addChild('nums',1);
			$xml->saveXML('config/trial_error1.xml');
			exit('Login：用户或密码错误，1(出错次数)/6(总次数)');
		}
	}
	$sql->free();
}else{
	//验证签名；
	$post["SESSION_ID"]=session_id();
	$post["USER_IP_ADDRESS"]=getIp();
	$post["HTTP_USER_AGENT"]=$_SERVER["HTTP_USER_AGENT"];
	ksort($post,SORT_REGULAR);
	$token=sign($post);
	if(isset($_SESSION["token"])){
		if($token!=$_SESSION["token"]){
			exit("签名错误，程序中止！");
		}else{
			session_regenerate_id();
			$post["SESSION_ID"]=session_id();
			$post["USER_IP_ADDRESS"]=getIp();
			$post["HTTP_USER_AGENT"]=$_SERVER["HTTP_USER_AGENT"];
			ksort($post,SORT_REGULAR);
			$_SESSION["token"]=sign($post);
		}
	}else{
		exit("签名丢失，程序中止！");
	}
	//检验访问服务器的路径是否发生改变；
	if(strpos($_SERVER["HTTP_REFERER"],$_SESSION["url_key"])!==false){
		//echo '新路径包含根路径';//注释原因：仅测试使用；
	}else{
		session_unset();
		session_destroy();
		setcookie('PHPSESSID',0,time()-3600);
		exit('<script language="javascript" type="text/javascript">
			$(function(){$("#footerinformation").load("config/sys_getLogin.php");});
			</script>');
	}
	//检验服务器上的各个会话变量是否存在；
	if($_SESSION["db"]=="") exit("非法访问,程序中止-1！");
	if($_SESSION["token"]=="") exit("非法访问,程序中止-2！");
	if($_SESSION["userid"]=="") exit("非法访问,程序中止-3！");
	if($_SESSION["username"]=="") exit("非法访问,程序中止-4！");
	if($_SESSION["usergroup"]=="") exit("非法访问,程序中止-5！");
	//数据库的重新选择；
	$mysqli->select_db($_SESSION["db"]);
	//设置当前数据库及账户信息；
	$arrPublicVar["db"]=$_SESSION["db"];
	$arrPublicVar["userid"]=$_SESSION["userid"];
	$arrPublicVar["username"]=$_SESSION["username"];
	$arrPublicVar["usergroup"]=$_SESSION["usergroup"];
	/*
	定义文件保存绝对地址，项目文件夹外，该方法更安全，建议采用，系统文件运用于数据库的创建与备份；
	在config文件夹下的conf.php文件中有类似的定义；
	*/
	$arrPublicVar["filesystems"]='D:/AccSoft_Data/'.$arrPublicVar["db"].'/';
	//$arrPublicVar["filesystems"]='access/'.$arrPublicVar["db"].'/';//定义文件保存相对地址，项目文件内，采用原因只因实施简单方便。

	/*
	获取当前用户组对应的组权限集，权限来源于【superac】账套，赋值一组数组：$arrGroup
	调用示例： 
		if(!$arrGroup['序时账簿']){
			echo '权限不够，请与管理员联系！';
			exit();
		}
	*/
	$arrGID=array();
	$arrGroup=array();
	$strSqlGetGroup='select Gcontent,Gvalue from superac.`groups` where Gname="'.$arrPublicVar["usergroup"].'"';
	$sqlGetGroup=$mysqli->query($strSqlGetGroup);
	while($infoGetGroup=$sqlGetGroup->fetch_array(MYSQLI_BOTH)){
		$GcontentValue=explode('=>',$infoGetGroup["Gcontent"]);
		$arrGID[$GcontentValue[0]]=$infoGetGroup["Gvalue"];
		$arrGroup[$GcontentValue[1]]=$infoGetGroup["Gvalue"];
	}
	$sqlGetGroup->free();
}

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

/*系统函数：签名，请在配置网站中自行设置 key 的值，以增强网站安全*/
function sign($data){
	$stringA = '';
	foreach ($data as $key=>$value){
		if(!$value) continue;
		if($stringA){
			$stringA .= '&'.$key."=".$value;
		}else{
			$stringA = $key."=".$value;
		}
	}
	$stringSignTemp = $stringA.'&key='.SIGN_KEY;
	return strtoupper(md5($stringSignTemp));
}

/*
系统函数：获取表中计算字段最大记录号的加一数字值；
调用示例：getNextNumber("表名称","需要计算字段名称");
*/
function getNextNumber($tableName,$tableFiled){
	global $mysqli;
	$strsql="select $tableFiled from $tableName order by $tableFiled desc limit 1";
	$sql=$mysqli->query($strsql);
	$info=$sql->fetch_array(MYSQLI_NUM);
	if(!$info){
		$maxNextID="1";
	}else{
		$maxNextID=(float)$info[0]+1;
	}
	return $maxNextID;
	$sql->free();
}

/*
系统函数：检验表中的字段（数组变量）是否存在指定的值,存在返回：true，否则返回false;
调用示例：getValueExist("表名称","字段1","字段的值1","字段2","字段的值2"......);
          getValueExist(array("v_chin","qj","201802","pzh",$intpzh));
		  参数的个数一定是：3、5、7、9……
*/
function getValueExist($arrayArgs){
	global $mysqli;
	if(count($arrayArgs)<3) return false;//参数数量不够三个，直接返回假；
	$strSqlValue="select * from $arrayArgs[0] where";
	for($i=1;$i<=(count($arrayArgs)-1)/2;$i++){
		$m=$i*2-1;$n=$i*2;
		if($i==1){
			$strSqlValue.=" $arrayArgs[$m]='$arrayArgs[$n]'";
		}else{
			$strSqlValue.=" and $arrayArgs[$m]='$arrayArgs[$n]'";
		}
	}
	$sqlValue=$mysqli->query($strSqlValue);
	$afrValue=$mysqli->affected_rows;
	if($afrValue>0){
		return true;
	}else{
		return false;
	}
}

/*
系统函数：根据ID(唯一值)获取数据库表中任一字段的信息；
调用示例：getDbField("表名称","字段ID","字段的值","获取的字段信息");
*/
function getDbField($table,$idname,$idvalue,$name){
	global $mysqli;
	$strSqlField="select $name from $table where $idname='$idvalue'";
	$sqlField=$mysqli->query($strSqlField);
	$infoField=$sqlField->fetch_array(MYSQLI_NUM);
	return $infoField[0];
	$sqlField->free();
}

/*系统函数：截取有限字符，多余的字符显示省略号（...）*/
function cut($strS,$intI){
	//return substr($strS,1,$intI).'...';//英文截取
	if(strlen($strS)>$intI){
		return mb_substr($strS,0,$intI,"utf-8").'...';
	}else{
		return $strS;
	}
}

/*系统函数：把浮点数据转化成标准货币金额，参数1：要转化的数据，参数2：保留几个小数*/
function toMoney($floatNum,$intSN){
	if(is_nan($floatNum)||is_infinite($floatNum)){
		return 0;
	}else{
		return number_format(round($floatNum,$intSN),$intSN,".",",");
	}
}

/*
系统函数：取出php字符串中的数字；
调用示例：findNum("带数字的字符串");
*/
function findNum($str=''){
    $str=trim($str);
    if(empty($str)){return '';}
    $reg='/(\d{3}(\.\d+)?)/is';//匹配数字的正则表达式
    preg_match_all($reg,$str,$result);
    if(is_array($result)&&!empty($result)&&!empty($result[1])&&!empty($result[1][0])){
        return $result[1][0];
    }
    return '';
}

/*
系统函数：根据mysql数据库表、视图信息自动生成h5页面控件信息；
调用示例：tblToH5("表名称或者视图名称","表或视图唯一标识ID值");
*/
function tblToH5($strTableName,$intid){
	global $mysqli;
	//取表结构信息；
	$strsql='show full columns from '.$strTableName.' from '.$_SESSION["db"];
	$sql=$mysqli->query($strsql);
	//取表数据信息；
	$id=mysqli_real_escape_string($mysqli,$intid);
	$strsql7='select * from '.$strTableName.' where id="'.$id.'"';
	$sql7=$mysqli->query($strsql7);
	$afr7=$mysqli->affected_rows;
	if($afr7==1){
		$info7=$sql7->fetch_array(MYSQLI_BOTH);
	}
	while($info=$sql->fetch_array(MYSQLI_BOTH)){
		//研究表明：text,date,boolean,enum 四个数据类型是比较特殊的，特别是复选框的值，在页面中是 on ，而数据库中是：1，h5页面到数据库需要转换；
		//必需字段标记；
		if($info["Null"]=='NO'){
			$strNotNull = 'required';
			$strNotNullTag = "<font color='red' size='5px'><strong> * </strong></font>";
		}else{
			$strNotNull = '';
			$strNotNullTag = '';
		}
		switch($info["Type"]){
			case "text":
				if($afr7==1){
					echo '<tr><td>'.$info["Comment"].'</td><td><textarea name="'.$info["Field"].'" rows="5" '.$strNotNull.'>'.$info7[$info["Field"]].'</textarea>'.$strNotNullTag.'</td></tr>';
				}else{
					echo '<tr><td>'.$info["Comment"].'</td><td><textarea name="'.$info["Field"].'" rows="5" '.$strNotNull.'></textarea>'.$strNotNullTag.'</td></tr>';
				}
				break;
			case "date":
			case "datetime":
				if($afr7==1){
					echo '<tr><td>'.$info["Comment"].'</td><td><input name="'.$info["Field"].'" type="date" value="'.$info7[$info["Field"]].'" '.$strNotNull.'>'.$strNotNullTag.'</td></tr>';
				}else{
					echo '<tr><td>'.$info["Comment"].'</td><td><input name="'.$info["Field"].'" type="date" '.$strNotNull.'></td>'.$strNotNullTag.'</tr>';
				}
				break;
			/*
			case "tinyint(1)":
			    //mysql表没有是否字段，建议建表时不使用复选框，1 表示 true,0 表标 false;
				if($afr7==1){
					echo '<tr><td>'.$info["Comment"].'</td><td><input name="'.$info["Field"].'" type="checkbox" '.($info7[$info["Field"]]?"checked":"").'></td></tr>';
				}else{
					echo '<tr><td>'.$info["Comment"].'</td><td><input name="'.$info["Field"].'" type="checkbox"></td></tr>';
				}
				break;
			*/
			case strstr($info["Type"],'enum'):
			case strstr($info["Type"],'set'):
				if(strpos($info["Type"],'enum')!==false){
					$stroldenum=substr($info["Type"],5,-1);
				}else{
					$stroldenum=substr($info["Type"],4,-1);
				}
				$strnewenum=str_ireplace("'","",$stroldenum);
				$arrenum=explode(",",$strnewenum);
				if($afr7==1){
					echo '<tr><td>'.$info["Comment"].'</td><td><select name="'.$info["Field"].'">';
					foreach($arrenum as $enumvalue){
						if($info7[$info["Field"]]==$enumvalue){
							echo '<option value="'.$enumvalue.'" selected>'.$enumvalue.'</option>';
						}else{
							echo '<option value="'.$enumvalue.'">'.$enumvalue.'</option>';
						}
					}	
					echo '</select>'.$strNotNullTag.'</td></tr>';
				}else{
					echo '<tr><td>'.$info["Comment"].'</td><td><select name="'.$info["Field"].'">';
					foreach($arrenum as $enumvalue){
						echo '<option value="'.$enumvalue.'">'.$enumvalue.'</option>';
					}	
					echo '</select>'.$strNotNullTag.'</td></tr>';
				}
				break;
			case strstr($info["Type"],'int'):
				//number 类型在IE中可能无法兼容，但是不影响使用；
				if($afr7==1){
					echo '<tr><td>'.$info["Comment"].'</td><td><input name="'.$info["Field"].'" type="number" value="'.$info7[$info["Field"]].'" '.$strNotNull.'>'.$strNotNullTag.'</td></tr>';
				}else{
					echo '<tr><td>'.$info["Comment"].'</td><td><input name="'.$info["Field"].'" type="number" '.$strNotNull.'>'.$strNotNullTag.'</td></tr>';
				}
				break;
			case strstr($info["Type"],'decimal'):
			case strstr($info["Type"],'float'):
			case strstr($info["Type"],'double'):
			case strstr($info["Type"],'real'):
				//number 类型在IE中可能无法兼容，但是不影响使用；
				if($afr7==1){
					echo '<tr><td>'.$info["Comment"].'</td><td><input name="'.$info["Field"].'" type="number" step="0.01" value="'.$info7[$info["Field"]].'" '.$strNotNull.'>'.strNotNullTag.'</td></tr>';
				}else{
					echo '<tr><td>'.$info["Comment"].'</td><td><input name="'.$info["Field"].'" type="number" step="0.01" '.$strNotNull.'>'.$strNotNullTag.'</td></tr>';
				}
				break;
			default:
				if($afr7==1){
					echo '<tr><td>'.$info["Comment"].'</td><td><input name="'.$info["Field"].'" type="text" value="'.$info7[$info["Field"]].'" '.$strNotNull.'>'.$strNotNullTag.'</td></tr>';
				}else{
					echo '<tr><td>'.$info["Comment"].'</td><td><input name="'.$info["Field"].'" type="text" '.$strNotNull.'>'.$strNotNullTag.'</td></tr>';
				}
				break;
		}
	}
	$sql->free();
	if($afr7==1){
		$sql7->free();
	}
}

/*
系统函数：查询mysql表的sql信息，输出到html页面tree结构信息，sql数据id层级使用“-”连接；
调用示例：tblToTree(sql语句,展示与收缩控制true/false,是否带勾选控件true/false);
样式举例：
php：	echo '<div id="mytree_DIV">';
		tblToTree("select id,name from kt order by id",true,false);
		echo '</div>';

js:		<script type="text/javascript">
		$(function(){
			//样式及通用动作调用；
			$("#mytree_DIV").mytree();
			//单击<li></li>分类动作；
			$(".mytree li").on("click",function(){
				if($(this).next("ul").length==0){
					alert($(this).children("input").val() + '-' + $(this).text());
				}
			});
		}); 
		</script>
*/
function tblToTree($strSQL,$blnALL,$blnCHK){
	global $mysqli;
	$sql=$mysqli->query($strSQL);
	if($blnALL){
		echo '<input class="zk" type="button" value="全部展开"/><input class="ss" type="button" value="全部收缩"/>';
	}
	$i=0;$j=0;
	if($blnCHK){
		echo '<input class="xq" type="button" value="全部选取"/><input class="qx" type="button" value="全部取消"/>';
		echo '<ul class="mytree">';
		while($info=$sql->fetch_array(MYSQLI_BOTH)){
			$i=substr_count($info[0],'-');
			if($i>$j){
				$tempM=$i-$j;//此处计算出有多少个开始ul起点；
				for($tempN=1;$tempN<=$tempM;$tempN++){
					echo '<ul>';
				}
				echo '<li><input name=treecheck[] type="checkbox" value="'.$info[0].'" />'.$info[1].'</li>';
			}else if($i<$j){
				$tempI=$j-$i;//此处计算出有多少个封闭ul回路；
				for($tempJ=1;$tempJ<=$tempI;$tempJ++){
					echo '</ul>';
				}
				echo '<li><input name=treecheck[] type="checkbox" value="'.$info[0].'" />'.$info[1].'</li>';
			}else{
				echo '<li><input name=treecheck[] type="checkbox" value="'.$info[0].'" />'.$info[1].'</li>';
			}
			$j=substr_count($info[0],'-');
		}
		echo '</ul>';
	}else{
		echo '<ul class="mytree">';
		while($info=$sql->fetch_array(MYSQLI_BOTH)){
			$i=substr_count($info[0],'-');
			if($i>$j){
				$tempM=$i-$j;//此处计算出有多少个开始ul起点；
				for($tempN=1;$tempN<=$tempM;$tempN++){
					echo '<ul>';
				}
				echo '<li><input type="hidden" value="'.$info[0].'" />'.$info[1].'</li>';
			}else if($i<$j){
				$tempI=$j-$i;//此处计算出有多少个封闭ul回路；
				for($tempJ=1;$tempJ<=$tempI;$tempJ++){
					echo '</ul>';
				}
				echo '<li><input type="hidden" value="'.$info[0].'" />'.$info[1].'</li>';
			}else{
				echo '<li><input type="hidden" value="'.$info[0].'" />'.$info[1].'</li>';
			}
			$j=substr_count($info[0],'-');
		}
		echo '</ul>';
	}
}

//请求系统默认类文件，分页文件；
require_once('config/class/Page.php');

//===================以下代码为会计软件通用公共代码部分===================
//获取公司名称信息（会话变量）：$_SESSION["strCompanyName"]
$strsqlCompanyName='select namef from info where idf=1';
$sqlCompanyName=$mysqli->query($strsqlCompanyName);
$infoCompanyName=$sqlCompanyName->fetch_array(MYSQLI_BOTH);
$_SESSION["strCompanyName"]=$infoCompanyName["namef"];
$arrPublicVar["strCompanyName"]=$_SESSION["strCompanyName"];
$sqlCompanyName->free();

//会计日期问题，获取记账凭证中的最大日期，然后运用该日期与主机日期对比；
$sqlDate=$mysqli->query('SELECT max(lrrq) as lrrq FROM v2');
$infoDate=$sqlDate->fetch_array(MYSQLI_BOTH);
$cDate=$infoDate["lrrq"];
$first_day = date("Y-m-01",strtotime($cDate));
$next_month_last_day=date("Y-m-d",strtotime("$first_day +2 month -1 day"));
//each $next_month_last_day.'</br>';
if($arrPublicVar["currentDate"]>$next_month_last_day) $arrPublicVar["currentDate"]=$next_month_last_day;

//定义会计科目、基础资料服务器验证规则；
/*
//验证名称($name变量)是否包含匹配字符；
foreach($fieldValidation as $yz){
	if(substr_count($name,$yz)!=0){
		echo '验证失败；原因：名称中不能出现 '.$yz.' 字符';
		exit();
	}
}
*/
$fieldValidation=array("'","\"","→","|","@",",",";","$","echo","all");

/*
中文项目名称对应的英文表名称，
当向页面请示 $_GET["tbl"] 时，自动生成系统全局变量：$tblName
进行转换时先验证是否指定了$_GET["tbl"]变量；
*/
if(isset($_GET["tbl"])){
	switch($_GET["tbl"]){
		case "现金流量":
			$tblName="jc_xjll";
			break;
		case "客户":
			$tblName="jc_kh";
			break;
		case "个人":
			$tblName="jc_gr";
			break;
		case "存货":
			$tblName="jc_ch";
			break;	
		case "供应商":
			$tblName="jc_gys";
			break;
		case "部门":
			$tblName="jc_bm";
			break;
		case "核算项目":
			$tblName="jc_xm";
			break;
		default:
			$tblName="";
	}
}

//类：提取会计期间；
//调用该类的方法后，获取数据库中相应会计期间的数组；
class QJ{
	public $arrayQj;
	public function getQj($strSql){
		global $mysqli;
		$this->arrayQj=array();
		$sqlQj=$mysqli->query($strSql);
		while($infoQj=$sqlQj->fetch_array(MYSQLI_BOTH)){
			array_push($this->arrayQj,$infoQj["qj"]);
		};
	}
}

//print_r($arrPublicVar);//系统全局变量打印测试；

?>