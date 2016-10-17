
$(function() {
    $.getCartNo()
});
(function() {
    $.delFav = function(id) { 
        var title = "��ȷ��Ҫɾ����ղ���";
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
        var title = "��ȷ��Ҫ��������¼��";
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
            jAlert("�Բ���������д�ջ���ַ��",SYSTITLE);
            return false
        };
		$.ajax({
                url: "/m/Cart.asp?Action=SubmitOrder&OrderInfo=" + OrderInfo + "&info=" + info + "&pm=" + pm + "&" + Math.random(),
                success: function(msg) {
                    if (msg != "") {
                        var msgArr = (msg.split("��"));
                        if (msgArr[0] == "1") {
                            location = "/m/M_UOrderStatus.asp?orderno=" + msgArr[2]
                        } else {
							if(confirm(msgArr[0],SYSTITLE)){
								     window.location.href="/m/M_UCenter.asp";
									   }
                            if (msgArr[0].indexOf("����") != -1) {
                                location = "/m/M_UCenter.asp"
                            }
                        }
                    } else {
                        location = "/m/M_UCenter.asp"
                    }
                }
				
      /*  var title = "��ȷ��Ҫ�ύ������";
		jConfirm(title,SYSTITLE,function(msg){
			if(msg){
				$.ajax({
                url: "/m/Cart.asp?Action=SubmitOrder&OrderInfo=" + OrderInfo + "&info=" + info + "&pm=" + pm + "&" + Math.random(),
                success: function(msg) {
                    if (msg != "") {
                        var msgArr = (msg.split("��"));
                        if (msgArr[0] == "1") {
                            location = "/m/M_UOrderStatus.asp?orderno=" + msgArr[2]
                        } else {
                            jAlert(msgArr[0],SYSTITLE);
                            if (msgArr[0].indexOf("����") != -1) {
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
        var title1 = "��ȷ��Ҫ����������һ���ύ�ɹ����������ݲ����޸ģ�";
		jConfirm(title1,SYSTITLE,function(msg){
			if(msg){
				 $.ajax({
                url: "/m/Ajax/?Action=AddComment&id=" + id + "&PID=" + PID + "&ConstTbl=" + escape(title) + "&orderno=" + orderno + "&" + Math.random(),
                success: function(msg) {
                    if (msg == "1") {
                        jAlert("��ϲ�����۷���ɹ���",SYSTITLE)
                    } else {
                        jAlert("�Բ�������ʧ�ܣ�",SYSTITLE)
                    }
                }
            })
			};	
		});
    };
	
    $.PayOrder = function(act, orderno, tradeno) {
        var title = "��ȷ��Ҫ֧���������𣿿�����������п۳�";
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
        var title = "��ȷ��Ҫ�˳���";
		jConfirm(title,SYSTITLE,function(msg){
			if(msg){
				location = "M_Login.asp?Action=Exit"
			};	
		});
    };
    $.zCancel = function( act, orderno, productid, num) {	
        var title = "��ȷ��Ҫȡ��ö�����";
		var action="";
			action = "EditO_Order";
		if (act=="3"){
			title = "��ȷ�����յ�������ȷ���ջ���ɻ����Ӧ��֣�";	
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
        var title = "��ȷ��Ҫɾ��õ�ַ��";
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