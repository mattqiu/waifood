$(function () {
    CONST_CART = CONST_CART.toLowerCase();
    CONST_CART = CONST_CART.replace('cart/url', 'Cart/URL');

    //推荐朋友
    $.sendMail = function () {
        var email = $.trim($("#emaillist").val());
        if (email == "") {
            jAlert("Sorry, email cannot be empty.", SYSTITLE, function () {
                $("#emaillist").focus();
            });
            return false;
        }
        var url = CONST_CART.replace('Cart/URL', 'Index/sendmail');
        url += "?emaillist=" + email + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function (msg) {
                var o = eval(msg);
                if (o.status == "1") {
                    jAlert('succeed.', SYSTITLE, function () {
                        $("#emaillist").val("");
                    });

                } else {
                    jAlert("Sorry, email sent failed：" + o.info);
                }
            }
        })
    };
    //积分订单
    $.addPoint = function (nID, nNum, cExt) {
        if (nNum == undefined) {
            nNum = 1
        }
        ;
        if (cExt == undefined) {
            cExt = ''
        } else {
            cExt = escape(cExt)
        }
        ;
        var url = CONST_CART.replace('Cart/URL', 'Order/addPointOrder');
        url += "?item_id=" + nID + "&num=" + nNum + "&ext=" + cExt + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function (msg) {
                var o = eval(msg);
                if (o.status == "1") {
                    jAlert('恭喜，积分兑换商品申请成功，我们会尽快处理您的兑换请求！');
                } else {
                    jAlert("对不起，积分兑换申请失败！<br /><br />原因：" + o.info);
                }
            }
        })
    };

    //购物车相关
    $.buyCart = function (nID, nNum, cExt) {
        if (nNum == undefined) {
            nNum = 1
        }
        ;
        if (cExt == undefined) {
            cExt = ''
        } else {
            cExt = escape(cExt)
        }
        ;
        var url = CONST_CART.replace('URL', 'add');
        var casher = CONST_CART.replace('Cart/URL', 'Settle/cart');
        url += "?item_id=" + nID + "&num=" + nNum + "&ext=" + cExt + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function (msg) {
                var o = eval(msg);
                if (o.status == "1") {
                    location = casher;	//去订单页面
                } else {
                    jAlert("Sorry, add to cart failure!")
                }
            }
        })
    };
    $.addAllCart = function (nID, nNum, cExt) {
        if (nNum == undefined) {
            nNum = 1
        }
        ;
        if (cExt == undefined) {
            cExt = ''
        } else {
            cExt = escape(cExt)
        }
        ;
        var url = CONST_CART.replace('URL', 'addAll');
        url += "?item_ids=" + nID + "&num=" + nNum + "&ext=" + cExt + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function (msg) {
                var o = eval(msg);
                if (o.status == "1") {
                    //jAlert("恭喜，该商品已经成功加入购物车！");
                    $.getCartNo()
                } else {
                    jAlert("Sorry, add to cart failure!")
                }
            }
        })
    };
    $.addCart = function (nID, nNum, cExt) {
        if (nNum == undefined) {
            nNum = 1
        }
        ;
        if (cExt == undefined) {
            cExt = ''
        } else {
            cExt = escape(cExt)
        }
        ;
        if (nNum == 0) {
            return false;
        }
        ;
        var urlCasher = CONST_CART.replace('Cart/URL', 'Settle/cart');
        var url = CONST_CART.replace('URL', 'add');
        url += "?item_id=" + nID + "&num=" + nNum + "&ext=" + cExt + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function (msg) {
                var o = eval(msg);
                if (o.status == "1") {
                    // var title="Add to cart succeed.<br /><br /><div style='display:block; text-align:center;'><input type='button' value ='Continue' class='cart-cancel' onclick='$.alerts._hide();'/> <input type='button' value ='Casher' class='cart-go' onclick=\"location='"+urlCasher+"';\"/></div>";
                    var title = "Add to cart successfully.";
                    jTip(title);
                    $.getCartNo()
                } else {
                    jAlert(o.info)
                }
            }
        })
    };
    $.editCart = function (nID, nNum, cExt) {
        if (nNum == 0) {
            // if (confirm("您确定要从购物车中移除该产品吗？")) {
            $.delCart(nID, cExt)
            //}
        } else {
            var url = CONST_CART.replace('URL', 'edit');
            url += "?item_id=" + nID + "&num=" + nNum + "&ext=" + cExt + "&" + Math.random();
            $.ajax({
                "url": url,
                success: function (msg) {
                    var o = eval(msg);
                    if (o.status == "1") {
                        //$.getCartNo()
                        location.reload();
                    } else {
                        if (o.info != 0) {
                            jAlert(o.info, SYSTITLE, function () {
                                location.reload();
                            });
                        } else {
                            jAlert(o.info, SYSTITLE, function () {
                                location.reload();
                            });
                        }
                    }
                }
            })
        }
    };
    $.delCart = function (nID, cExt) {
        var title = "Are you sure to delete this product?";
        jConfirm(title, SYSTITLE, function (msg) {
            if (msg) {
                var url = CONST_CART.replace('URL', 'del');
                url += "?item_id=" + nID + "&ext=" + cExt + "&" + Math.random();
                $.ajax({
                    "url": url,
                    success: function (msg) {
                        var o = eval(msg);
                        if (o.status == "1") {
                            location.reload();
                            //$.getCartNo();
                        } else {
                            jAlert("Sorry, remove failed!")
                        }
                    }
                })
            }
        });
    };
    $.clearCart = function (shop_id) {
        if (shop_id == undefined) {
            shop_id = 0
        }
        ;

        var title = "Are you sure you want to empty the shopping cart?";
        jConfirm(title, SYSTITLE, function (msg) {
            if (msg) {
                var url = CONST_CART.replace('URL', 'ept');
                url += "?shop_id=" + shop_id + "&" + Math.random();
                $.ajax({
                    "url": url,
                    success: function (msg) {
                        var o = eval(msg);
                        if (o.status == "1") {
                            location.reload();
                            // jAlert("恭喜，已清空购物车！")
                        } else {
                            jAlert("Sorry, empty failed!")
                        }
                    }
                })

            }
        });

    };
    $.loadCart = function () {
        var url = CONST_CART.replace('URL', 'load');
        url += "?" + Math.random();
        $.ajax({
            "url": url,
            success: function (msg) {
                jAlert(msg)
            }
        })
    };
    $.getCreditAmount = function (credit) {
        var url = CONST_CART.replace('URL', 'getCreditAmount');
        url += "?credit=" + credit + "&" + Math.random();
        $.ajax({
            "url": url, success: function (msg) {
                $("#creditamount").html(msg);
            }
        });
    };
    $.getCartNo = function () {
        var shop_id = $("#shop_id").val();
        shop_id = (shop_id == undefined ? 1 : shop_id);
        var url = CONST_CART.replace('URL', 'getNum');
        url += "?shop_id=" + shop_id + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function (msg) {
                var o = eval(msg);
                if (o.status == "1") {
                    var arr = (o.info + "|").split("|");
                    $("#CartNo,#CartNum").html(arr[0]);
                    $("#CartAmount").html(arr[1]);
                    if (arr[0] != "0") {
                        //显示提交订单按钮
                        $("#tjDiv").show();
                    } else {
                        $("#tjDiv").hide();
                    }
                } else {
                }
            }
        })
    };
    $.rendCart = function () {
        var cartids = $("#cartids").val();
        var cartarr = cartids.split(",");
        var favids = $("#favids").val();

        $(".addCart").each(function () {
            var v = $(this).attr("data");
            if ($.inArray(v, cartarr) > -1) {
                $(this).attr("checked", true);
            }
        });
    };

    $.resetInfo = function () {
        $("#detail-info img").each(function () {
            var v = $("#detail-info").width();
            var w = $(this).width();

            if (w > v) {
                $(this).width(v);
            }
        })
    };

    $.addFav = function (nID) {

        var url = CONST_CART.replace('URL', 'addFav');
        url += "?item_id=" + nID + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function (msg) {
                var o = eval(msg);
                if (o.status == "1") {
                    var title = "恭喜，已成功加入收藏夹！";
                    jBox(title);
                } else {
                    jAlert("对不起，加入收藏夹失败！")
                }
            }
        })
    };


    $.SubmitOrder = function () {
        var OrderInfo = "";
        OrderInfo = $("#UseAddressID").val();
        var info = "";
        info = $.trim($("#Info").val());
        var pm;
        pm = $('input:radio[name="paymethod"]:checked').val();
        pm = (pm == 0 ? 0 : 1);
        if (OrderInfo == "" || OrderInfo == "0") {
            jAlert("Sorry, please fill in the receipt!", SYSTITLE);
            return false
        }
        ;
        var deliverydate = $("#delivertime").val();
        if (deliverydate == '') {
            jAlert("Sorry, please select the delivery date!", SYSTITLE);
            return false
        }

        var title = "Are you sure you want to submit the order?";
        jConfirm(title, SYSTITLE, function (msg) {
            if (msg) {
                $("#form1").submit();
            } else {
                return false;
            }
            ;
        });

        return false;
    };


    //**供会员中心使用
    $.confirmOrder = function (orderno) {
        var title = "Are you sure you want to confirm the completion of the order?";
        jConfirm(title, SYSTITLE, function (msg) {
            if (msg) {
                var url = CONST_CART.replace('Cart/URL', 'Order/confirmOrder');
                url += "?orderno=" + orderno + "&" + Math.random();
                $.ajax({
                    "url": url,
                    success: function (msg) {
                        var o = eval(msg);
                        if (o.status == "1") {
                            location.reload();
                        } else {
                            alert(o.info);
                        }
                    }
                })

            } else {
                return false;
            }
            ;
        });

        return false;
    };


    $.cancelOrder = function (orderno) {
        var title = "Are you sure you want to cancel the order?";
        jConfirm(title, SYSTITLE, function (msg) {
            if (msg) {
                var url = CONST_CART.replace('Cart/URL', 'Order/cancelOrder');
                url += "?orderno=" + orderno + "&" + Math.random();
                $.ajax({
                    "url": url,
                    success: function (msg) {
                        var o = eval(msg);
                        if (o.status == "1") {
                            location.reload();
                        } else {
                            alert(o.info);
                        }
                    }
                })

            } else {
                return false;
            }
            ;
        });

        return false;
    };

    $.delAddress = function (id) {
        var title = "Are you sure you want to delete this address?";
        jConfirm(title, SYSTITLE, function (msg) {
            if (msg) {
                var url = CONST_CART.replace('Cart/URL', 'Member/deleteAddress');
                url += "?id=" + id + "&" + Math.random();
                $.ajax({
                    "url": url,
                    success: function (msg) {
                        var o = eval(msg);
                        if (o.status == "1") {
                            location.reload();
                        } else {
                            alert(o.info);
                        }
                    }
                })

            } else {
                return false;
            }
            ;
        });

        return false;
    };

    $.setDefaultAddress = function (id) {
        var url = CONST_CART.replace('Cart/URL', 'Member/setDefaultAddress');
        url += "?id=" + id + "&" + Math.random();
        $.ajax({
            "url": url,
            success: function (msg) {
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

    $.delFav = function (id) {
        var title = "Are you sure you want to delete this collection?";
        jConfirm(title, SYSTITLE, function (msg) {
            if (msg) {
                var url = CONST_CART.replace('Cart/URL', 'Member/deleteFav');
                url += "?id=" + id + "&" + Math.random();
                $.ajax({
                    "url": url,
                    success: function (msg) {
                        var o = eval(msg);
                        if (o.status == "1") {
                            location.reload();
                        } else {
                            alert(o.info);
                        }
                    }
                })

            } else {
                return false;
            }
            ;
        });

        return false;
    };

    $.clrHistory = function (id) {
        var title = "Are you sure you want to empty the browsing history?";
        jConfirm(title, SYSTITLE, function (msg) {
            if (msg) {
                var url = CONST_CART.replace('Cart/URL', 'Member/clearHistory');
                url += "?id=" + id + "&" + Math.random();
                $.ajax({
                    "url": url,
                    success: function (msg) {
                        var o = eval(msg);
                        if (o.status == "1") {
                            location.reload();
                        } else {
                            alert(o.info);
                        }
                    }
                })

            } else {
                return false;
            }
            ;
        });

        return false;
    };

    $.logout = function (id) {
        var title = "Are you sure you want to quit?";
        jConfirm(title, SYSTITLE, function (msg) {
            if (msg) {
                var url = CONST_CART.replace('Cart/URL', 'Login/logout');
                location = url;
            } else {
                return false;
            }
            ;
        });

        return false;
    };
    $.login = function (username, pwd, url1) {
        username = $.trim(username);
        pwd = $.trim(pwd);

        if (username == "") {
            jAlert("Sorry, username cannot be empty.", SYSTITLE, function () {
                $("#username").focus();
            });

            return false;
        }

        if (pwd == "") {
            jAlert("Sorry, password cannot be empty.", SYSTITLE, function () {
                $("#password").focus();
            });
            return false;
        }

        var url = CONST_CART.replace('Cart/URL', 'Login/loginbox');

        $.ajax({
            "url": url,
            "type": "POST",
            "data": "username=" + username + "&userpwd=" + pwd + "&" + Math.random(),
            success: function (msg) {
                if (msg == "1") {
                    location = url1;
                } else {
                    jAlert("Sorry,wrong user name or password.");
                }
            }
        });


        return false;
    };

});

$(function () {
    //初始化
    $.getCartNo();

    $("img.lazy").lazyload({effect: "fadeIn", threshold: 200});

    $(".addCart").on("click", function () {
        var pid = $(this).attr("data");
        var ck = $(this).is(":checked");
        var cext = $(this).attr("ext");
        if (ck) {
            $.addCart(pid, 1, cext);
        } else {
            $.delCart(pid, cext);
        }
    });

    $(".cartnum").on("change", function () {
        var v = $(this).val();
        var stock = $(this).attr("data-stock");
        if (isNaN(v)) {
            v = 0
        } else {
            v = parseInt(v);
        }
        if (isNaN(stock)) {
            stock = 0
        } else {
            stock = parseInt(stock);
        }

        if (v > stock) {
            $(this).val(stock);
            jAlert("Insufficient stock!", SYSTITLE);
            return false;
        }
    });

    $(".btnPlus").on("click", function () {
        var v = $(this).prev("input").val();
        if (isNaN(v)) {
            v = 0
        } else {
            v = parseInt(v);
        }

        v++;
        $(this).prev("input").val(v).change();

        var num = parseFloat($(this).prev("input").val());
        var price = parseFloat($(this).prev("input").attr("data"));
        var nAmount = 0;
        nAmount = num * price;
        nAmount = nAmount.toFixed(2);
        var o = $(this).parent().find(".CartAmount");
        o.html(nAmount);

        return false;
    });
    $(".btnDeduct").on("click", function () {
        var v = $(this).next("input").val();
        if (isNaN(v)) {
            v = 0
        }
        v--;
        if (v < 0) {
            v = 0
        }
        if (v == 0) {
            return false;
            //隐藏该条目
            var obj = $(this);
            var title = "Are you sure you don't buy the product?";
            jConfirm(title, SYSTITLE, function (msg) {
                if (msg) {
                    obj.next("input").val(v).change();
                } else {
                    return false;
                }
            });


        } else {
            $(this).next("input").val(v).change();
            var num = parseFloat($(this).next("input").val());
            var price = parseFloat($(this).next("input").attr("data"));
            var nAmount = 0;
            nAmount = num * price;
            nAmount = nAmount.toFixed(2);
            var o = $(this).parent().find(".CartAmount");
            o.html(nAmount);
        }

        return false;
    });

    $(".btnCartNum").on("change", function () {
        var shop_id = $("#shop_id").val();
        var id = $(this).attr("data");
        var ext = $(this).attr("ext");
        var num = $(this).val();
        $.editCart(id, num, ext);
        return false
    });

    $("#btnCheckout").on("click", function () {
        $(".ld").show();
        return false
    });


    $("#btnMail").on("click", function () {
        $.sendMail();
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


    $("#China_Province").change(function () {
        $("#China_City,#China_District").hide();
        var url1 = url + "?tbl=china_city&id=" + $(this).val() + "&" + Math.random();
        $.ajax({
            url: url1,
            success: function (msg) {
                $("#China_City").html(msg);
                $("#China_City").show()
            }
        })
    });
    $("#China_City").change(function () {
        $("#China_District").hide();
        var url1 = url + "?tbl=china_district&id=" + $(this).val() + "&" + Math.random();
        $.ajax({
            url: url1,
            success: function (msg) {
                $("#China_District").html(msg);
                $("#China_District").show()
            }
        })
    })
};
function AddFavorite() {
    var sURL = window.location.toString();
    var sTitle = document.title;
    try {
        window.external.addFavorite(sURL, sTitle)
    } catch (e) {
        try {
            window.sidebar.addPanel(sTitle, sURL, "")
        } catch (e) {
        }
    }
}
function SetHome(obj) {
    var sURL = window.location.toString();
    var strHref = window.location.href;
    obj.style.behavior = 'url(#default#homepage)';
    obj.setHomePage(sURL)
};

function checkregform() {
    if (!$("#username").val()) {
        jAlert("Sorry,please enter user name.", SYSTITLE, function () {
            $("#username").focus();
        });
        return false
    }
    ;
    /*if($(v).val().length<6){
     jAlert("Sorry, the username must be at least 6.",SYSTITLE,function(){
     $(v).focus();
     });
     return false
     };*/
    if (!$("#userpwd").val()) {
        jAlert("Sorry, please enter your password!", SYSTITLE, function () {
            $("#userpwd").focus();
        });
        return false
    }
    ;
    if ($("#userpwd").val().length < 4 || $("#userpwd").val().length > 20) {
        jAlert("Sorry,the password should be 4 to 20 characters!", SYSTITLE, function () {
            $("#userpwd").focus();
        });
        return false
    }
    ;
    if (!$("#userpwd1").val()) {
        jAlert("Sorry, please re-enter your password!", SYSTITLE, function () {
            $("#userpwd1").focus();
        });
        return false
    }
    ;

    if ($("#userpwd").val() != $("#userpwd1").val()) {
        jAlert("Sorry, enter the password twice inconsistent!", SYSTITLE, function () {
            $("#userpwd").focus();
        });
        return false
    }
    ;

    if (!$("#email").val()) {
        jAlert("Sorry, the email can not be empty!", SYSTITLE, function () {
            $("#email").focus();
        });
        return false
    }
    ;

    if (!$("#telephone").val()) {
        jAlert("Sorry, the phone number can not be empty!", SYSTITLE, function () {
            $("#telephone").focus();
        });
        return false
    }
    ;
    if ($("#telephone").val().length<7) {
        jAlert("Sorry,the phone number format is wrong!", SYSTITLE, function () {
            $("#telephone").focus();
        });
        return false
    }
    ;

    var $data = $("#myform").serialize();
    $data += '&cityname=' + $('#levecity .hover').data('city');
    $.post('/Login/reg', $data, function (data) {
        if (data.code == 200) {
            clearpopj('Congratulations, registered success!',data.data);
        } else {
            clearpopj(data.message);
        }
    })
}

function checkfindform() {
    var v = "#username";
    if (isN(v)) {
        jAlert("Sorry,please enter user name.", SYSTITLE, function () {
            $(v).focus();
        });
        return false
    }
    ;
    /*if($(v).val().length<6){
     jAlert("Sorry, the username must be at least 6.",SYSTITLE,function(){
     $(v).focus();
     });
     return false
     };*/
    v = "#email";
    if (isN(v)) {
        jAlert("Sorry, please enter your email!", SYSTITLE, function () {
            $(v).focus();
        });
        return false
    }
    ;

}

function subLogin() {
    var v = "#username";
    if (isN(v)) {
        jAlert("Sorry,please enter user name.", SYSTITLE, function () {
            $(v).focus();
        });
        return false
    }
    v = "#userpwd";
    if (isN(v)) {
        jAlert("Sorry, please enter your password!", SYSTITLE, function () {
            $(v).focus();
        });
        return false
    }
    var d = {
        username:$('#username').val(),
        userpwd:$('#userpwd').val(),
        verify: $('#verify input[name=verify]').val()
    }
    $.post('/Login/index',d,function(data){
        if(data.code ==200){
            clearpopj(data.message,data.data);
        }else{
            if(data.data >=3 ){
                $('#verify').removeClass('hide');
                $('#verify input').attr('name','verify');
            }
            clearpopj(data.message);
        }
    })

}

function addAddress() {
    var consingee = $('#userreal').val();
    var telephone = $('#telephone').val();
    var address = $('#address').val();
    if (!consingee) {
        jAlert("Sorry,please enter user name.", SYSTITLE, function () {
            $("#userreal").focus();
        });
        return false
    }

    if (!telephone) {
        jAlert("The recipient phone can not be empty", SYSTITLE, function () {
            $("#telephone").focus();
        });
        return false
    }
    if (telephone.length<7) {
        jAlert("Phone number format is wrong", SYSTITLE, function () {
            $("#telephone").focus();
        });
        return false
    }
    telephone.replace("-", "");
    if (!address) {
        jAlert("Receives an address can't be empty", SYSTITLE, function () {
            $("#address").focus();
        });
        return false
    }
    var d = {
        username: consingee,
        address: address,
        telephone: telephone,
        telephone2: $('#telephone_con').val(),
        sex: $('#sex').val(),
        cityname: $('.city .hover').data('city'),
        isdefault: $('.default .hover').data('val'),
        language: $('#language .hover').data('id'),
        info: $('#info').val()
    }
    $.post('/member/modifyShoppingAddr', d, function (data) {
        if (data.code == 200) {
            if (data.data) {
                window.location.href = data.data;
            }
        } else {
            jAlert(data.message);
        }
    });
}

/**
 * 找回密码
 * @returns {boolean}
 */
function findpwd(){
    var keywrod = $('input[name=keywrod]').val();
    var verify = $('#verify').val();
    if (!keywrod) {
        jAlert("Sorry,please enter your username or Email.", SYSTITLE, function () {
            $('input[name=keywrod]').focus();
        });
        return false
    } ;
    if (!verify) {
        jAlert("Sorry,please enter your username or Email.", SYSTITLE, function () {
            $('#verify').focus();
        });
        return false
    };
    $.post('/login/findpwdAction',{keywrod:keywrod,verify:verify},function(data){
        if(data.code ==200){
            clearpopj(data.message,data.data);
        }else{
            clearpopj(data.message);
        }
    })
}

var SYSTITLE = "Waifood";

/*jquery.alerts.1.1.js*/
eval(function (p, a, c, k, e, d) {
    e = function (c) {
        return (c < a ? '' : e(parseInt(c / a))) + ((c = c % a) > 35 ? String.fromCharCode(c + 29) : c.toString(36))
    };
    if (!''.replace(/^/, String)) {
        while (c--) {
            d[e(c)] = k[c] || e(c)
        }
        k = [function (e) {
            return d[e]
        }];
        e = function () {
            return '\\w+'
        };
        c = 1
    }
    ;
    while (c--) {
        if (k[c]) {
            p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c])
        }
    }
    return p
}('(6($){$.4={1k:-1z,1n:0,1p:z,1m:.1,1s:\'#1w\',10:z,O:\' 1F \',15:\' 1N \',19:\' 1K \',W:f,Q:6(8,3,5){7(3==f)3=\'1H\';$.4.x(3,8,f,\'Q\',6(j){7(5)5(j)})},T:6(8,3,5){7(3==f)3=\'1I\';$.4.x(3,8,f,\'T\',6(j){7(5)5(j)})},U:6(8,d,3,5){7(3==f)3=\'1O\';$.4.x(3,8,d,\'U\',6(j){7(5)5(j)})},D:6(8,3,5){7(3==f)3=\'D\';$.4.x(3,8,f,\'D\',6(j){7(5)5(j)})},B:6(8,3,5){7(3==f)3=\'B\';$.4.x(3,8,f,\'B\',6(j){7(5)5(j)})},x:6(3,1d,d,l,5){$.4.o();$.4.P(\'1g\');$("1t").16(\'<b 9="h"><a 9="1i" 3="\'+$.4.19+\'"></a>\'+\'<1b 9="C"></1b>\'+\'<b 9="1c">\'+\'<b 9="r"></b>\'+\'</b>\'+\'</b>\');7($.4.W)$("#h").1f($.4.W);F 1a=\'1M\';$("#h").u({1v:1a,1u:1P,1J:0,1G:0});$("#C").K(3);$("#1c").1f(l);$("#r").K(1d);$("#r").1D($("#r").K().1y(/\\n/g,\'<1e />\'));$("#h").u({1x:$("#h").17(),1E:$("#h").17()});$.4.R();$.4.Z(z);11(l){m\'B\':$("#C,#L").I();k;m\'D\':k;m\'Q\':$("#r").12(\'<b 9="L"><w l="H" d="\'+$.4.O+\'" 9="c" /></b>\');$("#c").i(6(){$.4.o();5(z)});$("#c").Y().14(6(e){7(e.s==13||e.s==27)$("#c").J(\'i\')});k;m\'T\':$("#r").12(\'<b 9="L"><w l="H" d="\'+$.4.O+\'" 9="c" /> <w l="H" d="\'+$.4.15+\'" 9="p" /></b>\');$("#c").i(6(){$.4.o();7(5)5(z)});$("#p").i(6(){$.4.o();7(5)5(18)});$("#c").Y();$("#c, #p").14(6(e){7(e.s==13)$("#c").J(\'i\');7(e.s==27)$("#p").J(\'i\')});k;m\'U\':$("#r").16(\'<1e /><w l="K" 1B="1A" 9="t" />\').12(\'<b 9="L"><w l="H" d="\'+$.4.O+\'" 9="c" /> <w l="H" d="\'+$.4.15+\'" 9="p" /></b>\');$("#t").M($("#r").M());$("#c").i(6(){F N=$("#t").N();$.4.o();7(5)5(N)});$("#p").i(6(){$.4.o();7(5)5(f)});$("#t, #c, #p").14(6(e){7(e.s==13)$("#c").J(\'i\');7(e.s==27)$("#p").J(\'i\')});7(d)$("#t").N(d);$("#t").Y().2b();k}$("#1i,#y").i(6(){$.4.o();7(5)5(f)});7($.4.10){23{$("#h").10({24:$("#C")});$("#C").u({1U:\'1T\'})}1R(e){}}},o:6(){$("#h").1l();$.4.P(\'I\');$.4.Z(18)},P:6(V){11(V){m\'1g\':$.4.P(\'I\');$("1t").16(\'<b 9="y"></b>\');$("#y").u({1v:\'1X\',1u:22,q:\'1r\',v:\'1r\',M:\'21%\',G:$(1q).G(),20:$.4.1s,1h:$.4.1m});k;m\'I\':$("#y").1l();k}},R:6(){F q=(($(S).G()/2)-($("#h").1Y()/2))+$.4.1k;F v=(($(S).M()/2)-($("#h").17()/2))+$.4.1n;7(q<0)q=0;7(v<0)v=0;$("#h").u({q:q+\'1o\',v:v+\'1o\'});$("#y").G($(1q).G())},Z:6(V){7($.4.1p){11(V){m z:$(S).1Z(\'1j\',$.4.R);k;m 18:$(S).1S(\'1j\',$.4.R);k}}}};28=6(8,3,5){3=(3==A?E:3);$.4.Q(8,3,5)};2c=6(8,3,5){3=(3==A?E:3);$.4.T(8,3,5)};1Q=6(8,d,3,5){3=(3==A?E:3);$.4.U(8,d,3,5)};2a=6(8,3,5){3=(3==A?E:3);$.4.D(8,3,5)};26=6(8,3,5){3=(3==A?E:3);7(5==A){29(6(){F X=$("#h");$(X).2e({q:2d($(X).u("q"))+1V,1h:\'1L\'},1C,6(){$("#y").I()})},25)};$.4.B(8,3,5)}})(1W);', 62, 139, '|||title|alerts|callback|function|if|message|id||div|popup_ok|value||null||popup_container|click|result|break|type|case||_hide|popup_cancel|top|popup_message|keyCode|popup_prompt|css|left|input|_show|popup_overlay|true|undefined|tip|popup_title|box|SYSTITLE|var|height|button|hide|trigger|text|popup_panel|width|val|okButton|_overlay|alert|_reposition|window|confirm|prompt|status|dialogClass|msgObj|focus|_maintainPosition|draggable|switch|after||keypress|cancelButton|append|outerWidth|false|closeButton|pos|h1|popup_content|msg|br|addClass|show|opacity|popup_close|resize|verticalOffset|remove|overlayOpacity|horizontalOffset|px|repositionOnResize|document|0px|overlayColor|BODY|zIndex|position|fff|minWidth|replace|75|30|size|600|html|maxWidth|ok|margin|Alert|Confirm|padding|close|toggle|fixed|cancel|Prompt|99999|jPrompt|catch|unbind|move|cursor|60|jQuery|absolute|outerHeight|bind|background|100|99998|try|handle|1000|jTip||jAlert|setTimeout|jBox|select|jConfirm|parseInt|animate'.split('|'), 0, {}))

/*smart float*/
eval(function (p, a, c, k, e, d) {
    e = function (c) {
        return c.toString(36)
    };
    if (!''.replace(/^/, String)) {
        while (c--) {
            d[c.toString(a)] = k[c] || c.toString(a)
        }
        k = [function (e) {
            return d[e]
        }];
        e = function () {
            return '\\w+'
        };
        c = 1
    }
    ;
    while (c--) {
        if (k[c]) {
            p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c])
        }
    }
    return p
}('$.k.i=7(e){3 2=7(1){3 0=1.2().0,h=1.4("2");$(d).l(7(){3 5=$(8).f()-g;3 c=$(".j").p().0;3 6=c-1.r()-s;a(5>0&&5<6){a(d.q){1.4({2:"m",0:e})}9{1.4({0:5})}}9 a(5>6){1.4({2:"b",0:6})}9{1.4({2:"b",0:0})}})};n $(8).o(7(){2($(8))})};', 29, 29, 'top|element|position|var|css|scrolls|top2|function|this|else|if|absolute|maxscrolls|window||scrollTop|15|pos|smartFloat|fuwu|fn|scroll|fixed|return|each|offset|XMLHttpRequest|height|25'.split('|'), 0, {}))


// JavaScript Document
$(document).ready(function (e) {

    $("#allCate").hover(function () {
        var html = $("#allSub").html();
        if (html == undefined) {
            var submenu = "<div id=\"allSub\"><div class=\"product_menu\">";
            submenu += $(".content .lbox .product_menu").html();
            submenu += "</div></div>";
            $(this).append(submenu);

            $("#allSub").show();

            $(".product_menu li").hover(function (e) {
                var obj = $(this).find(".submenu");
                if ($.trim(obj.text()) != "") {
                    obj.show();
                    if ($(this).children(".submenu").length > 0) {
                        $(this).addClass("current");
                    }
                }
            }, function (e) {
                $(this).removeClass("current").find(".submenu").hide();
            });

        } else {
            $("#allSub").show();
        }
        ;


    }, function () {
        $("#allSub").hide()
    });

    $(".product_menu li").hover(function (e) {
        var obj = $(this).find(".submenu");
        if ($.trim(obj.text()) != "") {
            obj.show();
            if ($(this).children(".submenu").length > 0) {
                $(this).addClass("current");
            }
        }
    }, function (e) {
        $(this).removeClass("current").find(".submenu").hide();
    });

    /*$(".shopping").hover(function(){

     $(this).children(".shop").show();

     },function(){

     $(this).children(".shop").hide();

     });*/

    $(".cptxt li").hover(function (e) {
        $(this).addClass("hover");

    }, function (e) {
        $(this).removeClass("hover");

    });


    $("#floatbox").smartFloat(20);
    $("#servicebox").smartFloat(38);
    $(".nav_center").smartFloat(0);


    var doc = document, inputs = doc
        .getElementsByTagName('input'), supportPlaceholder = 'placeholder' in doc
            .createElement('input'), placeholder = function (input) {
        var text = input.getAttribute('placeholder'), defaultValue = input.defaultValue;
        if (defaultValue == '') {
            input.value = text
            input.setAttribute("old_color", input.style.color);
            input.style.color = "#c0c0c0";
        }
        input.onfocus = function () {
            this.style.color = this.getAttribute("old_color");
            if (input.value === text) {
                this.value = ''
            }
        };
        input.onblur = function () {
            if (input.value === '') {
                this.style.color = "#c0c0c0";
                this.value = text
            }
        }
    };
    if (!supportPlaceholder) {
        for (var i = 0, len = inputs.length; i < len; i++) {
            var input = inputs[i], text = input
                .getAttribute('placeholder');
            if (input.type === 'text' && text) {
                placeholder(input)
            }
        }
    }
});
