 
$(function(){ 

	CONST_CART=CONST_CART.toLowerCase();
	CONST_CART = CONST_CART.replace('cart/url','Cart/URL');
	//积分订单
    $.signPoint = function() {
       
        var url = CONST_CART.replace('Cart/URL', 'Member/sign'); 
        url += "?" + Math.random();
        $.ajax({
            "url": url,
            success: function(msg) {
                var o = eval(msg);
                if (o.status == "1") {
                    clearpopj('恭喜，签到成功，积分已到账！','success',true);
                }else{
                    clearpopj("对不起，您今天已经签过到了！",'error',true);
                }
            }
        }) 
    };
	  
	
	//积分订单
    $.addPoint = function(nID, nNum, cExt) {
         if (nNum == undefined) {
            nNum = 1
        };
        if (cExt == undefined) {
            cExt = ''
        } else {
            cExt = escape(cExt)
        };
        var url = CONST_CART.replace('Cart/URL', 'Order/addPointOrder'); 
        url += "?item_id=" + nID + "&num=" + nNum + "&ext=" + cExt + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function(msg) {
                var o = eval(msg);
                if (o.status == "1") {
                    clearpopj('恭喜，积分兑换商品申请成功，我们会尽快处理您的兑换请求！','success',true);
                }else{
                    clearpopj("对不起，积分兑换申请失败！<br /><br />原因："+o.info,'error',true);
                }
            }
        }) 
    };
	
	//购物车相关
    $.buyCart = function(nID, nNum, cExt) {
         if (nNum == undefined) {
            nNum = 1
        };
        if (cExt == undefined) {
            cExt = ''
        } else {
            cExt = escape(cExt)
        };
        var url = CONST_CART.replace('URL', 'add');
        var casher = CONST_CART.replace('Cart/URL', 'm_cart');
        url += "?item_id=" + nID + "&num=" + nNum + "&ext=" + cExt + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function(msg) {
                var o = eval(msg);
                if (o.status == "1") {
                  location=casher;	//去订单页面
                } else {
                    clearpopj("对不起，加入购物车失败",'error',true);
                }
            }
        }) 
    };
    $.addAllCart = function(nID, nNum, cExt) {
        if (nNum == undefined) {
            nNum = 1
        };
        if (cExt == undefined) {
            cExt = ''
        } else {
            cExt = escape(cExt)
        };
        var url = CONST_CART.replace('URL', 'addAll');
        url += "?item_ids=" + nID + "&num=" + nNum + "&ext=" + cExt + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function(msg) {
                var o = eval(msg);
                if (o.status == "1") {
                    //jAlert("恭喜，该商品已经成功加入购物车！");
              //      $.getCartNo()
                } else {
                    clearpopj("对不起，加入购物车失败",'error',true);
                }
            }
        })
    };
    $.addCart = function(nID, nNum, cExt) {
        if (nNum == undefined) {
            nNum = 1
        };
        if (cExt == undefined) {
            cExt = ''
        } else {
            cExt = escape(cExt)
        };
    	var urlCasher = CONST_CART.replace('Cart/URL', 'm_cart');
        var url = CONST_CART.replace('URL', 'add');
        url += "?item_id=" + nID + "&num=" + nNum + "&ext=" + cExt + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function(msg) {
                var o = eval(msg);
                if (o.status == "1") {
                   //var title="恭喜，已成功加入购物车！<br /><br /><div style='display:block; text-align:center;'><input type='button' value ='再逛逛' class='cart-cancel' onclick='$.alerts._hide();'/> <input type='button' value ='去结算' class='cart-go' onclick=\"location='"+urlCasher+"';\"/></div>"; 
               		//jBox(title);
					 var title="Add to cart successfully.";
               		jTip(title);
                    $.getCartNo()
                } else {
                    clearpopj(o.info,'error',true);
                }
            }
        })
    };
    $.editCart = function(nID, nNum, cExt) {
        if (nNum == 0) {
           // if (confirm("您确定要从购物车中移除该产品吗？")) {
                $.delCart(nID,  cExt)
            //}
        } else {
            var url = CONST_CART.replace('URL', 'edit');
            url += "?item_id=" + nID + "&num=" + nNum + "&ext=" + cExt + "&" + Math.random();
            $.ajax({
                "url": url,
                success: function(msg) {
                    var o = eval(msg);
                    if (o.status == "1") {
                        //$.getCartNo();
						location.reload();
                    } else {
						if(o.info!=0){
                            clearpopj(o.info,'success',true,'self');
						}else{
                            clearpopj(o.info,'error',true,'self');
						}
                    }
                }
            })
        }
    };
    $.delCart = function(nID, cExt) {
       // if (confirm("您确定要从购物车中移除该产品吗？")) {
		var title = "Are you sure to delete this product?";
				var url = CONST_CART.replace('URL', 'del');
            url += "?item_id=" + nID + "&ext=" + cExt + "&" + Math.random();
            $.ajax({
                "url": url,
                success: function(msg) {
                    var o = eval(msg);
                    if (o.status == "1") {
                        location.reload();
						//$.getCartNo();
                    } else {
                        clearpopj("Sorry, remove failed!",'error',true,'self');
                    }
                }
            })
       // }
    };
    $.clearCart = function(shop_id) {
		if(shop_id==undefined){shop_id=0};
        swal({
            title: '',
            text: 'Are you sure you want to empty the shopping cart?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes",
            //confirmButtonColor: "#35D374"
        }, function() {
            var url = CONST_CART.replace('URL', 'ept');
            url += "?shop_id="+shop_id+"&" + Math.random();
            $.ajax({
                "url": url,
                success: function(msg) {
                    var o = eval(msg);
                    if (o.status == "1") {
                        location.reload();
                        // jAlert("恭喜，已清空购物车！")
                    } else {
                        clearpopj("Sorry, remove failed!",'error',true,'self');
                    }
                }
            })
        });
    };
    $.loadCart = function() {
        var url = CONST_CART.replace('URL', 'load');
        url += "?" + Math.random();
        $.ajax({
            "url": url,
            success: function(msg) {
                clearpopj(msg,'error',true);
            }
        })
    };
    $.getCreditAmount = function(credit) {
        var url = CONST_CART.replace('URL', 'getCreditAmount');
        url += "?credit=" + credit + "&" + Math.random(); 
		$.ajax({"url":url,success:function(msg){
			$("#creditamount").html(msg); 
		}
		});
    };
    $.getCartNo = function() {
		var shop_id=$("#shop_id").val();
			shop_id =(shop_id==undefined?1:shop_id); 
        var url = CONST_CART.replace('URL', 'getNum');
        url += "?shop_id="+shop_id+"&" + Math.random();
        $.ajax({
            "url": url,
            success: function(msg) {
                var o = eval(msg);
                if (o.status == "1") {
					var arr=(o.info+"|").split("|");
                   //$("#CartNo,#CartNum").html(arr[0]);
                   $("#CartAmount").html(arr[1]);
				   if(arr[0]!="0"){
						//显示提交订单按钮
						$("#tjDiv").show();   
				   }else{
						$("#tjDiv").hide();
				   }
                } else {}
            }
        })
    };
    $.rendCart = function() {
		var cartids=$("#cartids").val();
		var cartarr=cartids.split(",");
		var favids=$("#favids").val();
		
		$(".addCart").each(function(){
			var v=$(this).attr("data");
			if($.inArray(v,cartarr)>-1){
				$(this).attr("checked",true);
			}
		});
	};
	
	$.resetInfo = function(){ 
		$("#detail-info img").each(function(){
			var v=$("#detail-info").width();
			var w=$(this).width();
			 
			if(w>v){
				$(this).width(v);	
			}
		})
	};
	
    $.addFav = function(nID) {
       
        var url = CONST_CART.replace('URL', 'addFav');
        url += "?item_id=" + nID + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function(msg) {
                var o = eval(msg);
                if (o.status == "1") {
                   var title="恭喜，已成功加入收藏夹！";
                    clearpopj(title,'success',true);
                } else {
                    clearpopj("对不起，加入收藏夹失败！",'error',true);
                }
            }
        })
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
            clearpopj("Sorry, please fill in the receipt!",'error',true);
            return false
        };
		var  deliverydate = $("#delivertime").val();
		if(deliverydate==''){
            clearpopj("Sorry, please select the delivery date!",'error',true);
            return false
		}

        swal({
            title: '',
            text: 'Are you sure you want to submit the order?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes",
             //confirmButtonColor: "#35D374"
        }, function() {
            $("#form1").submit();
        });

		return false;
    };
	
	
	//**供会员中心使用
	$.confirmOrder =  function(orderno) { 
        var title = "Are you sure you want to confirm the completion of the order?";
        swal({
            title: '',
            text: title,
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes",
             //confirmButtonColor: "#35D374"
        }, function() {
            var url = CONST_CART.replace('Cart/URL', 'Order/confirmOrder');
            url += "?orderno="+orderno+"&" + Math.random();
            $.ajax({
                "url": url,
                success: function(msg) {
                    var o = eval(msg);
                    if (o.status == "1") {
                        location.reload();
                    } else {
                        clearpopj(o.info,'error',true);
                    }
                }
            })
        });
		return false;
    };
		

	$.delAddress =  function(id,jurl) {
        var title = "Are you sure you want to delete this address?";
        swal({
            title: '',
            text: title,
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes",
             //confirmButtonColor: "#35D374"
        }, function() {
            var url = CONST_CART.replace('Cart/URL', 'Member/deleteAddress');
            url += "?id="+id+"&" + Math.random();
            $.ajax({
                "url": url,
                success: function(msg) {
                    var o = eval(msg);
                    if (o.code ==200) {
                        if(jurl){
                            window.location.href = jurl;
                        }else{
                            clearpopj(o.message,'success',true,'self');
                        }
                    } else {
                        clearpopj(o.message,'error',true);
                    }
                }
            })
        });
		return false;
    };
	
	$.setDefaultAddress =  function(id) { 
        var url = CONST_CART.replace('Cart/URL', 'Member/setDefaultAddress');
			url += "?id="+id+"&" + Math.random();
			$.ajax({
				"url": url,
				success: function(msg) {
					var o = eval(msg);
					if (o.status == "1") {
						location.reload();
					} else {
						alert(o.info);
					}
				}
			})
		return false;
    };
	
	$.delFav =  function(id) { 
        var title = "Are you sure you want to delete this collection?";
        swal({
            title: '',
            text: title,
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes",
             //confirmButtonColor: "#35D374"
        }, function() {
            var url = CONST_CART.replace('Cart/URL', 'Member/deleteFav');
            url += "?id="+id+"&" + Math.random();
            $.ajax({
                "url": url,
                success: function(msg) {
                    var o = eval(msg);
                    if (o.status == "1") {
                        location.reload();
                    } else {
                        alert(o.info);
                    }
                }
            })
        });
		return false;
    };
	
	$.clrHistory =  function(id) { 
        var title = "Are you sure you want to empty the browsing history?";
        swal({
            title: '',
            text: title,
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes",
             //confirmButtonColor: "#35D374"
        }, function() {
            var url = CONST_CART.replace('Cart/URL', 'Member/clearHistory');
            url += "?id="+id+"&" + Math.random();
            $.ajax({
                "url": url,
                success: function(msg) {
                    var o = eval(msg);
                    if (o.status == "1") {
                        location.reload();
                    } else {
                        alert(o.info);
                    }
                }
            })
        });

		return false;
    };
	
	$.logout =  function(id) { 
        var title = "Are you sure you want to quit?";
        swal({
            title: '',
            text: title,
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes",
             //confirmButtonColor: "#35D374"
        }, function() {
            var url = CONST_CART.replace('Cart/URL', 'Login/logout');
            location = url;
        });
		return false;
    };
	
    $.addComment = function(id, PID, title, orderno) {; 
		if($.trim(title)==""){
            clearpopj("Sorry, comment cannot be empty.",'error',true);
			return false;
		};
        var title1 = "Are you sure you want to post the comment?";
        swal({
            title: '',
            text: title1,
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes",
             //confirmButtonColor: "#35D374"
        }, function() {
            var url = CONST_CART.replace('Cart/URL', 'Member/addComment');
            url += "?id=" + id + "&pid=" + PID + "&comment=" + title + "&orderno=" + orderno + "&" + Math.random()
            $.ajax({
                "url": url,
                "success": function(o) {
                    if (o.status == "1") {
                        clearpopj("Succeed",'success',true,'self');
                    } else {
                        clearpopj("Failed.",'error',true);
                    }
                }
            })
        });

    };
	
	$.payOrder =  function(orderno,price) { 
		location = "/home/order/wxpay/orderno_"+orderno+".html";//payout
	}
	
	
});

$(function(){
	//初始化 
	//$.getCartNo();
	$.resetInfo(); 
	 $("img.lazy").lazyload({effect : "fadeIn" ,threshold : 200});
	 $(".addCart").on("click",function() {
		var pid=$(this).attr("data");
		var ck=$(this).is(":checked");
		var cext=$(this).attr("ext"); 
		if(ck){
			$.addCart(pid, 1, cext) ;
		}else{
			$.delCart(pid, cext) ;
		}
	});
	
	 $(".cartnum").on("change",function() {
        var v = $(this).val();
		var stock=$(this).attr("data-stock"); 
        if (isNaN(v)) {
            v = 0
        }else{
			v=parseInt(v);	
		}
		if (isNaN(stock)) {
            stock = 0
        }else{
			stock=parseInt(stock);	
		}
		
		if(v>stock){
			$(this).val(stock);
            clearpopj("Insufficient stock!",'error',true);
			return false;
		}
    });
	
    $(".btnPlus").on("click",function() {
        var v = $(this).prev("input").val(); 
       if (isNaN(v)) {
            v = 0
        }else{
			v=parseInt(v);	
		}
		 
		v++;
        $(this).prev("input").val(v).change(); 
		
		var num=parseFloat($(this).prev("input").val());	
		var price=parseFloat($(this).prev("input").attr("data"));
		var nAmount = 0;
			nAmount = num*price; 
			nAmount = nAmount.toFixed(2);
		var o=$(this).parent().find(".CartAmount");
			o.html(nAmount);
			
        return false;
    });
    $(".btnDeduct").on("click",function() {
        var v = $(this).next("input").val();
        if (isNaN(v)) {
            v = 0
        }
        v--;
        if (v < 0) {
            v = 0
        }
		if(v==0){
			//隐藏该条目
		var obj=$(this);
		var title = "Are you sure to delete this product?";
        swal({
            title: '',
            text: title,
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "Yes",
             //confirmButtonColor: "#35D374"
        }, function() {
            obj.next("input").val(v).change();
        });
		}else{
			$(this).next("input").val(v).change();	
			var num=parseFloat($(this).next("input").val());	
			var price=parseFloat($(this).next("input").attr("data"));
			var nAmount = 0;
				nAmount = num*price; 
			nAmount = nAmount.toFixed(2);
			var o=$(this).parent().find(".CartAmount");
				o.html(nAmount);
		}
		
        return false;
    });
	
    $(".btnCartNum").on("change",function() {
		var shop_id=$("#shop_id").val();
		var id=$(this).attr("data");
		var ext=$(this).attr("ext");
		var num=$(this).val();
	 	$.editCart(id, num, ext);
        return false
    });
	
	$(".pagelist select").change(function(){
		var v=$(this).find("option:selected").attr('data');
		location=(v);	
	});
	
	
	$(".nav ul li a").each(function(){
		var href=$(this).attr("href");
		href = href.replace('.html',EXTRA_PARAM+".html");
		$(this).attr("href",href);
	});

	$("#sou").click(function(){
		$("#searchform").toggle();
		return false;
	});
	
});

function isN(id) {
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
	var url = CONST_CART.replace('Cart/URL', 'Member/getArea');
	
	
    $("#China_Province").change(function() {
        $("#China_City,#China_District").hide();
		var url1 =url+ "?tbl=china_city&id="+$(this).val()+"&" + Math.random();
        $.ajax({
            url: url1,
            success: function(msg) {
                $("#China_City").html(msg);
                $("#China_City").show()
            }
        })
    });
    $("#China_City").change(function() {
        $("#China_District").hide();
		var url1 =url+"?tbl=china_district&id="+$(this).val()+"&" + Math.random();
        $.ajax({
            url: url1,
            success: function(msg) {
                $("#China_District").html(msg);
                $("#China_District").show()
            }
        })
    })
};