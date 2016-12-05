
$(function() {
    /*$(".lazy").lazyload({
        effect: "fadeIn",
        threshold: 200,
        skip_invisible: false
    });*/
    $(".btnPlus").click(function() {
        var v = $(this).prev("input").val();
        if (isNaN(v)) {
            v = 0
        }
        v++;
        $(this).prev("input").val(v);
        return false
    });
    $(".btnDeduct").click(function() {
        var v = $(this).next("input").val();
        if (isNaN(v)) {
            v = 1
        }
        v--;
        if (v < 1) {
            v = 1
        }
        $(this).next("input").val(v);
        return false
    });
    $("#btnBuy").click(function() {
        var productstr = $("#allprod").val();
        if (productstr == "") {
            clearpopj("�Բ�������ѡ��Ҫ�������Ʒ��",SYSTITLE);
            return false
        }
        $.ajax({
            url: "/m/Cart.asp?Action=BatchAdd&idstr=" + productstr + "&" + Math.random(),
            success: function(msg) {
                location = "/m/M_MyCart.asp"
            }
        })
    });
	
	/*����ͼƬ100%*/
	$.resetInfo();
	$.getCartNo();
}); 
(function() {

	$.resetInfo = function(){ 
		$("#detail-info img").each(function(){
			var v=$("#detail-info").width();
			var w=$(this).width();
			 
			if(w>v){
				$(this).width("100%");	
			}
		})
	}
	
    $.creditCart = function(id) {
        var num = $("#buycount").val();
        var color = $.trim($("#Color").val()) + "," + $.trim($("#Base").val());
			color = escape(color);
		
		//1. ��Ҫ��¼
		//2. �����
		//3. �ύ�һ������������ۻ��
		
			var title = "��ȷ��Ҫ�һ��ò�Ʒ��";
			jConfirm(title,SYSTITLE,function(msg){
				if(msg){
				 $.ajax({
					url: "/Include/Ajax/?Action=CreditCart&id=" + id + "&orderno=" + color + "&num=" + num + "&" + Math.random(),
					success: function(msg) {
						if(msg==""){
							clearpopj("��ϲ���һ��������ύ�ɹ���",SYSTITLE);
						}else{
							clearpopj(msg,SYSTITLE);
						}
					}
				})
				};	
			});
			
		 
    };
	
    $.addFav = function(id) {
        $.ajax({
            url: "/m/Ajax/?Action=AddFav&id=" + id + "&" + Math.random(),
            success: function(msg) {
                clearpopj("��ϲ���Ѿ��ɹ������ղؼУ�",SYSTITLE)
            }
        })
    };
	
    $.setCity = function(id) {
        $.ajax({
            url: "/Include/Ajax/?Action=SetCity&id=" + id + "&" + Math.random(),
            success: function(msg) {
                location.reload()
            }
        })
    };
    $.buyCart = function(n) {
        var num = $("#buycount").val();
        var num1 = $("#buycount").attr("stock");
        var color = $.trim($("#Color").val()) + "," + $.trim($("#Base").val());
        var n1 = 0;
        if (num == 0) {
            clearpopj("�Բ��������빺��������",SYSTITLE);
            $("#buycount").focus();
            return false
        };
        if (num == undefined) {
            num = 1
        };
        if (parseInt(num) > parseInt(num1)) {
            clearpopj("�Բ��𣬸���Ʒֻʣ" + num1 + "����",SYSTITLE);
            return false
        }
        color = escape(color);
        $.ajax({
            url: "/m/Cart.asp?Action=Add&num=" + num + "&id=" + n + "&color=" + color + "&" + Math.random(),
            success: function(msg) {
                location = "/m/M_MyCart.asp"
            }
        })
    };
    $.addCart = function(n) {
        var num = $("#buycount").val();
        if (num == undefined) {
            num = arguments[1]
        };
        var num1 = $("#buycount").attr("stock");
        var color = $.trim($("#Color").val()) + "," + $.trim($("#Base").val());
        var n1 = 0;
        if (num == 0) {
            clearpopj("�Բ��������빺��������",SYSTITLE);
            $("#buycount").focus();
            return false
        };
        if (num == undefined) {
            num = 1
        };
        if (parseInt(num) > parseInt(num1)) {
            clearpopj("�Բ��𣬸���Ʒֻʣ" + num1 + "����",SYSTITLE);
            return false
        }
        color = escape(color);
        $.ajax({
            url: "/m/Cart.asp?Action=Add&num=" + num + "&id=" + n + "&color=" + color + "&" + Math.random(),
            success: function(msg) {
				var title="��ϲ���ѳɹ����빺�ﳵ��<br /><br /><div style='display:block; text-align:center;'><input type='button' value ='�ٹ��' class='cart-cancel' onclick=\"location='/m/m_category.asp'\"/> <input type='button' value ='ȥ����' class='cart-go' onclick=\"location='M_MyCart.asp';\"/></div>"; 
                jBox(title,SYSTITLE);
				 $.getCartNo();
            }
        })
    };
    $.editCart = function(id, num, color) {
        color = escape(color);
        if (num == 0) {
           
			var title = "��ȷ��Ҫ�ӹ��ﳵ���Ƴ�ò�Ʒ��";
			jConfirm(title,SYSTITLE,function(msg){
				if(msg){
					$.delCart(id, 0, color) 
				};	
			});
		 
        } else {
            $.ajax({
                url: "/m/Cart.asp?Action=Edit&id=" + id + "&num=" + num + "&color=" + color + "&" + Math.random(),
                success: function(msg) {
                    location.reload()
                }
            })
        }
    };
    $.delCart = function(id) {
        var p2 = arguments[1];
        var color = arguments[2];
        color = escape(color);
        if (p2 != "0") {
              
			var title = "��ȷ��Ҫ�ӹ��ﳵ���Ƴ�ò�Ʒ��";
			jConfirm(title,SYSTITLE,function(msg){
				if(msg){
					 $.ajax({
						url: "/m/Cart.asp?Action=Del&id=" + id + "&color=" + color + "&" + Math.random(),
						success: function(msg) {
							location.reload()
						}
					})
				};	
			}); 
			
        } else {
            $.ajax({
                url: "/m/Cart.asp?Action=Del&id=" + id + "&color=" + color + "&" + Math.random(),
                success: function(msg) {
					location.reload();
                   /* $.loadCart();
                    $.getCartNo()*/
                }
            })
        }
    };
    $.loadCart = function() {
        $.ajax({
            url: "/m/Cart.asp?Action=Load&" + Math.random(),
            success: function(msg) {
                $("#LoadCart").html(msg)
            }
        })
    };
    $.clearCart = function() {
       
			var title = "��ȷ��Ҫ��չ��ﳵ��";
			jConfirm(title,SYSTITLE,function(msg){
				if(msg){
					 $.ajax({
                url: "/m/Cart.asp?Action=Clr&" + Math.random(),
                success: function(msg) {
                   location.reload();
                }
            })
				};	
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
     
})($);
function showUCenter() {
    location = "M_UCenter.asp"
}
function IsN(id) {
    var v = $(id).val();
    v = $.trim(v);
    if (v == "") {
        return true
    } else {
        return false
    }
}
function valid_email(email) {
    var patten = new RegExp(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]+$/);
    return patten.test(email)
}
function valid_tel(tel) {
    var isMobile = /^(?:13\d|15\d|14\d|18\d)\d{5}(\d{3}|\*{3})$/;
    var isPhone = /^((0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?$/;
    var patten1 = new RegExp(isMobile);
    var patten2 = new RegExp(isPhone);
    return (patten1.test(tel) || patten2.test(tel))
}
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
    })
};