<?php
require("../config.inc.php");
if(!$arrGroup["财务报表"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}

if(($_SERVER['REQUEST_METHOD']=='POST')&&isset($_POST["qj0"])){

	$objQj=new QJ();
	$objQj->getQj('select distinct qj from v2 order by qj desc');
	
	try{
			
		/*
		通过 PhpSpreadsheet 向 excel 报表写入数据。
		在 _db 文件夹可能有如下名称的文件，分别为：report.xlsx 和 report_*_*.xlsx( * 号表示用户id 和 日期时间戳) ,其作用如下：
		report.xlsx 文件是模板文件，用于存放格式和报表公式，report_*_*.xlsx 文件是临时报表文件，不仅存放格式和报表公式，还存放写入的数据。
		请注意把敏感的财务数据存放在服务器的文件中是不安全的，我们在下载完报表文件后，请及时清除 report_*_*.xlsx 文件，以防数据的泄漏。
		关于 PhpSpreadsheet 比较全面的使用方法可以参考：https://phpspreadsheet.readthedocs.io/en/latest/
		*/
		
		//EXCEL报表文件默认的存放地址，临时报表文件名称采用了 reports_会计期间_日期时间戳 的格式。
		$now1 = strtotime(date("Y-m-d H:i:s")); //当前日期时间的时间戳；

		//报表模板文件；
		$fileName0 = $arrPublicVar["filesystems"].'_db/report.xlsx';
		//报表数据文件；
		$fileName1 = $arrPublicVar["filesystems"].'_db/report_'.$_POST["qj0"].'_'.$now1.'.xlsx';
		
		//首先检查报表模板是否存在；
		if(!file_exists($fileName0)){
			exit('提示：未找到报表模板！');
		}
		
		//如果excel报表文件不存在，重新复制模板。
		if(!file_exists($fileName1)){
			copy($fileName0,$fileName1);
		}

		require '../config/class/PhpSpreadsheet/vendor/autoload.php';

		/** Load $fileName1 to a Spreadsheet Object  **/
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileName1);

		$callStartTime = microtime(true);
		
		$spreadsheet->getSheetByName('Sheet1')->setCellValue('A1', '键')
											  ->setCellValue('B1', '值')
											  ->setCellValue('A2', '企业名称')
											  ->setCellValue('B2', $arrPublicVar["strCompanyName"])
											  ->setCellValue('A3', '会计期间')
											  ->setCellValue('B3', $_POST["qj0"])
											  ->setCellValue('A4','操作人员')
											  ->setCellValue('B4',$arrPublicVar["username"])
											  ->setCellValue('A5','表格插件')
											  ->setCellValue('B5','PhpSpreadsheet');
		
		//会计报表取数通用查询字符串；
		$strsql='select km,kma,dc,sum(dr0) as dr0,sum(cr0) as cr0,sum(ds1) as ds1,sum(dr1) as dr1,sum(cs1) as cs1,sum(cr1) as cr1 from kmyrb group by km,kma,dc,t order by km,kma;';
		
		//向 Sheet2 写入上年同期发生数；
		$qj0=mysqli_real_escape_string($mysqli,$_POST["qj0"]);
		$qjs=(substr($qj0,0,4)-1).substr($qj0,-2);
		
		$mysqli->query("call kmyrbs('".$qjs."','".$qjs."','','','1','1','1')");
		$sql=$mysqli->query($strsql);

		$spreadsheet->getSheetByName('Sheet2')->setCellValue('A1','会计科目')
											  ->setCellValue('B1','科目全称')
											  ->setCellValue('C1','方向')
											  ->setCellValue('D1','期初余额')
											  ->setCellValue('E1','借方金额')
											  ->setCellValue('F1','贷方金额')
										      ->setCellValue('G1','期末余额')
											  ->setCellValue('H1','tag:上年同期发生数'.$qjs);

		$i=1;
		
		while($info=$sql->fetch_array(MYSQLI_BOTH)){
			$i++;
			if($info["dc"]=="借方"){
				$qcyr = round($info["dr0"]-$info["cr0"],2);
				$bqdr = round($info["dr1"],2);
				$bqcr = round($info["cr1"],2);
				$qmyr = round($info["dr0"]-$info["cr0"]+$info["dr1"]-$info["cr1"],2);
			}else{
				$qcyr = round($info["cr0"]-$info["dr0"],2);
				$bqdr = round($info["dr1"],2);
				$bqcr = round($info["cr1"],2);
				$qmyr = round($info["cr0"]-$info["dr0"]+$info["cr1"]-$info["dr1"],2);
			}
			$spreadsheet->getSheetByName('Sheet2')->setCellValueExplicit('A'.$i,$info["km"],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
												  ->setCellValue('B'.$i,$info["kma"])
												  ->setCellValue('C'.$i,$info["dc"])
												  ->setCellValue('D'.$i,$qcyr)
												  ->setCellValue('E'.$i,$bqdr)
												  ->setCellValue('F'.$i,$bqcr)
												  ->setCellValue('G'.$i,$qmyr);
			$spreadsheet->getSheetByName('Sheet2')->getStyle('D'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet2')->getStyle('E'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet2')->getStyle('F'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet2')->getStyle('G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		}

		//向 Sheet3 写入上年同期累计数；
		$qj0=mysqli_real_escape_string($mysqli,$_POST["qj0"]);
		$qjs=(substr($qj0,0,4)-1).'01';
		$qje=(substr($qj0,0,4)-1).substr($qj0,-2);
		
		$mysqli->query("call kmyrbs('".$qjs."','".$qje."','','','1','1','1')");
		$sql=$mysqli->query($strsql);

		$spreadsheet->getSheetByName('Sheet3')->setCellValue('A1','会计科目')
											  ->setCellValue('B1','科目全称')
											  ->setCellValue('C1','方向')
											  ->setCellValue('D1','期初余额')
											  ->setCellValue('E1','借方金额')
											  ->setCellValue('F1','贷方金额')
										      ->setCellValue('G1','期末余额')
											  ->setCellValue('H1','tag:上年同期累计数'.$qjs.'-'.$qje);
		$i=1;
		
		while($info=$sql->fetch_array(MYSQLI_BOTH)){
			$i++;
			if($info["dc"]=="借方"){
				$qcyr = round($info["dr0"]-$info["cr0"],2);
				$bqdr = round($info["dr1"],2);
				$bqcr = round($info["cr1"],2);
				$qmyr = round($info["dr0"]-$info["cr0"]+$info["dr1"]-$info["cr1"],2);
			}else{
				$qcyr = round($info["cr0"]-$info["dr0"],2);
				$bqdr = round($info["dr1"],2);
				$bqcr = round($info["cr1"],2);
				$qmyr = round($info["cr0"]-$info["dr0"]+$info["cr1"]-$info["dr1"],2);
			}
			$spreadsheet->getSheetByName('Sheet3')->setCellValueExplicit('A'.$i,$info["km"],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
												  ->setCellValue('B'.$i,$info["kma"])
												  ->setCellValue('C'.$i,$info["dc"])
												  ->setCellValue('D'.$i,$qcyr)
												  ->setCellValue('E'.$i,$bqdr)
												  ->setCellValue('F'.$i,$bqcr)
												  ->setCellValue('G'.$i,$qmyr);
			$spreadsheet->getSheetByName('Sheet3')->getStyle('D'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet3')->getStyle('E'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet3')->getStyle('F'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet3')->getStyle('G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		}

		//向 Sheet4 写入当期发生数；
		$qj0=mysqli_real_escape_string($mysqli,$_POST["qj0"]);
		
		$mysqli->query("call kmyrbs('".$qj0."','".$qj0."','','','1','1','1')");
		$sql=$mysqli->query($strsql);

		$spreadsheet->getSheetByName('Sheet4')->setCellValue('A1','会计科目')
											  ->setCellValue('B1','科目全称')
											  ->setCellValue('C1','方向')
											  ->setCellValue('D1','期初余额')
											  ->setCellValue('E1','借方金额')
											  ->setCellValue('F1','贷方金额')
										      ->setCellValue('G1','期末余额')
											  ->setCellValue('H1','tag:当期发生数'.$qj0);

		$i=1;
		
		while($info=$sql->fetch_array(MYSQLI_BOTH)){
			$i++;
			if($info["dc"]=="借方"){
				$qcyr = round($info["dr0"]-$info["cr0"],2);
				$bqdr = round($info["dr1"],2);
				$bqcr = round($info["cr1"],2);
				$qmyr = round($info["dr0"]-$info["cr0"]+$info["dr1"]-$info["cr1"],2);
			}else{
				$qcyr = round($info["cr0"]-$info["dr0"],2);
				$bqdr = round($info["dr1"],2);
				$bqcr = round($info["cr1"],2);
				$qmyr = round($info["cr0"]-$info["dr0"]+$info["cr1"]-$info["dr1"],2);
			}
			$spreadsheet->getSheetByName('Sheet4')->setCellValueExplicit('A'.$i,$info["km"],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
												  ->setCellValue('B'.$i,$info["kma"])
												  ->setCellValue('C'.$i,$info["dc"])
												  ->setCellValue('D'.$i,$qcyr)
												  ->setCellValue('E'.$i,$bqdr)
												  ->setCellValue('F'.$i,$bqcr)
												  ->setCellValue('G'.$i,$qmyr);
			$spreadsheet->getSheetByName('Sheet4')->getStyle('D'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet4')->getStyle('E'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet4')->getStyle('F'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet4')->getStyle('G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		}

		//向 Sheet5 写入当期累计数；
		$qj0=mysqli_real_escape_string($mysqli,$_POST["qj0"]);
		$qjs=substr($qj0,0,4).'01';
		$qje=$qj0;
		
		$mysqli->query("call kmyrbs('".$qjs."','".$qje."','','','1','1','1')");
		$sql=$mysqli->query($strsql);

		$spreadsheet->getSheetByName('Sheet5')->setCellValue('A1','会计科目')
											  ->setCellValue('B1','科目全称')
											  ->setCellValue('C1','方向')
											  ->setCellValue('D1','期初余额')
											  ->setCellValue('E1','借方金额')
											  ->setCellValue('F1','贷方金额')
										      ->setCellValue('G1','期末余额')
											  ->setCellValue('H1','tag:当期累计数'.$qjs.'-'.$qje);
		$i=1;
		
		while($info=$sql->fetch_array(MYSQLI_BOTH)){
			$i++;
			if($info["dc"]=="借方"){
				$qcyr = round($info["dr0"]-$info["cr0"],2);
				$bqdr = round($info["dr1"],2);
				$bqcr = round($info["cr1"],2);
				$qmyr = round($info["dr0"]-$info["cr0"]+$info["dr1"]-$info["cr1"],2);
			}else{
				$qcyr = round($info["cr0"]-$info["dr0"],2);
				$bqdr = round($info["dr1"],2);
				$bqcr = round($info["cr1"],2);
				$qmyr = round($info["cr0"]-$info["dr0"]+$info["cr1"]-$info["dr1"],2);
			}
			$spreadsheet->getSheetByName('Sheet5')->setCellValueExplicit('A'.$i,$info["km"],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING)
												  ->setCellValue('B'.$i,$info["kma"])
												  ->setCellValue('C'.$i,$info["dc"])
												  ->setCellValue('D'.$i,$qcyr)
												  ->setCellValue('E'.$i,$bqdr)
												  ->setCellValue('F'.$i,$bqcr)
												  ->setCellValue('G'.$i,$qmyr);
			$spreadsheet->getSheetByName('Sheet5')->getStyle('D'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet5')->getStyle('E'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet5')->getStyle('F'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$spreadsheet->getSheetByName('Sheet5')->getStyle('G'.$i)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		}

		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$writer->save($fileName1);

		$sql->close();
		
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;
		echo '数据读取共耗时：' , sprintf('%.4f',$callTime) , " 秒<br />";
		
		//最后给出报表下载的地址及清空报表文件，为了数据的安全，一定要记得清空。
		$tempUrl='../templates/access/report_'.$arrPublicVar["userid"].'.xlsx';
		$tempUrlUrl='templates/access/report_'.$arrPublicVar["userid"].'.xlsx';
		copy($fileName1,$tempUrl);
		echo '<a href="'.$tempUrlUrl.'" target="black">报表下载</a>',"    ",
			'<a href="#" id="clear_report1">清空文件</a><强列要求下载结束后进行清空文件操作>';
		
	}catch (Exception $e) {
		die("<font color='red'>-> 提示：程序中止运行，请关闭本页面后重试。<-</font>"); // 终止异常
	}
	
	unset($objQj);
}
?>

<script language="javascript" type="text/javascript">
<!--
$(function(){
	//清空文件
	$("#rightHand>div:visible #clear_report1").click(function(){
		$.post("templates/report1_clear.php",function(data,textStatus){
			alert(data);
		});
		return false;
	});
});
//-->
</script>