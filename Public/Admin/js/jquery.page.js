$(function(){ 
	//表格滑过特效
	$(".MainTbl .row1,.MainTbl .row2").hover(function(){$(this).addClass("hover");}, function(){$(this).removeClass("hover");}); 
	//全选与反选按钮
	$("#AllCheck").click(function(){
		$(".MainTbl input:[name=SelectIDs]").attr("checked",true);
	});
	$("#ReverseCheck").click(function(){
		$(".MainTbl input:[name=SelectIDs]").each(function(){
			$(this).attr("checked",!$(this).attr("checked"));
		});
	});
	$("#AllDel").click(function(){
		if(getSelectIDs()==""){
			alert("请您先勾选要操作条目！");	
		}else{
			var tbl=$("#ConstTbl").val();
			var ids=getSelectIDs();
			if(confirm("您确定要批量删除这些条目吗？删除后无法恢复！！")){
	 
					$.ajax({url:APP_PATH+"/Admin/Rbac/batch?table="+tbl+"&id="+ids+"&col=__del__&v="+Math.random(),success:function(msg){
						msg=eval(msg);
						if(msg.status=="1"){
							alert ("批量删除成功！");
							location.reload();	
						}else{
							
						}
						 
					}
					});
			};
		};
	});
	 
	 
	$("#AllStatus_1").click(function(){
		if(getSelectIDs()==""){
			alert("请您先勾选要操作条目！");	
		}else{
			var tbl=$("#ConstTbl").val();
			var ids=getSelectIDs();
			var id=$("#AllStatus_2").val();
			if(confirm("您确定要批量设置选中条目吗？")){
				
					$.ajax({url:APP_PATH+"/Admin/Rbac/batch?table="+tbl+"&id="+ids+"&col=__sta__&v="+id+"&"+Math.random(),success:function(msg){
						msg=eval(msg);
						if(msg.status=="1"){
							alert ("批量设置成功！");
							location.reload();	
						}else{
							
						}
						 
					}
					});
			};
		};
	});
	
	 $("#form1").submit(function(){
		var IsSubmit = true;
		$("#form1 span.fc_red").each(function(){
			var obj=$(this).prev("input");
			var label=$(this).parent().prev();
			if(obj.val()==""){
				var txt=(label.text().replace("：",""));
				alert("对不起,"+txt+"不能为空！");
				obj.focus();
				IsSubmit = false;
				return false;
			}
		});
		return IsSubmit;
	});
	
	$(".MainTbl input:text[readonly]").addClass("readonly");
	$(".MainTbl input:text").first().focus();
	
	
	
	$(".dplus1").click(function(){
		var obj=$(this).next("input")	;
		if (parseInt(obj.val())<2){
			obj.val(1);
		}else{
			obj.val(parseInt(obj.val())-1)	;
			obj.change();
		};
	});
	
	$(".dplus2").click(function(){
		var obj=$(this).prev("input")	;
		var nStock=obj.attr("stock");
		nStock = parseInt(nStock);
		if (parseInt(obj.val())>nStock-1){
			obj.val(nStock);
			alert("对不起，本产品库存只有"+nStock+"！");
		}else{
			obj.val(parseInt(obj.val())+1)	;
			obj.change();
		};
	});
	
});

(function(){
	$.showTip=function(txt){
		$("#tip").html(txt);
		setTimeout(function(){$("#tip").html("");},3000);	
	};
	
	$.EditOrder = function(type,orderno,did,val){
		if(type=="0"){
			if($('#spanAdd').css("display")=="none"){
				$('#spanAdd').show();
			$('#spanAdd input').focus();
				return false;	
			};
			if(did==""){
				alert("对不起，请输入要添加的产品ID！");
				$("#AddProductID").focus();
				return false;
				}
			if (confirm("您确定要添加该条目吗？")){
				
			}else{
				return false;	
			}
		}else if(type=="2"){
			if (confirm("您确定要删除该条目吗？")){
				
			}else{
				return false;
			};
		}
		
		var mid=type;
		var cid=orderno+"|"+did+"|"+val; 
		$.ajax({url:APP_PATH+"/Admin/Cms/ajax?type=1&mid="+mid+"&cid="+cid+"&"+Math.random(),success: function(msg){
			if(msg==""){
				location.reload();
			}else{
			 	alert(msg);
			}
		}
		});
	}	;
	
	$.showFields = function(mid,cid){
		if(cid==""){cid=0;}  
		
		$.ajax({url:APP_PATH+"/Admin/Cms/ajax?type=0&mid="+mid+"&cid="+cid+"&"+Math.random(),success: function(msg){
				if(msg==""){
					var rootid=$("#pid option").eq(1).val();
					if(rootid=="2"){
					$(".proext").show();
					}else{
					$(".proext").hide();
					};
					$("#ext_field").hide();
					$("#autotab_3").html(msg);
				}else{
					$(".proext").show();
					$("#ext_field").show();
					$("#autotab_3").html(msg);
					//赋予效果
					$("#autotab_3 .numeric").numeric();
					$("#autotab_3 .editor").xheditor();
					$("#autotab_3 .calendar1").calendar({format:'yyyy-MM-dd HH:mm:ss'});
				}
			}
		});
		 
	}	;
	
})(jQuery);

   
function getSelectIDs(){
	var selectids="";
	$(".MainTbl input:[name='SelectIDs'][checked]").each(function(){
		selectids += $(this).val()+",";
	});	
	return selectids;
};

function setVal(tbl,col,id,val,showmsg){
	$.ajax({url:APP_PATH+"/Admin/Rbac/batch?table="+tbl+"&id="+id+"&col="+col+"&v="+val+"&"+Math.random(),success:function(msg){
		msg=eval(msg);
		if(msg.status=="1"){
			var str=col.substring(0,3).toLowerCase();
			if(str=="tag"||str=="pri"||str=="sor"||str=="sto"){  //指定指定不执行操作

			}else{
               if(showmsg == true){
                   clearpopj('操作成功!');
                   return ;
               }else{
                   location.reload();
               }
			}
		}else{

		}
	}
	});
}


function setGroup(obj,id,col){
	var tbl,val;
	tbl = "content";
	val = $(obj).attr("checked")?1:0;
	setVal(tbl,col,id,val)
}

function setOrder(obj,id,showmsg){
	var tbl,col,val;
	tbl = "order";
	col = "status";
	val=$(obj).find('option:selected').val();
    setVal(tbl,col,id,val,showmsg)

}

function setOrdernew(obj,id){
	var tbl,col,val;
	tbl = "ordernew";
	col = "__sta__";
	val=$(obj).find('option:selected').val();
	setVal(tbl,col,id,val)
}



function modifyUser(userid){
    if(!userid){
        clearpopj('参数错误');
        return false;
    }
    if($('#member input[name=password]').val().length<4){
        clearpopj('密码长度最小4位');
        return false;
    }
    var user = {
        id:userid,
        username:$('#member input[name=username]').val(),
        email:$('#member input[name=email]').val(),
        occupation:$('#member input[name=occupation]').val(),
        telephone:$('#member input[name=telephone]').val(),
      //  cityname:$('#member input[name=cityname]').val(),
        userpwd:$('#member input[name=password]').val(),
        hobby:$('#member input[name=hobby]').val(),
        usertype:$('#member select[name=usertype]').val(),
        sex:$('#member input[name=sex]:checked').val()
    }
    $.post('/admin/member/modifyUser.html',user,function(data){
        clearpopj(data.message);
    })
}
function modifyAddr($id){
    if(!$id){
        clearpopj('参数错误');
        return false;
    }
    var addr = {
        id:$id,
        username:$('.addr_tabel_'+$id+' input[name=username]').val(),
        telephone:$('.addr_tabel_'+$id+' input[name=telephone]').val(),
        address_or:$('.addr_tabel_'+$id+' input[name=address_or]').val(),
        sex:$('.addr_tabel_'+$id+' input[name=sex'+$id+']:checked').val(),
        telephone2:$('.addr_tabel_'+$id+' input[name=telephone2]').val(),
      //  cityname:$('.addr_tabel_'+$id+' input[name=cityname]').val(),
        address:$('.addr_tabel_'+$id+' input[name=address]').val(),
        address_reserve:$('.addr_tabel_'+$id+' input[name=address_reserve]').val(),
    }
    $.post('/admin/member/modifyAddr.html',addr,function(data){
        clearpopj(data.message);
    })
}
