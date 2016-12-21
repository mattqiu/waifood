$(function(){
	var _WIDTH=$(window).width();
	var _HEIGHT=$(window).height();
	var _TOP=95;
	var _LEFT=200;
	var _SPLIT=10;
	
	//初始化布局
	initPage();
	$(window).resize(function(){initPage();});
	function initPage(){
		_WIDTH=$(window).width();
		_HEIGHT=$(window).height();
        console.log('_WIDTH='+_WIDTH);
        console.log('_HEIGHT='+_HEIGHT);
        console.log('_TOP='+_TOP);
        console.log('_LEFT='+_LEFT);
        console.log('_SPLIT='+_SPLIT);
		$("#Lefter").height(_HEIGHT-_TOP);
		$("#Lefter").width(_LEFT);
		$("#Righter").width(_WIDTH-_LEFT-_SPLIT-2);
		$("#Righter").height($("#Lefter").height());
		$("#Spliter").width(_SPLIT-2);
		$("#Spliter").height($("#Lefter").height());
		$(".container").height($("#Lefter").height());
		setLocation("","");
		
		//$("#tester").text(_WIDTH-$("#Lefter").width()-_SPLIT+","+_HEIGHT); 
	};
	
	function setLocation(menu1,menu2){
		var html="&nbsp;<b>当前位置：</b> 网站后台";
		html +=  (menu1==""?" > 首页":(" > "+menu1));
		html +=  (menu2==""?"":(" > "+menu2));
		$("#Location").html(html);	
	}
	
	//显隐左侧菜单
	$("#Spliter").click(function(){_LEFT=(_LEFT==200?0:200);$("#Lefter").toggle();$(this).toggleClass("spliterRight",_LEFT==0);initPage();});
	
	//左侧菜单收缩特效
	$("#LeftMenu ul li:has(ul) span").click(function(){
		setLocation($(this).text(),"");
		var $ul = $(this).siblings("ul");
		if($ul.is(":visible")){
			$(this).attr("class","collapsed");
			$ul.hide();//.slideUp("fast");
		}else{
			$(this).attr("class","expanded");
			$ul.show();//.slideDown("fast");
		}
		return false;
	});
	
	//菜单链接处理
	$("#LeftMenu li a").attr("target","MainFrame").click(function(){
		setLocation($(this).parent().parent().parent().find("span").text(),$(this).text());
			$("#LeftMenu li a").removeClass("selected");
			$(this).addClass("selected").blur();
		});
	
	$("#ClrCache").click(function(){
		var obj=$(this).prev("span"); 
		if(obj.length==0){
			$(this).parent().prepend("<span id='tip'><img src='/Public/Admin/images/load.gif' /></span>");
		};
		obj=$(this).prev("span"); 
		obj.show();
		$.ajax({url:APP_PATH+'/Admin/Login/clrcache',success:function(msg){
			msg=eval(msg);
			if(msg.status=="1"){
				//alert ("缓存更新成功！");
				obj.hide();
			}else{
				
			}
		}})
	});
	
	var okInterval=setInterval(function(){
		$.visit();
	},100000);
});

(function(){
/*	$.visit=function(){
		$.ajax({url:"/Login/test"});
			
	}*/
})(jQuery);