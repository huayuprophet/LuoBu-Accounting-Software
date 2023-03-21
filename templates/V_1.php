<?php
require("../config.inc.php");
if(!$arrGroup["会计凭证"]){
	exit('<img src="picture/locked.png" /><font color="red">权限不够，请与管理员联系！</font>');
}
?>

<script language="javascript" type="text/javascript">
<!--
//本页面所有的javascript为纯原生代码，没有加入任何js库；
var intCurrentI_V1;//定义会计凭证中查找会计科目文本框ID序号；
var intMaxTableTr_V1;//定义会计凭证表格新增行最大ID序号，仅累加；
var intCurrentLine_V1;//定义会计科目（核算项目）提示表格上下移动键对应的行号；
var strXmNameId_V1;//定义项目提示的活动文本框ID；
var array1_V1=Array(); //弹出项目DIV中的项目名称（数组形式保存）；
var strKM_V1;//定义向项目搜索时相对应的会计科目代码信息；
var strFX_V1;//定义向项目搜索时相对应的会计科目借贷方向信息；

//会计凭证表格表体行数，表格总行数—表头（业务日期、录入日期、凭证号）—表头（序号、摘要……）—表尾（小计：）
intMaxTableTr_V1=document.getElementById("tbl1_V1").getElementsByTagName("tr").length-3;

//JS创建XMLHttpRequest对象
var request = false;
try {
   request = new XMLHttpRequest();
}
catch (trymicrosoft) {
   try {
	   request = new ActiveXObject("Msxml2.XMLHTTP");
   }
   catch (othermicrosoft) {
	   try {
		   request = new ActiveXObject("Microsoft.XMLHTTP");
	   }
	   catch (failed) {
		   request = false;
	   }
   }
}
if (!request) alert("Error initializing XMLHttpRequest!");

//函数：删除字符串前后空格；
function trim(s){
	s=s.replace(/(^\s*)/,"");//去左空格；
	return s.replace(/(\s*$)/,"");//返回去右空格后的值；
}

function showAccount(txtSearch){
	//会计科目下拉提示Ajax关键代码；
	//当键盘按下Tab(9),Enter(13),↑(38),↓(40)键时，程序中止执行；
	var e=window.event||e||arguments.callee.caller.arguments[0];
	var currKey=e.keyCode;
	if(currKey==9 || currKey==13 || currKey==38 || currKey==40){
		return false;
	}
	
	intCurrentI_V1=(txtSearch.id).slice(7);//从控件的ID中截取控件的序号；
	//alert(intCurrentI_V1);
	intCurrentLine_V1=0;//表格上下移动键对应的行号；
	
	if(document.getElementById(txtSearch.id).value.length>0){
		request.open("GET","templates/V_accountSearch.php?searchText="+encodeURIComponent(document.getElementById(txtSearch.id).value));
		var objdivV_V1=document.getElementById("divV_V1"+intCurrentI_V1);
		request.onreadystatechange=function(){
			if(request.readyState==4){
				if(request.status==200){
					objdivV_V1.style.display="block";
					objdivV_V1.innerHTML="";
					objdivV_V1.innerHTML=request.responseText;
				}
			}
		}
		request.send(null);
	}else{
		document.getElementById("divV_V1"+intCurrentI_V1).style.display="none";
	}		
}

function autozy(txtSearch){
	//把前一行的摘要信息自动填入到后面的表格中；
	intCurrentI_V1=(txtSearch.id).slice(8);//从控件的ID中截取控件的序号；
	if(intCurrentI_V1!=1){
		var intCurrentI_V1_temp=intCurrentI_V1-1;
		var objdivVtemp_V1=document.getElementById("txtZY_V1"+intCurrentI_V1_temp);
		var objdivV_V1=document.getElementById("txtZY_V1"+intCurrentI_V1);
		if(objdivVtemp_V1.value!="" && objdivV_V1.value==""){
			objdivV_V1.value=objdivVtemp_V1.value;
		}
	}
	return false;
}

function showAccountPages(url){
 	//会计科目分页操作Ajax关键代码；
	request.open("GET",url);
	request.onreadystatechange=function(){
		if(request.readyState==4){
			if(request.status==200){
				var objdivV_V1=document.getElementById("divV_V1"+intCurrentI_V1);
				objdivV_V1.style.display="block";
				objdivV_V1.innerHTML="";
				objdivV_V1.innerHTML=request.responseText;
			}
		}
	}
	request.send(null);
}

function showXM(txtSearch){
	//核算项目提示Ajax关键代码；
	//当键盘按下Tab(9),Enter(13),↑(38),↓(40)键时，程序中止执行；
	var e=window.event||e||arguments.callee.caller.arguments[0];
	var currKey=e.keyCode;
	if(currKey==9 || currKey==13 || currKey==38 || currKey==40){
		return false;
	}

	/*
	txtSearch.id 的样式如下：
	客户，供应商，现金流量……
	可以看出，控件根据标记的项目各自命名；
	*/
	intCurrentLine_V1=0;
	strXmNameId_V1=txtSearch.id;
	
	if(document.getElementById(txtSearch.id).value.length>0){
		var strSearch="templates/V_projectSearch.php?searchKM="+encodeURIComponent(strKM_V1)+"&searchFX="+encodeURIComponent(strFX_V1)+"&searchTable="+encodeURIComponent(txtSearch.id)+"&searchText="+encodeURIComponent(document.getElementById(txtSearch.id).value);
		request.open("GET",strSearch);
		var objDivSg=document.getElementById('divsg'+strXmNameId_V1);
		request.onreadystatechange=function(){
			if(request.readyState==4){
				if(request.status==200){
					objDivSg.style.display="block";
					objDivSg.innerHTML="";
					objDivSg.innerHTML=request.responseText;
				}
			}
		}
		request.send(null);
	}else{
		document.getElementById('divsg'+txtSearch.id).style.display="none";
	}	
}

function showXMPages(url){
	//核算项目分页操作Ajax关键代码；
	request.open("GET",url);
	request.onreadystatechange=function(){
		if(request.readyState==4){
			if(request.status==200){
				var objDivSg=document.getElementById('divsg'+strXmNameId_V1);
				objDivSg.style.display="block";
				objDivSg.innerHTML="";
				objDivSg.innerHTML=request.responseText;
			}
		}
	}
	request.send(null);	
}

function trClick(tableTr){
	//根据同行的第三列生成项目数组（是在txtF_V1存在值的情况下操作）；
	var arrinfoF=new Array();
	var arrinfoFsub=new Array();
	var arrinfoXM=new Array();
	arrinfoF=(document.getElementById("txtF_V1"+intCurrentI_V1).value).split("|");
	for(var i=0;i<arrinfoF.length;i++){
		arrinfoFsub=arrinfoF[i].split("→");
		//alert(arrinfoFsub[0]+';'+arrinfoFsub[1]+';'+arrinfoFsub[2]);
		arrinfoXM.push(arrinfoFsub[0]);
		arrinfoXM.push(arrinfoFsub[1]);
		arrinfoXM.push(arrinfoFsub[2]);
	}
	//鼠标单击会计科目提示表格行时动作；
	document.getElementById("txtV_V1"+intCurrentI_V1).value=trim(tableTr.cells[0].innerHTML);
	document.getElementById("txtF_V1"+intCurrentI_V1).value=trim(tableTr.cells[0].innerHTML)+"→"+trim(tableTr.cells[1].innerHTML);
	strKM_V1=trim(tableTr.cells[0].innerHTML);
	strFX_V1=trim(tableTr.cells[3].innerHTML);
	//===========================================弹出核算项目输入界面；===========================================
	var $xm=trim(tableTr.cells[2].innerHTML);
	if($xm!=="空白"){
		with(document.getElementById("bg").style){
			display="block";
			height=document.body.scrollHeight;//网页正文全页高；
		}
		with(document.getElementById("showForm").style){
			display="block";
			top="20%";
			left="25%";
			width="50%";
			height="60%";
		}
		//生成弹出界面元素；
		array1_V1=$xm.split("|");//将会计科目提示表格中所选第三列值生成数组；
		document.getElementById("showFormContent").innerHTML="";
		for(var i=0;i<array1_V1.length;i++){
			//查找字符串是否在数组中，并得出在数组的下标值，然后找出后面两个跟着的数组值；
			var s=arrinfoXM.indexOf(array1_V1[i]);
			if(s==-1){//说明没有找到对应的项目；
				arrinfoXM[s+1]="";
				arrinfoXM[s+2]="";
			}
			if(array1_V1[i]=="数量外币"){
				document.getElementById("showFormContent").innerHTML+='<table><tr><td width="100px">'+array1_V1[i]+'</td>'
					+'<td><input id='+array1_V1[i]+' type="text" value="'+arrinfoXM[s+1]+'" /></td></tr></table>';
			}else{
				document.getElementById("showFormContent").innerHTML+='<table><tr><td width="100px"><b>'+array1_V1[i]+'</b>'
					+'<a href="#" onclick="javascript:jca(\''+array1_V1[i]+'\')">＋</a></td>'
					+'<td><input id='+array1_V1[i]+' type="text" maxlength="25" oninput="showXM(this)" onkeyDown="kd()" value="'+arrinfoXM[s+1]+'" />'
					+'<br /><div id=divsg'+array1_V1[i]+' style="display:none;position:absolute;background:blue;width:auto;overflow:scroll;"></div></td>'
					+'<td width="50%"><div id=divmx'+array1_V1[i]+'>'+arrinfoXM[s+2]+'</div></td></tr></table>';
			}
		}
		document.getElementById("showFormContent").innerHTML+='<br /><input id="save1" type="button" onclick="closeShowForm();" value="确认" />';
		document.getElementById(array1_V1[0]).focus();
	}
	//============================================================================================================
	document.getElementById("divV_V1"+intCurrentI_V1).innerHTML="";
	document.getElementById("divV_V1"+intCurrentI_V1).style.display="none";
	if($xm=="空白"){
		document.getElementById("txtV_V1"+intCurrentI_V1).focus();
	}
}

function jca(strObj){
	var url=encodeURI('templates/jc_Insert.php?tbl='+strObj);
	$("#popbox").popup({title:'新增',width:"600",height:"300"}).load(url);
	return false;
}

function trClickXM(tableTr){
	//鼠标单击核算项目提示表格行时动作；
	var tbl3_V1=document.getElementById("tbl3_V1");//核算项目提示表格；
	if(tbl3_V1!=null){
		document.getElementById(strXmNameId_V1).value=trim(tableTr.cells[0].innerHTML);
		document.getElementById("divmx"+strXmNameId_V1).innerHTML=trim(tableTr.cells[1].innerHTML);
		document.getElementById("divsg"+strXmNameId_V1).innerHTML="";
		document.getElementById("divsg"+strXmNameId_V1).style.display="none";
	}
}

function closeShowFormCancel(){
	//取消项目数据的修改，关闭弹窗；
	document.getElementById("bg").style.display="none";
	document.getElementById("showForm").style.display="none";
	document.getElementById("txtV_V1"+intCurrentI_V1).focus();
}

function closeShowForm(){
	//将项目资料写入会计凭证中的全称中；
    for(var i=0;i<array1_V1.length;i++){
		//检查所有的项目资料是否为空；
		if(document.getElementById(array1_V1[i]).value==""){
			alert("请保持项目资料的完整!");
			document.getElementById(array1_V1[i]).focus();
			return false;
		}
	}
	
	var tbl3_V1=document.getElementById("tbl3_V1");//核算项目提示表格；
	if(tbl3_V1){
		alert("请使用选择器录入项目代码！");
		return false;
	}
	
	//循环构造字符串；
	var strXM;
	for(var i=0;i<array1_V1.length;i++){
		if(strXM==undefined || strXM==""){
			strXM=array1_V1[i]+','+document.getElementById(array1_V1[i]).value;
		}else{
			strXM+=';'+array1_V1[i]+','+document.getElementById(array1_V1[i]).value;
		}
	}
	
	//alert(strXM);
	
	//验证所有的项目资料代码是否均存在于项目表中；验证文件为V_projectCheck.php,向该文件传递表名和文本框输入值；
	request.open("GET","templates/V_projectCkeck.php?arrXM="+encodeURIComponent(strXM));
	request.onreadystatechange=function(){
	  if(request.readyState==4){
		  if(request.status==200){
			  //alert(request.responseText);
			  if(trim(request.responseText)=="acc_no"){
				  alert("项目资料填写不正确，请检查！");
			  }else{
				  //通过检查后将项目资料提交到全称中；
				  var strXms="";
				  for(var i=0;i<array1_V1.length;i++){
					  if(array1_V1[i]=="数量外币"){
						  strXms+="|"+array1_V1[i]+"→"+document.getElementById(array1_V1[i]).value;
					  }else{
						  strXms+="|"+array1_V1[i]+"→"+trim(document.getElementById(array1_V1[i]).value)+"→"+document.getElementById("divmx"+array1_V1[i]).innerHTML;
					  }
				  }
				  document.getElementById("txtF_V1"+intCurrentI_V1).value+=strXms;
				  document.getElementById("bg").style.display="none";
				  document.getElementById("showForm").style.display="none";
				  document.getElementById("txtDR_V1"+intCurrentI_V1).focus();
			  }
		  }
	   }
	}
	request.send(null);	
	
}

function kd(){
	//鼠标单击或键盘按下会计科目列表，自动根据会计科目信息加载项算项目信息；
	var tbl2_V1=document.getElementById("tbl2_V1");//会计科目提示表格；
	var tbl3_V1=document.getElementById("tbl3_V1");//核算项目提示表格；
	
	//判断表格控件是否存在；
	if(tbl2_V1!=null){
		var tr=tbl2_V1.getElementsByTagName("tr");
	}else if(tbl3_V1!=null){
		var tr=tbl3_V1.getElementsByTagName("tr");
	}
	
	//重新初始化表格所有行底色；
	var rows=tr.length;
	for(var i=0;i<rows;i++){
		if(i%2==0){
			tr[i].style.backgroundColor = "#cceeff";
		}else{
			tr[i].style.backgroundColor = "white";
		}
	}
	
	intCurrentLine_V1=intCurrentLine_V1||0;
	var e=window.event||e||arguments.callee.caller.arguments[0];
	var currKey=e.keyCode;
	if(currKey==38){//键盘按下↑；
		if(intCurrentLine_V1>=2){
			intCurrentLine_V1--;
			tr[intCurrentLine_V1-1].style.background="yellow";
		}else{
			tr[0].style.background="yellow";
		}
		return false;
	}
	if(currKey==40){//键盘按下↓；
		if(intCurrentLine_V1<tr.length-1){
			intCurrentLine_V1++;
		}
		tr[intCurrentLine_V1-1].style.background="yellow";
		return false;
	}
	
	//根据同行的第三列生成项目数组（是在txtF_V1存在值的情况下操作）；
	var arrinfoF=new Array();
	var arrinfoFsub=new Array();
	var arrinfoXM=new Array();
	arrinfoF=(document.getElementById("txtF_V1"+intCurrentI_V1).value).split("|");
	for(var i=0;i<arrinfoF.length;i++){
		arrinfoFsub=arrinfoF[i].split("→");
		//alert(arrinfoFsub[0]+';'+arrinfoFsub[1]+';'+arrinfoFsub[2]);
		arrinfoXM.push(arrinfoFsub[0]);
		arrinfoXM.push(arrinfoFsub[1]);
		arrinfoXM.push(arrinfoFsub[2]);
	}
	
	if(currKey==13){//键盘按下回车；
	    if(tbl2_V1!=null){
			if(intCurrentLine_V1!=0){
				//当通过上下键选择或鼠标单击选择会计科目下拉列表时的情景；
				if(trim(tr[intCurrentLine_V1-1].cells[0].innerHTML).substr(0,4)=='<div'){
				    alert('该行不允许选取；');
					return false;
				}
				
				document.getElementById("txtV_V1"+intCurrentI_V1).value=trim(tr[intCurrentLine_V1-1].cells[0].innerHTML);
				document.getElementById("txtF_V1"+intCurrentI_V1).value=trim(tr[intCurrentLine_V1-1].cells[0].innerHTML)+"→"+trim(tr[intCurrentLine_V1-1].cells[1].innerHTML);
				strKM_V1=trim(tr[intCurrentLine_V1-1].cells[0].innerHTML);
	            strFX_V1=trim(tr[intCurrentLine_V1-1].cells[3].innerHTML);
				//===========================================弹出核算项目输入界面；===========================================
				var $xm=trim(tr[intCurrentLine_V1-1].cells[2].innerHTML);
				if($xm!=="空白"){
					with(document.getElementById("bg").style){
						display="block";
						height=document.body.scrollHeight;//网页正文全页高；
					}
					with(document.getElementById("showForm").style){
						display="block";
						top="20%";
						left="25%";
						width="50%";
						height="60%";
					}
					//生成弹出界面元素；
					array1_V1=$xm.split("|");//将会计科目提示表格中所选第三列值生成数组；
					document.getElementById("showFormContent").innerHTML="";
					for(var i=0;i<array1_V1.length;i++){
						//查找字符串是否在数组中，并得出在数组的下标值，然后找出后面两个跟着的数组值；
						var s=arrinfoXM.indexOf(array1_V1[i]);
						if(s==-1){//说明没有找到对应的项目；
							arrinfoXM[s+1]="";
							arrinfoXM[s+2]="";
						}
						if(array1_V1[i]=="数量外币"){
							document.getElementById("showFormContent").innerHTML+='<table><tr><td width="100px">'+array1_V1[i]+'</td>'
								+'<td><input id='+array1_V1[i]+' type="text" value="'+arrinfoXM[s+1]+'" /></td></tr></table>';
						}else{
							document.getElementById("showFormContent").innerHTML+='<table><tr><td width="100px"><b>'+array1_V1[i]+'</b></a>'
								+'<a href="#" onclick="javascript:jca(\''+array1_V1[i]+'\')">＋</a></td>'
								+'<td><input id='+array1_V1[i]+' type="text" maxlength="25" oninput="showXM(this)" onkeyDown="kd()" value="'+arrinfoXM[s+1]+'" />'
								+'<br /><div id=divsg'+array1_V1[i]+' style="display:none;position:absolute;background:blue;width:auto;overflow:scroll;"></div></td>'
								+'<td width="50%"><div id=divmx'+array1_V1[i]+'>'+arrinfoXM[s+2]+'</div></td></tr></table>';
						}
					}
					document.getElementById("showFormContent").innerHTML+='<br /><input id="save1" type="button" onclick="closeShowForm();" value="确认" />';
					document.getElementById(array1_V1[0]).focus();
				}
				//============================================================================================================
				document.getElementById("divV_V1"+intCurrentI_V1).innerHTML="";
				document.getElementById("divV_V1"+intCurrentI_V1).style.display="none";
				e.returnValue=false;////IE阻止冒泡事件；
				e.preventDefault();
			}else{
				//当没有选择会计科目下拉列表时直接按下了回车键的情况；
				if(trim(tr[0].cells[0].innerHTML).substr(0,4)=='<div'){
				    alert('会计科目不合法；');
					e.returnValue=false;////IE阻止冒泡事件；
					e.preventDefault();
					return false;
				}
				
				document.getElementById("txtV_V1"+intCurrentI_V1).value=trim(tr[0].cells[0].innerHTML);
				document.getElementById("txtF_V1"+intCurrentI_V1).value=trim(tr[0].cells[0].innerHTML)+"→"+trim(tr[0].cells[1].innerHTML);
				strKM_V1=trim(tr[0].cells[0].innerHTML);
	            strFX_V1=trim(tr[0].cells[3].innerHTML);
				//===========================================弹出核算项目输入界面；===========================================
				var $xm=trim(tr[0].cells[2].innerHTML);
				if($xm!=="空白"){
					document.getElementById("bg").style.display="block";
					document.getElementById("bg").style.height=document.body.scrollHeight;//网页正文全页高；
					document.getElementById("showForm").style.display="block";
					document.getElementById("showForm").style.top=document.body.scrollTop+150+"px";//网页被卷去的高+150px；
					//生成弹出界面元素；
					array1_V1=$xm.split("|");//将会计科目提示表格中所选第三列值生成数组
					document.getElementById("showFormContent").innerHTML="";
					for(var i=0;i<array1_V1.length;i++){
						//查找字符串是否在数组中，并得出在数组的下标值，然后找出后面两个跟着的数组值；
						var s=arrinfoXM.indexOf(array1_V1[i]);
						if(s==-1){//说明没有找到对应的项目；
							arrinfoXM[s+1]="";
							arrinfoXM[s+2]="";
						}
						if(array1_V1[i]=="数量外币"){
							document.getElementById("showFormContent").innerHTML+='<table><tr><td width="100px">'+array1_V1[i]+'</td>'
								+'<td><input id='+array1_V1[i]+' type="text" value="'+arrinfoXM[s+1]+'" /></td></tr></table>';
						}else{
							document.getElementById("showFormContent").innerHTML+='<table><tr><td width="100px"><b>'+array1_V1[i]+'</b></a>'
								+'<a href="#" onclick="javascript:jca(\''+array1_V1[i]+'\')">＋</a></td>'
								+'<td><input id='+array1_V1[i]+' type="text" maxlength="25" oninput="showXM(this)" onkeyDown="kd()" value="'+arrinfoXM[s+1]+'" />'
								+'<br /><div id=divsg'+array1_V1[i]+' style="display:none;position:absolute;background:blue;width:auto;overflow:scroll;"></div></td>'
								+'<td width="50%"><div id=divmx'+array1_V1[i]+'>'+arrinfoXM[s+2]+'</div></td></tr></table>';
						}
					}
					document.getElementById("showFormContent").innerHTML+='<br /><input id="save1" type="button" onclick="closeShowForm();" value="确认" />';
					document.getElementById(array1_V1[0]).focus();
				}
				//============================================================================================================
				document.getElementById("divV_V1"+intCurrentI_V1).innerHTML="";
				document.getElementById("divV_V1"+intCurrentI_V1).style.display="none";
				e.returnValue=false;//IE阻止冒泡事件；
				e.preventDefault();
			}
		}else if(tbl3_V1!=null){
			if(intCurrentLine_V1!=0){
				//当通过上下键选择或鼠标单击选择核算项目下拉列表时的情景；
			    if(trim(tr[intCurrentLine_V1-1].cells[0].innerHTML).substr(0,4)=='<div'){
				    alert('该行不允许选取；');
					return false;
				}
				
				document.activeElement.value=trim(tr[intCurrentLine_V1-1].cells[0].innerHTML);
				document.getElementById("divmx"+document.activeElement.id).innerHTML=trim(tr[intCurrentLine_V1-1].cells[1].innerHTML);
				document.getElementById("divsg"+document.activeElement.id).innerHTML="";
				document.getElementById("divsg"+document.activeElement.id).style.display="none";
				e.returnValue=false;//IE阻止冒泡事件；
				e.preventDefault();
			}else{
				//当没有选择核算项目下拉列表时直接按下了回车键的情况；
			    if(trim(tr[0].cells[0].innerHTML).substr(0,4)=='<div'){
				    alert('基础资料代码不合法；');
					return false;
				}
				
				document.activeElement.value=trim(tr[0].cells[0].innerHTML);
				document.getElementById("divmx"+document.activeElement.id).innerHTML=trim(tr[0].cells[1].innerHTML);
				document.getElementById("divsg"+document.activeElement.id).innerHTML="";
				document.getElementById("divsg"+document.activeElement.id).style.display="none";
				e.returnValue=false;//IE阻止冒泡事件；
				e.preventDefault();
			}
		}
	}
	
}

function balance(obj){//按下“F7”键自动找平借贷方；
	var e=window.event||e||arguments.callee.caller.arguments[0];
	var currKey=e.keyCode;
	var dbljg=0;
	//alert(currKey);//118解释为键盘按下”F7“键，187解释为键盘按下”=“键，229为中文状态下的等号键；
	if(currKey==118||currKey==187||currKey==229){
		if((obj.id).slice(0,8)=="txtDR_V1"){
			dbljg=Number(document.getElementById("sumCr_V1").value)-Number(document.getElementById("sumDr_V1").value)+Number(obj.value);
			obj.value=dbljg.toFixed(2);
			sumV(1);//找平后重新计算加总；
		}else{
			dbljg=Number(document.getElementById("sumDr_V1").value)-Number(document.getElementById("sumCr_V1").value)+Number(obj.value);
			obj.value=dbljg.toFixed(2);
			sumV(2);//找平后重新计算加总；
		}
		//e.returnValue=false;//IE阻止冒泡事件；
		e.preventDefault();//阻止默认行为；
		return false;//停止事件冒泡和阻止默认行为共用；
	}
}

function sumV(intDC){//自动加总借贷方和
    var sumV=0;
	if(intDC==1){
		for(var i=0;i<document.getElementsByName("dr[]").length;i++){
			sumV+=Number(document.getElementsByName("dr[]")[i].value);
		}
		document.getElementById("sumDr_V1").value=sumV.toFixed(2);
	}else{
		for(var i=0;i<document.getElementsByName("cr[]").length;i++){
			sumV+=Number(document.getElementsByName("cr[]")[i].value);
		}
		document.getElementById("sumCr_V1").value=sumV.toFixed(2);
	}
}

function insertTr(indexstr){//插入表格行操作；

	if (indexstr==""){
	    alert("请选择相应数据行！");
		form1.tblIndex.focus();
		return false;
	}
	
	if (isNaN(indexstr)){
	    alert("行号必须为数字！");
		form1.tblIndex.focus();
		return false;
	}
	
	//新增的行号必须大于1，如果不是则在第一行新增；
	if (indexstr<1){
		indexstr=1;
	}
    
	//新增的行号必须小于表格的最大行号减2，如果不是则在最后一行新增；
	if(indexstr>document.getElementById("tbl1_V1").getElementsByTagName("tr").length-2){
		indexstr=document.getElementById("tbl1_V1").getElementsByTagName("tr").length-2
	}
	
	indexstr++;
	
	var newTR=document.getElementById("tbl1_V1").insertRow(indexstr);
	var newTD0=newTR.insertCell(0);
	var newTD1=newTR.insertCell(1);
	var newTD2=newTR.insertCell(2);
	var newTD3=newTR.insertCell(3);
	var newTD4=newTR.insertCell(4);
	var newTD5=newTR.insertCell(5);
	
	newTD0.setAttribute("valign","top");
	newTD1.setAttribute("valign","top");
	newTD2.setAttribute("valign","top");
	newTD3.setAttribute("valign","top");
	newTD4.setAttribute("valign","top");
	newTD5.setAttribute("valign","top");
	
	//为凭证表格行单击绑定事件，自动赋值到表格添加或删除控制器上，与函数setRowIndex功效等同；
	newTR.onclick=function(e){
		document.getElementById("tblIndex").value=this.rowIndex-1;
	}
	
	intMaxTableTr_V1++
	
	newTD0.innerHTML='<input id="txtXH_V1'+intMaxTableTr_V1+'" name="xh[]" type="text" disabled="disabled" style="width:40px;height:55px;"/>';
	newTD1.innerHTML='<textarea id="txtZY_V1'+intMaxTableTr_V1+'" name="zy[]" type="text" maxlength="64" style="width:150px;height:55px;word-break:break-all" onfocus="autozy(this)"></textarea></td>';
	newTD2.innerHTML='<td><textarea id="txtV_V1'+intMaxTableTr_V1+'" name="km[]" type="text" maxlength="12" style="width:100px;height:55px" oninput="showAccount(this)" onkeyDown="kd()"></textarea>';
	newTD2.innerHTML=newTD2.innerHTML+'<br /><div id="divV_V1'+intMaxTableTr_V1+'" style="display:none;background:blue;width:auto;overflow:scroll;"></div>';
	newTD3.innerHTML='<textarea id="txtF_V1'+intMaxTableTr_V1+'" name="F[]" type="text" disabled="disabled" style="width:350px;height:55px"></textarea>';
	newTD4.innerHTML='<input id="txtDR_V1'+intMaxTableTr_V1+'" name="dr[]" type="number" onchange="sumV(1)" onkeyDown="balance(this)" style="width:150px;height:55px;text-align:right"/>';
	newTD5.innerHTML='<input id="txtCR_V1'+intMaxTableTr_V1+'" name="cr[]" type="number" onchange="sumV(2)" onkeyDown="balance(this)" style="width:150px;height:55px;text-align:right"/>';
	
	//自动更新序号；
	var xhs=document.getElementsByName("xh[]");
	for(var i=0;i<xhs.length;i++){
		xhs[i].value=i+1;
    }
	
	//自动更新合计；
	sumV(1);
	sumV(2);
	
}

function insertTrNew(){//新增表格行操作；

	var indexstr=document.getElementById("tbl1_V1").getElementsByTagName("tr").length-1;
	
	//alert(indexstr);
	
	var newTR=document.getElementById("tbl1_V1").insertRow(indexstr);
	var newTD0=newTR.insertCell(0);
	var newTD1=newTR.insertCell(1);
	var newTD2=newTR.insertCell(2);
	var newTD3=newTR.insertCell(3);
	var newTD4=newTR.insertCell(4);
	var newTD5=newTR.insertCell(5);
	
	newTD0.setAttribute("valign","top");
	newTD1.setAttribute("valign","top");
	newTD2.setAttribute("valign","top");
	newTD3.setAttribute("valign","top");
	newTD4.setAttribute("valign","top");
	newTD5.setAttribute("valign","top");
	
	//为凭证表格行单击绑定事件，自动赋值到表格添加或删除控制器上，与函数setRowIndex功效等同；
	newTR.onclick=function(e){
		document.getElementById("tblIndex").value=this.rowIndex-1;
	}
	
	intMaxTableTr_V1++;
	
	newTD0.innerHTML='<input id="txtXH_V1'+intMaxTableTr_V1+'" name="xh[]" type="text" disabled="disabled" style="width:40px;height:55px;"/>';
	newTD1.innerHTML='<textarea id="txtZY_V1'+intMaxTableTr_V1+'" name="zy[]" type="text" maxlength="64" style="width:150px;height:55px;word-break:break-all" onfocus="autozy(this)"></textarea></td>';
	newTD2.innerHTML='<td><textarea id="txtV_V1'+intMaxTableTr_V1+'" name="km[]" type="text" maxlength="12" style="width:100px;height:55px" oninput="showAccount(this)" onkeyDown="kd()"></textarea>';
	newTD2.innerHTML=newTD2.innerHTML+'<br /><div id="divV_V1'+intMaxTableTr_V1+'" style="display:none;background:blue;width:auto;overflow:scroll;"></div>';
	newTD3.innerHTML='<textarea id="txtF_V1'+intMaxTableTr_V1+'" name="F[]" type="text" disabled="disabled" style="width:350px;height:55px"></textarea>';
	newTD4.innerHTML='<input id="txtDR_V1'+intMaxTableTr_V1+'" name="dr[]" type="number" onchange="sumV(1)" onkeyDown="balance(this)" style="width:150px;height:55px;text-align:right"/>';
	newTD5.innerHTML='<input id="txtCR_V1'+intMaxTableTr_V1+'" name="cr[]" type="number" onchange="sumV(2)" onkeyDown="balance(this)" style="width:150px;height:55px;text-align:right"/>';
	
	//自动更新序号；
	var xhs=document.getElementsByName("xh[]");
	for(var i=0;i<xhs.length;i++){
		xhs[i].value=i+1;
    }
	
	//自动更新合计；
	sumV(1);
	sumV(2);
	
}

function setRowIndex(obj){
	//为凭证表格行单击绑定事件，自动赋值到表格添加或删除控制器上；
	document.getElementById("tblIndex").value=obj.rowIndex-1;
}

function dropTr(indexstr){//删除表格行操作；
	if (indexstr==""){
	    alert("请选择相应数据行！");
		form1.tblIndex.focus();
		return false;
	}
	
	if (isNaN(indexstr)){
	    alert("行号必须为数字！");
		form1.tblIndex.focus();
		return false;
	}
	
	//删除的行号必须大于1；
	if (indexstr<1){
		alert("行号为负数！");
		form1.tblIndex.focus();
		return false;
	}
    
	//删除的行号不能大于表格行数减2；
	if(indexstr>=document.getElementById("tbl1_V1").getElementsByTagName("tr").length-2){
		alert("行号数过大！");
		form1.tblIndex.focus();
		return false;
	}
	
	//删除行时必须保证表格最少有4行;
	if(document.getElementById("tbl1_V1").getElementsByTagName("tr").length<6){
		alert("凭证表格最低需要保持两行！");
		form1.tblIndex.focus();
		return false;
	}
	
	indexstr++;
	
	document.getElementById("tbl1_V1").deleteRow(indexstr);
	
	//自动更新序号；
	var xhs=document.getElementsByName("xh[]");
	for(var i=0;i<xhs.length;i++){
		xhs[i].value=i+1;
    }
	
	//自动更新合计；
	sumV(1);
	sumV(2);
	
}

function tjpz(){//提交凭证表格
    
	//判断是否脱离框架；
	if(!document.getElementById("rightHand")){
		alert("脱离框架，操作失败！");
		return false;
	}
	
	//判断会计科目提示表格是否存在；
	var tbl2_V1=document.getElementById("tbl2_V1");//会计科目提示表格；
	if(tbl2_V1){
		alert("请使用选择器录入会计科目！");
		return false;
	}
	
    var rqyz=/^(\d{4})-(\d{1,2})-(\d{1,2})$/;
	if(!rqyz.test(document.getElementById("ywrq").value)){
		alert("业务日期格式不正确！");
		document.getElementById("ywrq").focus();
		return false;
	}
	if(!rqyz.test(document.getElementById("lrrq").value)){
		alert("录入日期格式不正确！");
		document.getElementById("lrrq").focus();
		return false;
	}
	if(isNaN(document.getElementById("pzh").value)){
		alert("凭证号必须为数字！");
		document.getElementById("pzh").focus();
		return false;
	}
	if(document.getElementsByName("km[]").item(0).value==""||document.getElementsByName("km[]").item(1).value==""){
		alert("至少保证凭证有两行数据！");
		return false;
	}
	if(parseFloat(document.getElementById("sumDr_V1").value)!=parseFloat(document.getElementById("sumCr_V1").value)){
		alert("借贷方合不相等！");
		return false;
	}
	
	//凭证借贷方不能同时存在数据，同时不存在任何数据或者金额是否不正确；
	for(var i=0;i<document.getElementsByName("km[]").length;i++){
		if(document.getElementsByName("km[]").item(i).value!=""){
			j=i+1;
			if(document.getElementsByName("dr[]").item(i).value==""&&document.getElementsByName("cr[]").item(i).value==""){
				alert("借贷方不能同时为空值，第"+j+"行！");
				return false;
			}
			if(document.getElementsByName("dr[]").item(i).value!=""&&document.getElementsByName("cr[]").item(i).value!=""){
				alert("借贷方不能同时存在数值，第"+j+"行！");
				return false;
			}
			var exp=/(^([-]?)[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^([-]?)(0){1}$)|(^([-]?)[0-9]\.[0-9]([0-9])?$)/; 
			if(document.getElementsByName("dr[]").item(i).value!=""){
				if(!exp.test(document.getElementsByName("dr[]").item(i).value)){
					alert("借方第"+j+"行金额错误！");
					document.getElementsByName("dr[]").item(i).focus();
					return false;
				}
			}
			if(document.getElementsByName("cr[]").item(i).value!=""){
				if(!exp.test(document.getElementsByName("cr[]").item(i).value)){
					alert("贷方第"+j+"行金额错误！");
					document.getElementsByName("cr[]").item(i).focus();
					return false;
				}
			}
		}
	}

	//构造原生JSON数组；
	//该处的凭证号信息是由：原凭证的期间+原凭证号+新凭证号 组成，如果是新增凭证，如该值为：0+0+新凭证号；
    var v1='{"ywrq":"'+document.getElementById("ywrq").value+'","lrrq":"'+document.getElementById("lrrq").value+'","pzh":"'+document.getElementById("oldPz").value+','+document.getElementById("pzh").value+'"}';
	var v2;
	for(var i=0;i<document.getElementsByName("km[]").length;i++){
	    if(document.getElementsByName("km[]").item(i).value!=""){
			if(v2==undefined || v2==""){
			    v2='{"zy":"'+document.getElementsByName("zy[]").item(i).value+'","km":"'+trim(document.getElementsByName("km[]").item(i).value)+'","f":"'+document.getElementsByName("F[]").item(i).value+'","dr":"'+document.getElementsByName("dr[]").item(i).value+'","cr":"'+document.getElementsByName("cr[]").item(i).value+'"}';
			}else{
				v2=v2+',{"zy":"'+document.getElementsByName("zy[]").item(i).value+'","km":"'+trim(document.getElementsByName("km[]").item(i).value)+'","f":"'+document.getElementsByName("F[]").item(i).value+'","dr":"'+document.getElementsByName("dr[]").item(i).value+'","cr":"'+document.getElementsByName("cr[]").item(i).value+'"}';
			}
		}else{
			break;
		}
	}

	v2='['+v2+']';
	v1=encodeURIComponent(v1);
	v2=encodeURIComponent(v2);
	
	//提交数组至服务器；
	var Vbody="v1="+v1+"&v2="+v2;
	request.open("POST","templates/V_save.php");
	request.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	request.send(Vbody);
	
	request.onreadystatechange=function(){
		if(request.readyState==4){
			if(request.status==200){
				if((request.responseText).indexOf('凭证保存成功')!=-1){
					//将后台保证的凭证号写入当前凭证号控件中；
					document.getElementById('pzh').value=(request.responseText).substring(12);
				}
                alert(request.responseText);
			}
		}
	}	
}

function saveMoBan(){
	if(document.getElementsByName("km[]").item(0).value==""||document.getElementsByName("km[]").item(1).value==""){
		alert("至少保证凭证有两行数据！");
		return false;
	}
	//构造原生JSON数组；
	var r=prompt("请输入模板名称:");
	if(r){
		var v1='{"mbname":"'+ r +'"}';
		var v2;
		for(var i=0;i<document.getElementsByName("km[]").length;i++){
			if(document.getElementsByName("km[]").item(i).value!=""){
				if(v2==undefined || v2==""){
					v2='{"zy":"'+document.getElementsByName("zy[]").item(i).value+'","km":"'+trim(document.getElementsByName("km[]").item(i).value)+'","f":"'+document.getElementsByName("F[]").item(i).value+'","dr":"'+document.getElementsByName("dr[]").item(i).value+'","cr":"'+document.getElementsByName("cr[]").item(i).value+'"}';
				}else{
					v2=v2+',{"zy":"'+document.getElementsByName("zy[]").item(i).value+'","km":"'+trim(document.getElementsByName("km[]").item(i).value)+'","f":"'+document.getElementsByName("F[]").item(i).value+'","dr":"'+document.getElementsByName("dr[]").item(i).value+'","cr":"'+document.getElementsByName("cr[]").item(i).value+'"}';
				}
			}else{
				break;
			}
		}

		v2='['+v2+']';
		v1=encodeURIComponent(v1);
		v2=encodeURIComponent(v2);
	
		//提交数组至服务器；
		var Vbody="v1="+v1+"&v2="+v2;
		request.open("POST","templates/V_save_mb.php");
		request.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		request.send(Vbody);
	
		request.onreadystatechange=function(){
			if(request.readyState==4){
				if(request.status==200){
					if(request.responseText=='YES'){
						var objSelect = document.getElementById("selectList");
						objSelect.options.add(new Option(r, r));
						alert("模板新增成功！");
					}else{
						alert(request.responseText);
					}
				}
			}
		}	
	}else{
		alert("未指定模板名称，操作中止！");
	}
}

function useMoBan(){
	//重置当前表格
	document.getElementById("resetV1").click();
	//调用后台数据
	var objSelect = document.getElementById("selectList");
	if(objSelect.value==''){
		alert('请选择相应的模板数据！');
		return;
	}
	var Vbody="v1="+objSelect.value;
	request.open("POST","templates/V_get_mb_list.php");
	request.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	request.send(Vbody);
	
	var j=1;
	request.onreadystatechange=function(){
		if(request.readyState==4){
			if(request.status==200){
				//alert(request.responseText);
				var jsmb = JSON.parse(request.responseText);
				for(var i = 0; i < jsmb.length; i++) {
					//alert(jsmb[i]);
					var strRowValue=jsmb[i].split(',');
					document.getElementById("txtZY_V1"+j).value=strRowValue[0];
					document.getElementById("txtV_V1"+j).value=strRowValue[1];
					document.getElementById("txtF_V1"+j).value=strRowValue[2];
					if(strRowValue[3]==0){
						document.getElementById("txtDR_V1"+j).value=null
					}else{
						document.getElementById("txtDR_V1"+j).value=strRowValue[3];
					}
					if(strRowValue[4]==0){
						document.getElementById("txtCR_V1"+j).value=null;
					}else{
						document.getElementById("txtCR_V1"+j).value=strRowValue[4];
					}
					j++;
				}
				closeShowFormCancel();
			}
		}
	}	
}

function delMoBan(){
	//调用后台数据
	var objSelect = document.getElementById("selectList");
	if(objSelect.value==''){
		alert('请选择相应的模板数据！');
		return;
	}
	var Vbody="v1="+objSelect.value;
	request.open("POST","templates/V_get_mb_del.php");
	request.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	request.send(Vbody);

	request.onreadystatechange=function(){
		if(request.readyState==4){
			if(request.status==200){
				if(request.responseText=='YES'){
					objSelect.options.remove(objSelect.selectedIndex);
					alert('删除模板成功！');
				}else{
					alert('删除模板失败！');
				}
			}
		}
	}
}

function getMoBan(){
	with(document.getElementById("bg").style){
		display="block";
		height=document.body.scrollHeight;//网页正文全页高；
	}
	with(document.getElementById("showForm").style){
		display="block";
		top="15%";
		left="25%";
		width="50%";
		height="60%";
	}
	document.getElementById("showFormContent").innerHTML="";
	//查询后台程序，调取数据库中的数据，在前端写入列表控件；
	//提交数组至服务器；
	var Vbody="v1=mb";
	request.open("POST","templates/V_get_mb.php");
	request.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	request.send(Vbody);
	
	request.onreadystatechange=function(){
		if(request.readyState==4){
			if(request.status==200){
				var strHtml='<select multiple id="selectList" style="width:100%;height:60%;">';
				var jsmb = JSON.parse(request.responseText);
				for(var i = 0; i < jsmb.length; i++) {
					//alert(jsmb[i]);
					strHtml+='<option value="'+jsmb[i]+'">'+jsmb[i]+'</option>';
				}
				strHtml+='</select>';
				strHtml+='</br></br>';
				strHtml+='<input id="useMoBan" type="button" onclick="useMoBan();" value="调用" />&nbsp;';
				strHtml+='<input id="saveMoBan" type="button" onclick="saveMoBan();" value="存储" />&nbsp;';
				strHtml+='<input id="delMoBan" type="button" onclick="delMoBan();" value="删除" />';
				document.getElementById("showFormContent").innerHTML=strHtml
			}
		}
	}	
}

//获取元素
var div_showForm = document.getElementById("showForm");
var div_showFormTitle = document.getElementById("showFormTitle");
var x = 0;
var y = 0;
var l = 0;
var t = 0;
var isDown = false;
//鼠标按下事件
div_showFormTitle.onmousedown = function(e) {
    //获取x坐标和y坐标
    x = e.clientX;
    y = e.clientY;

    //获取左部和顶部的偏移量
    l = div_showForm.offsetLeft;
    t = div_showForm.offsetTop;
    //开关打开
    isDown = true;
    //设置样式  
    div_showFormTitle.style.cursor = 'move';
}
//鼠标移动
window.onmousemove = function(e) {
    if (isDown == false) {
        return;
    }
    //获取x和y
    var nx = e.clientX;
    var ny = e.clientY;
    //计算移动后的左偏移量和顶部的偏移量
    var nl = nx - (x - l);
    var nt = ny - (y - t);

    div_showForm.style.left = nl + 'px';
    div_showForm.style.top = nt + 'px';
}
//鼠标抬起事件
div_showFormTitle.onmouseup = function() {
    //开关关闭
    isDown = false;
    div_showFormTitle.style.cursor = 'default';
}
//凭证附件的管理因与软件框架相关联，所以采用jquery处理
$(function(){
	//打开凭证上的附件；
	$("#rightHand>div:visible #kjpzfj_V_1").click(function(){
		var url=$(this).attr("href");
		$(this).mytabs({strTitle:"附件管理",strUrl:url});
		return false;
	});
	$("#mytable").mytable({interlacedColor:false,selectedColor:false,clickedColor:false,columnWidth:true});
});
//-->
</script>

<?php
if(($_SERVER['REQUEST_METHOD']=='GET')&&isset($_GET["qj"])&&isset($_GET["pzh"])){//修改、复制凭证页面提交的参数；
	$strsql='select qj,ywrq,lrrq,pzh,xh,zy,km,kmF,dr,cr,etr,chk,fj from V1 where qj="'.$_GET["qj"].'" and pzh="'.$_GET["pzh"].'" order by xh asc';
	$sql=$mysqli->query($strsql);
    $info=$sql->fetch_array(MYSQLI_BOTH);
    if($info){ //如果存在数据；
	    $chkV=$info["chk"];//指定凭证是否审核变量；
		
		echo '<form name="form1" action="" method="post" autocomplete="off">';
		echo '<div id="FloatCMD" class="access1">';
		
		//1、前排操作按钮；
		if($_GET["handle"]=='copyV'){//复制凭证(相当于新增一张凭证并保存)；
			//写操作控件，定义隐藏域来记录被复制凭证的期间和凭证号信息,0+0;
			echo '<input id="tj" type="button" style="width:100px" onclick="tjpz()" value="保存复制" />
				  <input type="hidden" id="oldPz" value="0,0" />
				  <input id="tblIndex" type="hidden" />
				  <input id="insertTr1" type="button" onclick="insertTr(this.form.tblIndex.value)" value="插入一行" />
				  <input id="dropTr1" type="button" onclick="dropTr(this.form.tblIndex.value)" value="删除一行" />
				  <input id="insertTrNew1" type="button" style="width:100px" onclick="insertTrNew()" value="新增一行" />
				  <input id="getmoban" type="button" onclick="getMoBan()" value="存取模板" />';
		}else if($chkV==""){//修改凭证（未审核凭证可修改）；
			//写操作控件，定义隐藏域来记录被修改凭证的期间和凭证号信息；
			echo '<input id="tj" type="button" onclick="tjpz()" value="保存修改" />
				  <input type="hidden" id="oldPz" value="'.$_GET["qj"].','.$_GET["pzh"].'" />
				  <input id="tblIndex" type="hidden" />
				  <input id="insertTr1" type="button" onclick="insertTr(this.form.tblIndex.value)" value="插入一行" />
				  <input id="dropTr1" type="button" onclick="dropTr(this.form.tblIndex.value)" value="删除一行" />
				  <input id="insertTrNew1" type="button" onclick="insertTrNew()" value="新增一行" />
				  <input id="getmoban" type="button" onclick="getMoBan()" value="存取模板" />';
		}else{//已审核凭证或者查看凭证；
			echo '只读凭证';
		}
		
		echo '</div>';
		echo '<div align="left" class="access2" style="margin-top:49px">';
		
	    //2、写凭证表头；
		if($_GET["handle"]=='copyV'){//复制凭证；
			echo '<table id="tbl1_V1">
				  <tr>
				  <td colspan="6" style="border:0;" align="center">
				  业务日期：<input id="ywrq" type="date" value="'.$arrPublicVar["currentDate"].'" />
				  录入日期：<input id="lrrq" type="date" value="'.$arrPublicVar["currentDate"].'" />
				  凭证号：<input id="pzh" type="number" />
				  </td>
				  </tr>
				 <tr class="headlines">
				 <td>序号</td>
				 <td>摘要*</td>
				 <td>会计科目*</td>
				 <td>全称</td>
				 <td>借方*</td>
				 <td>贷方*</td>
				 </tr>';
		}else{//修改、查看凭证；
			echo '<table id="tbl1_V1">
				  <tr>
				  <td colspan="6" style="border:0;" align="center">
				  业务日期：<input id="ywrq" type="date" value='.$info["ywrq"].' />
				  录入日期：<input id="lrrq" type="date" value='.$info["lrrq"].' />
				  凭证号：<input id="pzh" type="number" value='.$info["pzh"].' />
				  <a id="kjpzfj_V_1" href="templates/V_upFile.php?qj='.$info["qj"].'&pzh='.$info["pzh"].'" target="black" title="会计凭证附件">附件:'.$info["fj"].'</a>
				  </td>
				  </tr>
				  <tr class="headlines">
				  <td>序号</td>
				  <td>摘要*</td>
				  <td>会计科目*</td>
				  <td>全称</td>
				  <td>借方*</td>
				  <td>贷方*</td>
				  </tr>';
		}
		
		//3、写凭证表体；
		$sumDr_V1=0;
		$sumCr_V1=0;
		DO{
            echo '<tr onclick="setRowIndex(this)">
				  <td><input id="txtXH_V1'.$info["xh"].'" name="xh[]" type="text" disabled="disabled" value='.$info["xh"].' style="width:40px;height:55px;"/></td>
				  <td><textarea id="txtZY_V1'.$info["xh"].'" name="zy[]" type="text" maxlength="64" style="width:150px;height:55px;word-break:break-all" onfocus="autozy(this)">'.$info["zy"].'</textarea></td>
				  <td><textarea id="txtV_V1'.$info["xh"].'" name="km[]" type="text" maxlength="12" style="width:100px;height:55px" oninput="showAccount(this)" ondblclick="showAccount(this)" onkeyDown="kd()">'.$info["km"].'</textarea><br />
				  <div id="divV_V1'.$info["xh"].'" name="vv[]" style="display:none;background:blue;width:auto;overflow:scroll;"></div></td>
				  <td><textarea id="txtF_V1'.$info["xh"].'" name="F[]" type="text" disabled="disabled" style="width:350px;height:55px">'.$info["kmF"].'</textarea></td>
				  <td><input id="txtDR_V1'.$info["xh"].'" name="dr[]" type="number" value="'.($info["dr"]==0?"":$info["dr"]).'" onchange="sumV(1)" onkeyDown="balance(this)" style="width:150px;height:55px;text-align:right"/></td>
				  <td><input id="txtCR_V1'.$info["xh"].'" name="cr[]" type="number" value="'.($info["cr"]==0?"":$info["cr"]).'" onchange="sumV(2)" onkeyDown="balance(this)" style="width:150px;height:55px;text-align:right"/></td>
				  </tr>';
			$sumDr_V1+=$info["dr"];
			$sumCr_V1+=$info["cr"];
		}while($info=$sql->fetch_array(MYSQLI_BOTH));
		
		$sql->free();
		$mysqli->close();
		
		//4、写信息借贷汇总小计；
		echo '<tr>
			  <td colspan="4"><input disabled="disabled" type="text" value="小计：" style="width:100%"></input></td>
			  <td><input id="sumDr_V1" disabled="disabled" type="text" value="'.$sumDr_V1.'" style="width:150px;height:30px;text-align:right"/></td>
			  <td><input id="sumCr_V1" disabled="disabled" type="text" value="'.$sumCr_V1.'" style="width:150px;height:30px;text-align:right"/></td>
			  </tr>
			  </table>
			  </form>';
			
		echo '</div>';
	}else{//如果不存在数据；
		echo '<div class="access1">该凭证不存在，请检查原因！</div>';
	}
}elseif($_SERVER['REQUEST_METHOD']=='POST'){//接收以POST形式传递过来的参数，快速生成凭证会时使用；
	echo '<form name="form1" action="" method="post" autocomplete="off">
		  <div id="FloatCMD" class="access1">
		  <input id="tj" type="button" onclick="tjpz()" value="新增保存" />
		  <input type="hidden" id="oldPz" value="0,0" />
		  <input id="tblIndex" type="text" />
		  <input id="insertTr1" type="button" onclick="insertTr(this.form.tblIndex.value)" value="插入一行" />
		  <input id="dropTr1" type="button" onclick="dropTr(this.form.tblIndex.value)" value="删除一行" />
		  <input id="insertTrNew1" type="button" onclick="insertTrNew()" value="新增一行" />
		  <input id="getmoban" type="button" onclick="getMoBan()" value="存取模板" />
		  </div>
		  <div align="left" class="access2">
		  <table id="tbl1_V1">
		  <tr>
		  <td colspan="6" style="border:0;" align="center">
		  业务日期：<input id="ywrq" type="date" value="'.$arrPublicVar["currentDate"].'" />
		  录入日期：<input id="lrrq" type="date" value="'.$arrPublicVar["currentDate"].'" />
		  凭证号：<input id="pzh" type="number" />
		  </td>
		  </tr>
		  <tr class="headlines">
		  <td>序号</td>
		  <td>摘要*</td>
		  <td>会计科目*</td>
		  <td>全称</td>
		  <td>借方*</td>
		  <td>贷方*</td>
		  </tr>';
	$i=count($_POST[tags]);//记录被选中的复选框个数；
	$n=1;//凭证表体行数标记；
	for($j=0;$j<$i+1;$j++){
		$m=$_POST[tags][$j];
		//echo $_POST[cr][$m-1];
		echo '<tr onclick="setRowIndex(this)">
			  <td><input id="txtXH_V1'.$n.'" name="xh[]" type="text" disabled="disabled" value='.$n.' style="width:40px;height:55px;"/></td>
			  <td><textarea id="txtZY_V1'.$n.'" name="zy[]" type="text" maxlength="64" style="width:150px;height:55px;word-break:break-all" onfocus="autozy(this)">'.$_POST[zy][$m-1].'</textarea></td>
			  <td><textarea id="txtV_V1'.$n.'" name="km[]" type="text" maxlength="12" style="width:100px;height:55px" oninput="showAccount(this)" ondblclick="showAccount(this)" onkeyDown="kd()">'.$_POST[km][$m-1].'</textarea><br />
			  <div id="divV_V1'.$n.'" name="vv[]" style="display:none;background:blue;width:auto;overflow:scroll;"></div></td>
			  <td><textarea id="txtF_V1'.$n.'" name="F[]" type="text" disabled="disabled" style="width:350px;height:55px">'.$_POST[kmF][$m-1].'</textarea></td>
			  <td><input id="txtDR_V1'.$n.'" name="dr[]" type="number" value="'.($_POST[dr][$m-1]==0?"":$_POST[dr][$m-1]).'" onchange="sumV(1)" onkeyDown="balance(this)" style="width:150px;height:55px;text-align:right"/></td>
			  <td><input id="txtCR_V1'.$n.'" name="cr[]" type="number" value="'.($_POST[cr][$m-1]==0?"":$_POST[cr][$m-1]).'" onchange="sumV(2)" onkeyDown="balance(this)" style="width:150px;height:55px;text-align:right"/></td>
			  </tr>';
		$n++;
		$sumDr_V1+=$info[dr];
		$sumCr_V1+=$info[cr];
	}
	echo '<tr>
		  <td colspan="4"><input disabled="disabled" type="text" value="小计：" style="width:100%"></input></td>
		  <td><input id="sumDr_V1" disabled="disabled" type="text" value="'.$sumDr_V1.'" style="width:150px;height:30px;text-align:right"/></td>
		  <td><input id="sumCr_V1" disabled="disabled" type="text" value="'.$sumCr_V1.'" style="width:150px;height:30px;text-align:right"/></td>
		  </tr>
		  </table>
		  </div></form>';
}else{//新增页面凭证表头及表体设置
	echo '<form name="form1" action="" method="post" autocomplete="off">
		<div id="FloatCMD" class="access1">
		<input id="tj" type="button" onclick="tjpz()" value="新增保存" />
		<input type="hidden" id="oldPz" value="0,0" />
		<input id="tblIndex" type="hidden" />
		<input id="insertTr1" type="button" onclick="insertTr(this.form.tblIndex.value)" value="插入一行" />
		<input id="dropTr1" type="button" onclick="dropTr(this.form.tblIndex.value)" value="删除一行" />
		<input id="insertTrNew1" type="button" onclick="insertTrNew()" value="新增一行" />
		<input id="getmoban" type="button" onclick="getMoBan()" value="存取模板" />
		<input id="resetV1" type="reset" value="重置" />
		</div>';
	echo '<div align="left" class="access2">
		<table id="tbl1_V1">
		<tr>
		<td colspan="6" style="border:0;" align="center">
		业务日期：<input id="ywrq" type="date" value="'.$arrPublicVar["currentDate"].'" />
		录入日期：<input id="lrrq" type="date" value="'.$arrPublicVar["currentDate"].'" />
		凭证号：<input id="pzh" type="number" />
		</td>
		</tr>
		<tr class="headlines">
		<td>序号</td>
		<td>摘要*</td>
		<td>会计科目*</td>
		<td>全称</td>
		<td>借方*</td>
		<td>贷方*</td>
		</tr>';

	//采用for循环写出十行表格；
	for($i=1;$i<=100;$i++){
	echo '<tr onclick="setRowIndex(this)">
		  <td><input id="txtXH_V1'.$i.'" name="xh[]" type="text" disabled="disabled" value='.$i.' style="width:40px;height:55px;"/></td>
		  <td><textarea id="txtZY_V1'.$i.'" name="zy[]" type="text" maxlength="64" style="width:150px;height:55px;word-break:break-all" onfocus="autozy(this)"></textarea></td>
		  <td><textarea id="txtV_V1'.$i.'" name="km[]" type="text" maxlength="12" style="width:100px;height:55px" oninput="showAccount(this)" ondblclick="showAccount(this)" onkeyDown="kd()"></textarea>
		  <div id="divV_V1'.$i.'" name="vv[]" style="display:none;background:blue;width:auto;overflow:scroll;"></div></td>
		  <td><textarea id="txtF_V1'.$i.'" name="F[]" type="text" disabled="disabled" style="width:350px;height:55px"></textarea>
		  <td><input id="txtDR_V1'.$i.'" name="dr[]" type="number" onchange="sumV(1)" onkeyDown="balance(this)" style="width:150px;height:55px;text-align:right"/></td>
		  <td><input id="txtCR_V1'.$i.'" name="cr[]" type="number" onchange="sumV(2)" onkeyDown="balance(this)" style="width:150px;height:55px;text-align:right"/></td>
		  </tr>';
	}

	echo '<tr>
		<td colspan="4"><input disabled="disabled" type="text" value="小计：" style="width:100%"></input></td>
		<td><input id="sumDr_V1" disabled="disabled" type="text" value="0" style="width:150px;height:30px;text-align:right"/></td>
		<td><input id="sumCr_V1" disabled="disabled" type="text" value="0" style="width:150px;height:30px;text-align:right"/></td>
		</tr>
		</table>
		</div></form>';
}
?>

<div id="bg"></div>
<div id="showForm">
	<div id="showFormTitle">会计凭证通用弹出窗体（按住鼠标左键可拖拽）<div style="float:right;margin-top:10px" onclick="closeShowFormCancel();"><a><img src="picture/cancel_2.png" /></a></div></div>
	<div id="showFormContent"></div>
</div>