$(function(){
	InitUserAddress();
});
/**
 * 获取用户所在地址
 */
function InitUserAddress(){
	$.ajax({
        type: 'Get',
        cache:false,
        url: 'http://api.map.baidu.com/location/ip',
        data: {"ak":"7jEcZBZR91Am7YnnGtaEEu0GoxZlIcOj",
        	"coor":"bd09ll"},
        dataType: 'jsonp',
        success: function (data) {
        	var city = data.content.address_detail.city;
        	if(city == "成都市"){
        		
        	}else{
        		$("#DispatchingOtherDateNote").show();
        		$("#DispatchingTakeOtherDateNote").show();
        		$("#date_select_box").hide();
        		$("#DispatchingOtherNote").show();
        		$("#DispatchingTime").hide();
        		$("#DispatchingDateNoteRight").hide();
        	}
        },
        error: function (error) {
        	console.log("获取用户所在地发生错误");
        }
    });
}
/**
 * 获取配送其他城市时间
 *//*
function GetDispatchingOtherDate(){
	var date =new Date();
	date.setDate(date.getDate()+1); 
	var week = date.getDay();
	if(week == 6){
		date.setDate(date.getDate()+2); 
	}else if(week == 0){
		date.setDate(date.getDate()+1);
	}
	var str = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
	return str;
	
}*/