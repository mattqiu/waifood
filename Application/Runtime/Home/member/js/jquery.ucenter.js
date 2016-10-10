
$(function() {
    $.getCartNo()
});
(function() {
    $.delFav = function(id) { 
        var title = "您确定要删除该收藏吗？";
		jConfirm(title,SYSTITLE,function(msg){
			if(msg){
				 $.ajax({
                url: "/m/Ajax/?Action=DelFav&id=" + id + "&" + Math.random(),
                success: function(msg) {
                    location.reload()
                }
            }) 
			};	
		});
    };
    $.clrHistory = function(id) { 
        var title = "您确定要清空浏览记录吗？";
		jConfirm(title,SYSTITLE,function(msg){
			if(msg){
				$.ajax({
                url: "/m/Ajax/?Action=ClrHistory&id=" + id + "&" + Math.random(),
                success: function(msg) {
                    location.reload()
                }
            }) 
			};	
		});
    };
    $.SubmitOrder = function() {
		
        var OrderInfo = "";
        OrderInfo = $("#UseAddressID").val();
        var info = "";
        info = $.trim($("#Info").val());
        var pm;
        pm = $('input:radio[name="paymethod"]:checked').val();
        pm = (pm==0 ? 0: 1);
        if (OrderInfo == "" || OrderInfo == "0") {
            jAlert("对不起，请先填写收货地址！",SYSTITLE);
            return false
        };
		$.ajax({
                url: "/m/Cart.asp?Action=SubmitOrder&OrderInfo=" + OrderInfo + "&info=" + info + "&pm=" + pm + "&" + Math.random(),
                success: function(msg) {
                    if (msg != "") {
                        var msgArr = (msg.split("￠"));
                        if (msgArr[0] == "1") {
                            location = "/m/M_UOrderStatus.asp?orderno=" + msgArr[2]
                        } else {
							if(confirm(msgArr[0],SYSTITLE)){
								     window.location.href="/m/M_UCenter.asp";
									   }
                            if (msgArr[0].indexOf("余额不足") != -1) {
                                location = "/m/M_UCenter.asp"
                            }
                        }
                    } else {
                        location = "/m/M_UCenter.asp"
                    }
                }
				
      /*  var title = "您确认要提交订单吗？";
		jConfirm(title,SYSTITLE,function(msg){
			if(msg){
				$.ajax({
                url: "/m/Cart.asp?Action=SubmitOrder&OrderInfo=" + OrderInfo + "&info=" + info + "&pm=" + pm + "&" + Math.random(),
                success: function(msg) {
                    if (msg != "") {
                        var msgArr = (msg.split("￠"));
                        if (msgArr[0] == "1") {
                            location = "/m/M_UOrderStatus.asp?orderno=" + msgArr[2]
                        } else {
                            jAlert(msgArr[0],SYSTITLE);
                            if (msgArr[0].indexOf("余额不足") != -1) {
                                location = "/m/M_UCenter.asp"
                            }
                        }
                    } else {
                        location = "/m/M_UCenter.asp"
                    }
                }
            })
			};	*/
		});
    };
    $.getCartNo = function() {
        $.ajax({
            url: "/m/Cart.asp?Action=getCartNo&" + Math.random(),
            success: function(msg) {
                $("#CartNo").html(msg)
            }
        })
    };
    $.addComment = function(id, PID, title, orderno) {; 
        var title1 = "您确定要发表评价吗？一旦提交成功，评价内容不可修改！";
		jConfirm(title1,SYSTITLE,function(msg){
			if(msg){
				 $.ajax({
                url: "/m/Ajax/?Action=AddComment&id=" + id + "&PID=" + PID + "&ConstTbl=" + escape(title) + "&orderno=" + orderno + "&" + Math.random(),
                success: function(msg) {
                    if (msg == "1") {
                        jAlert("恭喜，评价发表成功！",SYSTITLE)
                    } else {
                        jAlert("对不起，评论失败！",SYSTITLE)
                    }
                }
            })
			};	
		});
    };
	
    $.PayOrder = function(act, orderno, tradeno) {
        var title = "您确定要支付本订单吗？款项将从您的余额中扣除！";
		jConfirm(title,SYSTITLE,function(msg){
			if(msg){
				$.ajax({
                url: "/m/Ajax/?Action=PayOrder&PID=" + act + "&orderno=" + orderno + "&tradeno=" + tradeno + "&" + Math.random(),
                success: function(msg) {
                    var msgarr = msg.split("|");
                    if (msgarr[0] == "1") {
                        jAlert(msgarr[1],SYSTITLE);
                        location = "/m/M_UOrder.asp"
                    } else {
                        jAlert(msgarr[1],SYSTITLE)
                    }
                }
            })
			};	
		});
		 
    };
    $.EditOrder = function(act, orderno, productid, num) {
        $.zCancel(act, orderno, productid, num)
    };
    $.zLogOut = function() {
        var title = "您确定要退出吗？";
		jConfirm(title,SYSTITLE,function(msg){
			if(msg){
				location = "M_Login.asp?Action=Exit"
			};	
		});
    };
    $.zCancel = function( act, orderno, productid, num) {	
        var title = "您确定要取消该订单吗？";
		var action="";
			action = "EditO_Order";
		if (act=="3"){
			title = "您确定已收到货了吗？确认收货后可获得相应积分！";	
			action = "ConfirmOrder";
		}
		jConfirm(title,SYSTITLE,function(msg){
			if(msg){
				 $.ajax({
                    url: "/m/Ajax/?Action="+action+"&PID=" + act + "&orderno=" + orderno + "&id=" + productid + "&num=" + num + "&" + Math.random(),
                    success: function(msg) {
                        var msgarr = msg.split("|");
                        if (msgarr[0] == "1") {
                            location.reload()
                        } else {
                            jAlert(msgarr[1],SYSTITLE)
                        }
                    }
                })
			};	
		});
		
    };
    $.zAddressDel = function(id) {
        var title = "您确定要删除该地址吗？";
		jConfirm(title,SYSTITLE,function(msg){
			if(msg){
				location = "M_UAddressEdit.asp?Action=Del&id=" + id	
			};	
		});
    }
})(); 

function InitArea() {
    $("#China_Province").change(function() {
        $("#China_City,#China_District").hide();
        $.ajax({
            url: "/Include/Ajax/?Action=getArea&ConstTbl=City&IDS=" + $(this).val() + "&" + Math.random(),
            success: function(msg) {
                $("#China_City").html(msg);
                $("#China_City").show()
            }
        })
    });
    $("#China_City").change(function() {
        $("#China_District").hide();
        $.ajax({
            url: "/Include/Ajax/?Action=getArea&ConstTbl=District&IDS=" + $(this).val() + "&" + Math.random(),
            success: function(msg) {
                $("#China_District").html(msg);
                $("#China_District").show()
            }
        })
    });
    $.getSortByPath = function(obj) {
        var cpath,
        cid;
        cid = $(obj).val();
        $(obj).nextAll("select").remove();
        if (cpath == "") {} else {
            $.ajax({
                url: "/Include/Ajax/?Action=getSortByPath&IDS=" + cid + "&id=" + 0 + "&" + Math.random(),
                cache: false,
                success: function(msg) {
                    $(obj).parent().append(msg)
                }
            })
        };
        var obj1 = $(obj).parent().parent();
        obj1.children("input:hidden").eq(0).val(obj1.find("select").last().val())
    }
};