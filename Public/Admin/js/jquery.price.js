
$(function(){
	$("#pid").change(function(){
		var mid=$(this).find("option:selected").attr("data");
		$.showFields(mid,$("#id").val());
	});
	
	$("#btnPrice").click(function(){
		$(this).blur();
		var o=$(this);
		var l=o.position().left;
		var t=o.position().top;  
		$("#priceBox").css({"left":l,"top":t+23});
		
		var lastrow=$("#priceBox table .priceRow").length;
		if(lastrow==0){
			$("#btnPricePlus").click();
		}
		
		$("#priceBox").toggle(); 
	});	
	
	$("#btnPricePlus").click(function(){
		var lastrow=$("#priceBox table tr").last();
		$(getTemp()).clone().insertAfter(lastrow);
   		$(".numeric").numeric();
	});
	
	$(".pricedel").live("click",function(){
		$(this).parent().parent().remove();
		getPriceInfo();
	});
	
	
	//初始化价格：
	rendPriceInfo();
	
});

//价格相关
function getPriceInfo()
{
	
	var $test = $("input[name=prices]");
	var str=""
	for(var i=0;i<$test.length;i=i+4){    
	 str+=$test.eq(i).val()+"￠"+$test.eq(i+1).val()+"￠"+$test.eq(i+2).val()+"￠"+$test.eq(i+3).val()+"¤";
	}
	$("#priceinfo").val(str);
}

function rendPriceInfo(){
	var $test = $("#priceinfo").val();
    if(!$test){
        return false;
    }
	var str=""
	var arr1=$test.split("¤");
	var arr2;
	for(var i=0;i<arr1.length;i++){ 
		if(arr1[i].indexOf("￠")>-1){
			arr2=arr1[i].split("￠"); 
			str+=getTemp(arr2[0],arr2[1],arr2[2],arr2[3]);
			 
		}
	};
	
	if(str!=""){
		var lastrow=$("#priceBox table tr").last();
		$(str).insertAfter(lastrow);
		//$("#priceBox").show();
	}
   		$(".numeric").numeric();
}

function getTemp(){
	var p1,p2,p3,p4;
	p1=(arguments[0]==undefined?"":arguments[0]);
	p2=(arguments[1]==undefined?"":arguments[1]);
	p3=(arguments[2]==undefined?"":arguments[2]);
	p4=(arguments[3]==undefined?"":arguments[3]);
	html = "";
	html = html + ("<tr class=\"priceRow\">");
	html = html + ("  <td><input type=\"text\" class=\"inputText1 price\" name=\"prices\" maxlength=\"100\" value=\""+p1+"\" \></td>");
	html = html + ("  <td><input type=\"text\" class=\"inputText1 w50 numeric price\" name=\"prices\" value=\""+p2+"\" \></td>");
	html = html + ("  <td><input type=\"hidden\"   name=\"prices\" maxlength=\"100\" value=\""+p3+"\" \></td>");
	html = html + ("  <td><input type=\"hidden\"   name=\"prices\" maxlength=\"100\" value=\""+p4+"\" \></td>");
	html = html + ("  <td><span class=\"pricedel\">x<\span></td>");
	html = html + ("</tr>");
	return (html);	
}