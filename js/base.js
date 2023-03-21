$(document).ready(function(){
	//框架检测，脱离框架，程序将中止；
	if($("#header div a").length!=7) return;
	if($("#leftHand #leftHand2").length!=1) return;
	if($("#footer #footerinformation").length!=1) return;
	//加载登录信息;
	$("#footerinformation").load("config/sys_getLogin.php");
	//自动换肤功能；
	$("#footerskinselect").on("change", function(){
		$("#myCssFile").prop("href","css/"+$(this).val()+".css");
		$.cookie("myCssSkin", $(this).val(), { expires: 3650 });
	})
	if($.cookie("myCssSkin")){
		$("#myCssFile").prop("href","css/"+$.cookie("myCssSkin")+".css");
		$("#footerskinselect option[value=" + $.cookie("myCssSkin") + "]").prop("selected", "selected");
	}
	//布局内容窗口随可见页面大小而改变，在IE中测试正常，但在360中需要刷新。
	$(window).resize(function(){	
		if($("#header").is(":visible")){
			var $contentHeight=$(window).height()-$("#header").height()-$("#footer").height();
		}else{
			var $contentHeight=$(window).height();
		}
		//leftHand下的leftHand1宽度始终相同；
		$("#leftHand1").width($("#leftHand").width());
		//中间显示部分高度的设置；
		$("#leftHand,#splitLine,#rightHand").height($contentHeight);
		//组件的定位；
		$("#leftHand1,#tabMenu,.access1").css("position","absolute");
	}).trigger("resize");
	//左侧功能选项（树型菜单控件）全部展示与隐藏;
	$("#leftHand1>img").on("click",function(){
		if($(this).attr("src")=="picture/replay1.png"){
			$("#mymenu ul").show().prev("li").children("img").prop("src","picture/pluschild.png");
			$(this).attr({"src":"picture/replay2.png","title":"全部收缩"});
		}else{
			$("#mymenu ul").hide().prev("li").children("img").prop("src","picture/plus.png");
			$(this).attr({"src":"picture/replay1.png","title":"全部展开"});
		}
	})
	//菜单整体显示与隐藏图标的切换，以及单击事件；
	$("#splitLine>img").on("click",function(){
		var $leftHand=$("#leftHand");
		if($leftHand.is(":visible")){
			$leftHand.hide();
			$(this).attr("title","显示菜单").parent().css({"cursor":"default"});
		}else{
			$leftHand.show();
			$(this).attr("title","隐藏菜单").parent().css({"cursor":"w-resize"});
		}
	})
	//分割页面DIV拖放效果；
	var $disX=0;
	$("#splitLine").mousedown(function(ev){
		$disX=ev.pageX-$(this).offset().left;
		$(document).mousemove(function(ev){
			if($("#leftHand").is(":visible")){
				$disY=(ev.pageX-$disX>0)?ev.pageX-$disX:0;
				$("#splitLine").css("left",$disY).prev().css("width",$disY);
				$("#leftHand1").css("width",$disY-17);
			}
		}).mouseup(function(){
			$(document).off();
		})
		return false;
	})
	//控制面板切换，顶部和底部显示栏关闭或显示。
	$("#tabMenu").on("click",function(){
		if($("#header").is(":visible")){
			var $contentHeight=$(window).height();
			$(this).attr("title","单击显示主控面板");
			$("#header,#leftHand,#footer").hide();
		}else{
			var $contentHeight=$(window).height()-$("#header").height()-$("#footer").height();
			$(this).attr("title","单击隐藏主控面板");
			$("#header,#leftHand,#footer").show();
		}
		$("#contents,#leftHand,#splitLine,#rightHand").height($contentHeight);
		return false;
	});
	//内容窗体滚动条位置记录；
	$("#rightHand").on("scroll",function(){
		//将滚动条位置信息存储在客户端；
		$.cookie($("#splitLineMenu").text(),$(this).scrollTop(),{expires:3650});
	});
	//选项卡的切换以及关闭；
	$("#tabMenu").on("click","li",function(){
		//选项卡切换前先检查是否存在下拉提示
		if($("#suggestbox").css("display")!="none"){
			alert('请先退出下拉提示框！');
		}else{
			$("#splitLineMenu").text($(this).text());
			$(this).siblings().css({ "backgroundColor": "#D0D0D0", "color": "black", "font-weight": "normal" }).end().css({ "backgroundColor": "#B8B8B8", "color": "black", "font-weight":"bold"});
			$("#rightHand>div").hide().eq($(this).index()).show();
			$("#rightHand").scrollTop($.cookie($(this).text()));//滚动条定位到前端值；
		}
		return false;
	}).on("click","li>div",function(){
		//选项卡关闭前先检查是否存在下拉提示
		if($("#suggestbox").is(":visible")){
			alert('请先退出下拉提示框！');
		}else{
			var $index=$(this).parent().index();
			$("#tabMenu>li").eq($index).remove();
			$("#rightHand>div").eq($index).remove();
			$.cookie($("#splitLineMenu").text(),null,{expires:3650});//关闭后清空客户端值
			if($.trim($("#splitLineMenu").text())==$.trim($(this).parent().text())){
				if($index == 0){
					$("#tabMenu>li").eq(0).click();
					$("#splitLineMenu").text($("#tabMenu>li").eq(0).text());
				}else{
					$("#tabMenu>li").eq($index-1).click();
					$("#splitLineMenu").text($("#tabMenu>li").eq($index-1).text());
				}
				$("#rightHand").scrollTop($.cookie($("#splitLineMenu").text()));//滚动条定位到前端值；
			}
			//当活动li选项卡全部关闭，并且左侧菜单隐藏时自动展表左侧菜单；
			if($("#tabMenu>li").length==0){
				if($("#leftHand").is(":hidden")){
					$("#leftHand").show();
				}
			}
		}
		return false;
	});
	//登录窗口的显示；
	$("#login").on("click",function(){
		//检查是否登录，未登录情况下先清除所有选项卡元素；
		if($("#footerinformation").text()=='未登录'){
			$("#tabMenu>li").remove();
			$("#rightHand>div").remove();
		}
		$("#popbox").popup({title:"软件登录:",width:"350",height:"250"}).load("login.php");
		return false;
	});
	//安全退出；
	$("#safequita").on("click",function(){
		if(confirm('您确信要退出系统吗？')){	
			$.get("config/sys_quit.php",function(data,textStatus){
				$("#footerinformation").load("config/sys_getLogin.php");
				alert(data);
				return false;
			});
		}
	});
	//关闭控件的自动填充功能；
	//$("input:not([autocomplete]),textarea:not([autocomplete]),select:not([autocomplete])").attr("autocomplete","off");
	$("input,textarea,select").attr("autocomplete","off");
	//数据库管理；
	$("#mydatabase").on("click",function(){	
		$("#popbox").popup({title:"数据库创建、备份、还原、修改、删除管理：",width:"850",height:"525"}).load("config/database.php");
		return false;
	});
	//打开PhpMyAdmin；
	$("#myphpadmin").on("click",function(){	
		if(window.location.href.indexOf("127.0.0.1")==-1){
			alert("本功能只能在服务器上打开！");
			return false;
		}
	});
	/*插件：应用于页面元素的单击事件中，用于创建选项卡标签并打开对应的页面；
	  该插件需要指定选项卡标签名称和对应的页面文件，如果页面文件未指定，则加载href属性值；
	  如果是加载href属性，注意在单击事件的结尾加上：return false; 防止跳转；
	  1、$(this).mytabs({strTitle:"科目明细账",strUrl:""});
	  2、$(this).mytabs({strTitle:"会计凭证",strUrl:"templates/V_1.php"});
	  3、$(this).mytabs({strTitle:"会计凭证",strUrl:url});
	  该功能在“序时账簿”、“科目账表”、“项目账表”中应用比较多，可实现页面跳转或者刷新指定页面功能；
	*/
	;(function($){
		$.fn.extend({
			"mytabs":function(options){
				//设置默认值，strTitle：选项卡名称，strUrl：选项卡页面地址；
				options=$.extend({
					strTitle:"未指定名称",
					strUrl:""
				},options);
				if(options.strUrl==""){
					var strURL=encodeURI($(this).attr("href"));
				}else{
					var strURL=encodeURI(options.strUrl);
				}
				if($("#tabMenu>li:contains("+options.strTitle+")").length){
					var $index=$($("#tabMenu>li:contains("+options.strTitle+")")).index();
					$("#splitLineMenu").text(options.strTitle);
					$("#tabMenu>li").css({"backgroundColor":"#D0D0D0","color":"black","font-weight":"normal"});
					$("#tabMenu>li:contains("+options.strTitle+")").css({"backgroundColor":"#B8B8B8","color":"black","font-weight":"bold"});
					$("#rightHand>div").hide().eq($index).load(strURL).show();	
				}else{
					if($("#tabMenu>li").length<7){
						//当前活动内容显示界面index;
						var $index=$(this).parents("#rightHand>div").index()-1;
						$("#splitLineMenu").text(options.strTitle);
						$("#tabMenu>li").css({"backgroundColor":"#D0D0D0","color":"black","font-weight":"normal"}).eq($index).after('<li style="backgroundColor:#B8B8B8;color:black;font-weight:bold" title="'+options.strTitle+'"><img src="picture/plusli.png"> '+options.strTitle+'&nbsp<div></div></li>');
						$("#rightHand>div").css("margin-top","40px").eq($index).after('<div></div>');
						$("#rightHand>div:empty").load(strURL).css("marginLeft","5px").siblings("div").hide();
					}else{
						alert("选项卡最大显示7项！");
					}
				}
				return this;
			}
		});
	})(jQuery);
	/*插件；树型导航；
	  调用示例：
		//ul 元素需要写入 class="mytree"
		//样式及通用动作调用；
		$("#mytree_DIV").mytree();
		//单击<li></li>分类动作；
		$(".mytree li").on("click",function(){
			if($(this).next("ul").length==0){
				alert($(this).text());
			}
		});
        关于树型元素的生成，可以参照 config.inc.php 文件中的 tblToTree 函数；
	*/
	;(function($){
		$.fn.extend({
			"mytree":function(){
				//包含.mytree的DIV元素样式；
				$(this).css({"margin":"0","float":"left","height":"auto"});
				//包含.mytree的树型元素样式及动作；
				$(this).find(".mytree ul").hide();
				$(this).find(".mytree ul").prev("li").prepend('<img src="picture/tree1.png"><img src="picture/tree3.png">');
				$(this).find(".mytree li:not(:has(img))").prepend('<img src="picture/tree5.png">');
				$(this).find(".mytree li").on("click",function(){
					if($(this).next("ul").length>0){//含有子菜单；
						if($(this).next("ul").is(":visible")){//子菜单可见；
							$(this).children("img").eq(0).attr("src","picture/tree1.png");
							$(this).children("img").eq(1).attr("src","picture/tree3.png");
							$(this).next("ul").hide();
						}else{
							$(this).children("img").eq(0).attr("src","picture/tree2.png");
							$(this).children("img").eq(1).attr("src","picture/tree4.png");
							$(this).next("ul").show();
						}
					}
				});
				//树型控件下的选项控件勾选；
				$(this).find(".mytree li").on("click","input",function(event){
					if($(this).parent("li").next("ul").length!=0){
						if($(this).prop("checked")==true){
							$(this).parent("li").next("ul").find("input[type=checkbox]").prop("checked",true);
						}else{
							$(this).parent("li").next("ul").find("input[type=checkbox]").prop("checked",false);
						}
					}
					event.stopPropagation();
				});
				//树型控件的通用操作按钮：全部展开；
				$(this).on("click",".zk",function(){
					$(this).parent("div").find(".mytree ul").show();
				});
				//树型控件的通用操作按钮：全部收缩；
				$(this).on("click",".ss",function(){
					$(this).parent("div").find(".mytree ul").hide();
				});
				//树型控件的通用操作按钮：全部选取；
				$(this).on("click",".xq",function(){
					$(this).parent("div").find(".mytree li input[type=checkbox]").prop("checked",true);
				});
				//树型控件的通用操作按钮：全部取消；
				$(this).on("click",".qx",function(){
					$(this).parent("div").find(".mytree li input[type=checkbox]").prop("checked",false);
				});
				return this;
			}
		});
	})(jQuery);
	/*插件：通用表格样式，interlacedColor、隔行换色，selectedColor、选择高亮，clickedColor、单击变色，columnWidth、设置单元格宽度。
	  调用默认效果示例(简洁代码)：$("#mytable").mytable();
	  调用自定义效果示例(详细代码)：$("#mytable").mytable({interlacedColor:false,selectedColor:fasle,clickedColor:false,columnWidth:false});
	  注意此时的表严格区分：表头thead和表体tbody；
	*/
	;(function($){
		$.fn.extend({
			"mytable":function(options){
				//设置默认值，interlacedColor:隔行换色,selectedColor:选择高亮,clickedColor:单击变色,columnWidth:设置单元格宽度。
				options=$.extend({
					interlacedColor:true,
					selectedColor:true,
					clickedColor:true,
					columnWidth:true,
					strTableId:""
				},options);
				if(!$(this).hasClass("mytable")){
					$(this).addClass("mytable");
				}
				//隔行换色；
				if(options.interlacedColor){
					$(this).find("tbody>tr:odd").addClass("odd");
					$(this).find("tbody>tr:even").addClass("even");
				}
				//选择高亮；
				if(options.selectedColor){
					$(this).find("tbody>tr").on("click","input:checkbox",function(e){
						if($(this).is(":checked")){
							$(this).parents("tr").addClass("selected");
						}else{
							$(this).parents("tr").removeClass("selected");
						}
						e.stopPropagation();
					});
				}
				//单击变色；
				if(options.clickedColor){
					$(this).find("tbody>tr").on("click",function(e){
						$(this).siblings().removeClass("clicked");
						if($(this).hasClass("clicked")){
							$(this).removeClass("clicked");
						}else{
							$(this).addClass("clicked");
						}
						e.stopPropagation();
					});
				}
				//设置单元格宽度；
				if(options.columnWidth){
					options.strTableId=$(this).attr("id");//先记录下表格id信息；
					$(this).addClass("tableFixed").find("th,td").addClass("tdEllipsis");
					$(this).find("thead>tr").css("cursor","pointer").attr("title","按下鼠标右键设置表格宽度");
					$(this).find("thead>tr").on("mousedown",function(e){
						if(e.which==3){//按下鼠标右键；
							var $strObject="";
							$(this).children("th,td").each(function(){
								$strObject+='<tr><td>'+$(this).text()+':</td><td><input type="text" value="'+Math.round($(this).width())+'"></input><td></tr>';
							});
							$strObject='<table id="setTableCol">'+$strObject+'<tr><td colspan="2" align="center"><input id="saveTableCol" type="button" value="保存"></input></td></tr></table>';
							$("#popbox").popup({title:"调整表格宽度:",width:"350",height:"380"}).html($strObject);
							//表格列宽保存至Cookie中；
							$("#saveTableCol").on("click",function(){
								var $strTableCol="";
								$("#setTableCol :text").each(function(){
									$strTableCol+=$(this).val()+'|';
								});
								$.cookie(options.strTableId,$strTableCol,{expires:3650});
								alert('保存成功！');
								$("div.popupclose").click();//自动退出弹出窗体，同时解除鼠标右键的绑定；
							});
							$("body").bind("contextmenu", function() {
								return false;
							});
						}
					});
					//取出Cookie中的值；
					if($.cookie(options.strTableId)!=null){
						if($("#"+options.strTableId).length>0){
							var $tableWidth=0;//记录表格总长度变量；
							var arrtd=$.cookie(options.strTableId).split('|');
							for(var i=0;i<arrtd.length;i++){
								$tableWidth=float_add($tableWidth,parseFloat(arrtd[i]));
								$("#"+options.strTableId).find("thead th,thead td").eq(i).width(arrtd[i]);
							}
							$("#"+options.strTableId).css("width",$tableWidth);
						}
					}
				}
				return this;
			}
		});
	})(jQuery);
	/*插件；弹出窗体；
		调用示例：
		$("#popbox").popup({title:"标题title:",width:"350",height:"380"}).load("templates/文件名称.php");
	*/
	;(function($){
		$.fn.extend({
			"popup":function(options){
				//设置默认值，弹出窗口的标题，宽度(不指定时的默认值：400)和高度(不指定时的默认值：300)；
				options=$.extend({
					title:"提示",
					width:"400",
					height:"300"
				},options);
				$(this).append('<div class="popupbg"></div><div class="popup"><div class="popuptitle"></div><div class="popupclose"></div><div class="popupbox"></div></div>');
				$(this).children("div.popupbg").css({"display":"block","width":$(document).width(),"height":$(document).height()});
				$(this).children("div.popup").css("left",($(window).width()-options.width)/2)
											 .css("top",($(window).height()-options.height)/2-30)
											 .css("width",options.width)
											 .css("height",options.height)
											 .css("display","block");
				//关闭弹出DIV界面
				$(this).on("click","div.popupclose",function(){
					$("body").unbind("contextmenu");//解除鼠标右键的事件绑定，与设置表格列宽配合使用；
					if($("#suggestbox").css("display")!="none"){//关闭前先检查是否存在下拉提示
						$("#suggestbox").css("display","none");
					}else{
						$(this).parent().prev().remove().end().remove();
					}
				});
				//弹出DIV支持拖拽功能
				var $div = $(this).find(".popuptitle");
				$div.html('<img src="picture/star.png">'+options.title);
				$div.children("img").css({"marginLeft":"4px","marginRight":"4px"});
				//绑定鼠标左键按住事件
				$div.on("mousedown",function(event){
					if($("#suggestbox").css("display")!="none"){//拖动前先检查是否存在下拉提示
						alert('下拉提示框存在，不允许拖动！');
					}else{
						//获取需要拖动节点的坐标
						var offset_x = $div.parent()[0].offsetLeft;//x坐标
						var offset_y = $div.parent()[0].offsetTop;//y坐标
						//获取当前鼠标的坐标
						var mouse_x = event.pageX;
						var mouse_y = event.pageY;
						//绑定拖动事件
						//由于拖动时，可能鼠标会移出元素，所以应该使用全局（document）元素
						$(document).on("mousemove",function(ev){
							//计算鼠标移动了的位置
							var _x = ev.pageX - mouse_x;
							var _y = ev.pageY - mouse_y;
							//设置移动后的元素坐标
							var now_x = (offset_x + _x ) + "px";
							var now_y = (offset_y + _y ) + "px";
							//改变目标元素的位置
							$div.parent().css({top:now_y,left:now_x});
						});
					}
				});
				//当鼠标左键松开，接触事件绑定
				$(document).on("mouseup",function(){
					$(this).off("mousemove");
				});
				return $(this).find(".popupbox");
			}
		});
	})(jQuery);
	/*插件；下拉提示，通过在指定文本框中输入字符来搜索数据库，给出下拉数据提示，并返回一组数据单击后单元格的数组进行选择；
		前端调用示例：
		$("textName").getSuggestBox({RequestURI:"templates/findcustomer.php",FindSource:"sales",Callback:function(arrtd){
			$("#inputbox input[name=customerid]").val(arrtd[1]);
			$("#inputbox input[name=customername]").val(arrtd[2]);
			$("#inputbox input[name=saleman]").val(arrtd[3]);
			$("#inputbox input[name=consignee]").val(arrtd[4]);
			$("#inputbox input[name=consigneetel]").val(arrtd[5]);
			$("#inputbox input[name=consigneesite]").val(arrtd[6]);
		}});
		php后台调用示例：
		$findSource=mysqli_real_escape_string($mysqli,$_REQUEST["findSource"]);//后台接收到的查找标记
		$keyword=mysqli_real_escape_string($mysqli,$_REQUEST["keywordSuggestBox"]);//后台接收到的查找关键字
		$strsql="select id,name,mobilePhone from users where name like '%$keyword%'";
	*/
	;(function($){
		$.fn.extend({
			"getSuggestBox":function(options){
				//设置默认值；请求地址，数据源识别标志，选择表格行行号和一个回调函数；
				options=$.extend({
					RequestURI:"",
					FindSource:"",
					SuggestRow:-1,
					Callback: null					
				},options);
				$(this).addClass("suggestBoxStyle");
				$(this).on("input",function(){
					//空值检测
					if($(this).val()==""){
						$("#suggestbox").css("display","none");
						return false;
					}
					//更改提示框样式
					$("#suggestbox").css({"display":"block","left":$(this).offset().left,"top":$(this).offset().top+$(this).height()});
					//检索数据
					$.get(options.RequestURI,{
						findSource:options.FindSource,
						keywordSuggestBox:$(this).val()
					},function(data,textStatus){
						//搜索建议框加载远程数据
						var jsonTable = JSON.parse(data);
						var strTableHtml='<table id="suggestboxtable">';
						if(jsonTable.length>0){
							$.each(jsonTable,function(index,item){
								strTableHtml+='<tr>';
								$.each(item,function(index,subitem){
									strTableHtml+='<td>'+subitem+'</td>';
								});
								strTableHtml+='</tr>';
							});
						}else{
							strTableHtml+='<tr><td>===未搜索到相匹配内容===</td></tr>';
						}
						strTableHtml+='</table>';
						$("#suggestbox").html(strTableHtml);
						$("#suggestboxtable").mytable({interlacedColor:true,selectedColor:true,clickedColor:true,columnWidth:false});
						//鼠标单击选择数据
						$("#suggestbox tr").on("click",function(){
							if($(this).parent().is("thead")){
								alert('单击表格标题无效！');
								return false;
							}						
							// if(options.Callback && $.isFunction(options.Callback)){
								// options.Callback(this);
							// }
							//注释的方法是返回 tr 信息，优化的方法是直接返回一组数组；
							var tdValue=[];
							$(this).children("td").each(function(){
								tdValue.push($(this).text());
							});
							if(tdValue[0]!='===未搜索到相匹配内容==='){
								$("#suggestbox").css("display","none");
								options.Callback(tdValue);
							}
						});
					});
					options.SuggestRow=-1;//行定位初始值；
				});
				$(this).on("keydown",function(event){
					//向上
					if(event.keyCode==38){
						if(options.SuggestRow>0){
							options.SuggestRow--;
							$("#suggestbox>table tr").removeClass("selected").eq(options.SuggestRow).addClass("selected");
							//为什么这里的值是280而不是其他，原因是每个标准表格的行高是28，一共10行。
							if($("#suggestbox>table tr").eq(options.SuggestRow).position().top<=28){
								$("#suggestbox").scrollTop(28*options.SuggestRow-280);
							}
						}
					}
					//向下
					if(event.keyCode==40){
						if(options.SuggestRow<$("#suggestbox").find("tr").length-1){
							options.SuggestRow++;
							$("#suggestbox>table tr").removeClass("selected").eq(options.SuggestRow).addClass("selected");
							if($("#suggestbox>table tr").eq(options.SuggestRow).position().top>280){
								$("#suggestbox").scrollTop(28*options.SuggestRow);
							}
						}
					}
					//回车
					if(event.keyCode==13){
						//回车确认，返回数组；
						var tdValue=[];
						if(options.SuggestRow==-1){
							$("#suggestbox").find("tr").eq(0).children("td").each(function(){
								tdValue.push($(this).text());
							});
						}else{
							$("#suggestbox").find("tr").eq(options.SuggestRow).children("td").each(function(){
								tdValue.push($(this).text());
							});
						};
						$("#suggestbox").css("display","none");
						options.Callback(tdValue);
						return false;
					}
				});
			}
		});
	})(jQuery);
});
//浮点数加法运算
function float_add(arg1,arg2){
    var r1,r2,m;
    try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
    try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
    m=Math.pow(10,Math.max(r1,r2));
    return (arg1*m+arg2*m)/m;
}
//数字去掉千分符
function delcommafy(num){
	num = num.replace(/,/gi,'');
	return num;
}
//货币金额，参数：数字，保留小数位，小数点符号，千分符，四舍五入。
function decimal_format(number, decimals, dec_point, thousands_sep,round_tag) {
	number = (number + '').replace(/[^0-9+-Ee.]/g, '');
	decimals = decimals || 2; //默认保留2位
	dec_point = dec_point || "."; //默认是'.';
	thousands_sep = thousands_sep || ","; //默认是',';
	round_tag = round_tag || "round"; //默认是四舍五入
	var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		s = '',
		toFixedFix = function (n, prec) {
  
			var k = Math.pow(10, prec);
			console.log();
  
			return '' + parseFloat(Math[round_tag](parseFloat((n * k).toFixed(prec*2))).toFixed(prec*2)) / k;
		};
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	var re = /(-?\d+)(\d{3})/;
	while (re.test(s[0])) {
		s[0] = s[0].replace(re, "$1" + sep + "$2");
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);
}