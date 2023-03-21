$(document).ready(function(){
	//左侧树型菜单折叠与展示；
	$("#mymenu ul").hide();
	//菜单的单击事件,菜单无图标设置时自动维护菜单图标；
	$("#mymenu li").on("click",function(){
		if($(this).next("ul").length>0){//含有子菜单；
			if($(this).next("ul").is(":visible")){//子菜单可见；
				$(this).children("img").attr("src","picture/plus.png").end().next("ul").hide();
			}else{
				$(this).children("img").attr("src","picture/pluschild.png").end().next("ul").show();
			}
		}else{
			$(this).children("a").click();//不含有子菜单，直接跳转；
		}
	}).each(function(){
		if($(this).next("ul").length>0){
			if($(this).children("img").length==0) $(this).prepend('<img src="picture/plus.png">');
		}else{
			if($(this).children("img").length==0) $(this).prepend('<img src="picture/pluslili.png">');
		}
	});
	//左侧树型菜单对选项卡的控制；
	$("#leftHand ul li").on("click","a",function(){
		//判断a是否有链接指定的页面；
		if($(this).attr("data-href")){
			//检查是否登录，未登录直接弹出登录界面；
			if($("#footerinformation").text()=='未登录'){
				$("#login").click();
				return false;
			}
			//关闭前先检查是否存在下拉提示；
			if($("#suggestbox").css("display")!="none"){
				alert('请先退出下拉提示框！');
				return false;
			}
			//功能菜单对应的动作；
			if($("#tabMenu>li:contains("+$(this).text()+")").length==1){
				//如选项卡已经打开，直接切换到已经打开的选项卡上；
				$("#tabMenu>li:contains("+$(this).text()+")").click();
			}else{
				//新增选项卡
				if($("#tabMenu>li").length<7){
					$("#splitLineMenu").text($(this).text());//记录菜单值
					$("#tabMenu>li").css({"backgroundColor":"#D0D0D0","color":"black","font-weight":"normal"});
					$("#tabMenu").append('<li style="backgroundColor:#B8B8B8;color:black;font-weight:bold" title="'+$(this).attr("data-title")+'"><img src="'+$(this).parent().children("img").attr("src")+'"> '+$(this).text()+'&nbsp<div></div></li>');
					$("#rightHand>div").hide();
					$("#rightHand").append('<div></div>');
					if($(this).attr("data-href").indexOf("sys_")!=-1){
						//系统菜单的跳转
						var strURL=encodeURI('config/'+$(this).attr("data-href"));
						$("#rightHand>div:visible").load(strURL).css("marginLeft","5px").siblings("div").hide();
					}else{
					    //普通跳转自动跟上权限参数
						if($(this).attr("data-href").indexOf(".php?")==-1){//带参数
							//alert('templates/'+$(this).attr("data-href")+'?rht='+$(this).text());
							var strURL=encodeURI('templates/'+$(this).attr("data-href")+'?rht='+$(this).text());
						}else{//不带参数
							//alert('templates/'+$(this).attr("data-href")+'&rht='+$(this).text());
							var strURL=encodeURI('templates/'+$(this).attr("data-href")+'&rht='+$(this).text());
						}
						//alert(strURL);
						$("#rightHand>div:visible").load(strURL).css("marginLeft","5px").siblings("div").hide();
					}
					//新建选项卡后，模拟单击选择该选项卡；
					$("#tabMenu>li:contains("+$(this).text()+")").click();
				}else{
					alert("选项卡最大显示7项！");
				}
			}
		}else{
			$(this).parent("li").click();//a没有href属性的情况；
		}
		return false;
	})
	//获取后台菜单权限值数据，隐藏该菜单；
	$.post("./config/menu.php",function(data,status){
		JSON.parse(data,function(k,v){
			if(v==0) $('#mymenu #'+k).hide();     
		});     
    });
});