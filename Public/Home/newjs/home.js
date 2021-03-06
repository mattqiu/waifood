$(function(){
    var pagename = $("body").attr('pagename');
    switch(pagename){
        case "orderView":{paymethod();break}
        case "order":{paymethod();break}
        case "cart":{ getCartGoodStock('iscart');break}
    }
    loadGood();
    modelBox();
    window.weixinJs=function(){
        wx.share={
            title:" Waifood | Online supermarkets with authentic western foods",
            img:"http://www.waifood.com/Public/Home/images/logo_small.jpg?1",
            link:"http://www.waifood.com/?to=share",
            desc:"Home Delivery ,English Service"
        };
        wx.wxshare();
    }
})

function isNumber (x) {
    if (typeof x === 'number') return true;
    if (/^0x[0-9a-f]+$/i.test(x)) return true;
    return /^[-+]?(?:\d+(?:\.\d*)?|\.\d+)(e[-+]?\d+)?$/.test(x);
}

/**
 * 弹出框
 */
function modelBox(){
    if(!$('body').find('.lean_overlay').data('show')){
        var $html = '<div class="lean_overlay hide" data-show="1"></div><div id="showQr-box" style="display: none; padding: 2px;margin-left:-77px;margin-top: -81px;" class="hide leanModal" ><img src="/Public/images/qr.jpg" width="150" style=" margin-top: 5px;" alt=""/></div>'; //二维码
        $html += '<div id="attention" style="width: 95%" class="hide leanModal" ><img src="/Public/Home/images/attention.png" width="100%" alt=""/></div>'; //关注
        $html += '<div id="showAddr"  style="width: 200px;margin-left: -100px;margin-top: -115px;" class="hide leanModal"> <div class="addr" onclick="setHeadAddr(\'Chengdu\')">Chengdu</div> <div class="addr"  onclick="setHeadAddr(\'Chongqing\')">Chongqing</div><div  class="addr" onclick="setHeadAddr(\'Xian\')" >Xi\'an</div><div class="addr" onclick="setHeadAddr(\'Kunming\')">Kunming</div><div class="addr" onclick="setHeadAddr(\'Other\')">Other</div></div>';//地址
        $('body').append($html);
    }

    if(getUrlParam('to') == 'share'){
        $('#attention').css({'background':'none','margin-left':-(parseInt($('#attention').width())/2)+'px','margin-top':-(parseInt($('#attention').width())/2+30)+'px'})
        $('.lean_overlay').show();
        $('#attention').show();
    }

    $('.lean_overlay').click(function(){
        //if($(this).css('display') !='none'){
        $('#showQr-box').hide();
        $('#attention').hide();
        $('#showAddr').hide();
        $('.leanModal').hide();
        $('.lean_overlay').hide();
        //}
    })
    $('#showQr').click(function(){
        $('.leanModal').css({'background':'#FFFFFF'});
        $('.lean_overlay').show();
        $('#showQr-box').show();
        $('#showAddr').hide();
        $('#attention').hide();
    })

    $('#choosHeadAddr').click(function(){
        $('.leanModal').css({'background':'#FFFFFF','right':'25%'});
        $('.lean_overlay').show();
        $('#showAddr').show();
        $('#attention').hide();
        $('#showQr-box').hide();
    })
    $('#showAddr .addr').click(function(){
        $('.lean_overlay').hide();
        $('.leanModal').hide();
        $('#attention').hide();
    })
}
/**
 * 加载支付方式列表到页面
 */
function paymethod(){
    var $html = '<div class="radio-box leanModal hide" id="paymethod" >';
        $html += '<label class="radio on paymethod" data-val="4" onclick="gopay(this)"><i></i>Cash on delivery</label>';
        $html += ' <p></p>';
        $html += '<label class="radio paymethod paypal" data-val="2"  onclick="gopay(this);"><i></i>Paypal(USD)</label>';
        $html += ' <p></p>';
        $html += '<label class="radio paymethod wechat" data-val="5"  onclick="gopay(this);"><i></i>WeChat Pay</label>';
        $html += ' <input type="hidden" name="paymethod" id="paymethod" value="4" />';
        $html += ' </div>';
       $('body').append($html);
}
/**
 * 点击支付方式
 * @param obj
 */
function showPaymethod(obj){
    $('#paymethod').show();
    $('#paymethod').attr('data-id',$(obj).data('id'));
    if( $(obj).data('paymethod') ==2){
        $('#paymethod .paymethod').removeClass('on');
        $('#paymethod .paypal').addClass('on');
    }else if( $(obj).data('paymethod') ==5){
        $('#paymethod .paymethod').removeClass('on');
        $('#paymethod .wechat').addClass('on');
    }
    $('.lean_overlay').show();
}

function gopay(obj){
    var $paymethod = $(obj).data('val');
    var orderno =  $('#paymethod').data('id');
    $.post('/home/order/modifyOrderPaymethod',{orderno:orderno,paymethod:$paymethod},function(data){
        if(data.code ==200){
            if($paymethod ==2){ //Paypal支付
                window.location.href="/m_pay?orderno="+orderno
            }else if($paymethod ==5){ //Paypal支付
                window.location.href="/index/payWeixin.html?orderno="+orderno
            }else{
                $('.lean_overlay').hide();
                $('.leanModal').hide();
                clearpopj('Successful.','success',true,'self');
            }
        }else{
            clearpopj('Modify payment failed','error',true);
        }
    })
}

function getStockCount(){
    var myfood = $.cookie("myfood");
    if (myfood) {
        var obj = $.parseJSON(myfood);
        for (var i in obj) {
            if(obj[i]['id']){
               var stock = $('#js_goods_'+obj[i]['id']).data("stock");
                myfood[obj[i]['id']]['stock'] = stock;
            }
        }
    }
    var json = $.toJSON(myfood_array);
    $.cookie(key,json,{
        "path":"/"
    });
}

function setGoodNum(id,number){
    number = parseInt(number);
    if(number>0 && id >0){
        var key = "myfood",
            myfood = $.cookie(key),
            myfood_array = {};
         myfood_array = $.parseJSON(myfood);
        // 看该产品是否存在

        if(myfood_array && myfood_array[id]){
            if(myfood_array[id]){
                if(number > myfood_array[id]['stock']){ //如果输入的数字大于库存，默认库存
                    number = myfood_array[id]['stock'];
                }
                myfood_array[id]['amount'] = number;
            }
            var json = $.toJSON(myfood_array);
            $.cookie(key,json,{
                "path":"/"
            });
            loadGood();
        }
    }
}

/**
 *添加商品
 * @param id
 * @param event
 * @returns {boolean}
 */
function addgood(id,event){
    var key = "myfood",
        myfood = $.cookie(key),
        id = $('#js_goods_'+id).data("id"),
        price = $('#js_goods_'+id).data("price"),
        indexpic = $('#js_goods_'+id).data("indexpic"),
        name = $('#js_goods_'+id).data("name"),
        negative = $('#js_goods_'+id).data("negative"),
        stock = $('#js_goods_'+id).data("stock");
        if(stock<1 && negative!=1){ //没有库存(可负除外)
            clearpopj("Insufficient stock!",'error',true);
            return false;
        }
        var myfood_array = {};
        myfood_array = $.parseJSON(myfood);
        // 看该产品是否存在
        if(myfood_array){
            if(myfood_array[id]){
                var single = myfood_array[id];
                myfood_array[id]['amount'] = parseInt(single['amount']) + 1;
            }else{
                myfood_array[id] = {"id":id,"name":name,"price":price,"amount":1,"indexpic":indexpic,"negative":negative};
            }
        }else{
            myfood_array = {};
            myfood_array[id] = {"id":id,"name":name,"price":price,"amount":1,"indexpic":indexpic,"negative":negative};
        }
        if( myfood_array[id]['amount'] > stock && negative!=1){ //库存不足
            clearpopj("Insufficient stock!",'error',true);
            return false;
        }
        $('#CartNo').css('display','block');
        var json = $.toJSON(myfood_array);
        $.cookie(key,json,{
            "path":"/"
        });
        fly(event);
        loadGood();

}

/**
 * 减少商品
 * @param id
 */
function prepGood(id){
    var key = "myfood",
        myshop = $.cookie(key);
    var myshop_array = {};
    if(myshop){
        myshop_array = $.parseJSON(myshop);
        if(myshop_array[id]){   //存在
            var single = myshop_array[id];
            if( myshop_array[id]['amount'] >1){
                myshop_array[id]['amount'] = parseInt(single['amount']) - 1;
            }else{
                if( $('#js_goods_'+id).hasClass('jsCart')){
                    $('#js_goods_'+id).remove();
                }else{
                    $('#js_goods_'+id +' .g_btn .cat_cart_num').addClass('hide');
                    $('#js_goods_'+id +' .g_btn .num').addClass('hide');
                }
                myshop_array[id]= undefined;
            }
        }else{
            if( $('#js_goods_'+id).hasClass('jsCart')){
                $('#js_goods_'+id).remove();
            }
            myshop_array[id]= undefined;
        }
    }
    var json = $.toJSON(myshop_array);
    $.cookie(key,json,{
        "path":"/"
    });
    loadGood();
}

/**
 * 删除购物车指定商品
 */
function delGood(id){
    var key = "myfood";
    var myshop = $.cookie(key);
    var myshop_array = {};
    if(myshop){
        myshop_array = $.parseJSON(myshop);
        if(myshop_array[id]){//存在
            myshop_array[id]= undefined;
        }
    }
    var json = $.toJSON(myshop_array);
    $.cookie(key,json,{
        "path":"/"
    });
    loadGood();
    $('#js_goods_'+id).remove();
}

/**
 * 清空购物车
 */
function clearCart(){
    $('#content').remove();
    $('#itemg-title').addClass('hide');
    $('#emptycart').removeClass('hide');
    $('#CartNo').css('display','none');
    $('#cart_foot .totalMoney').html('&yen;0');
    $.cookie("myfood", null, {"path": "/"});
}

function fly(event){
    var offset = $("#CartNo").offset();
    var $obj = $('<img width="30px;" class="u-flyer" src="http://www.waifood.com/Public/Home/images/flycart.png">');
    $obj.css({
        'width':'30px',
        'height':'30px',
        'position': 'fixed',
        'top': parseInt(event.clientY) +'px',
        'left':  parseInt(event.clientX) +'px',
        'z-index': '5'
    });
    $('body').append($obj);
    $obj.animate({
        top:$(window).height(),
        left:offset.left,
        width:0,
        height:0
    },'slow');
}

/*加载缓存中的购物车数据到页面*/
function loadGood(){
    var myfood =  $.cookie("myfood"),amount= 0,totalMoney= 0;
    if(myfood){
        var obj = $.parseJSON(myfood);
        $('#CartNo').css('display','block');
        if(obj){
            for(var i in obj) {
                if(obj[i]['id']){
                    if($('#js_goods_num_'+obj[i]['id']).attr('type') =='text'){
                        $('#js_goods_num_'+obj[i]['id']).val(obj[i]['amount']);
                    }else{
                        $('#js_goods_num_'+obj[i]['id']).html(obj[i]['amount']);
                    }

                    $('#js_goods_'+obj[i]['id']+' .js_total').html('&yen;'+parseFloat((obj[i]['amount']*obj[i]['price'])));

                    if(parseInt(obj[i]['amount'])<1){
                        $('#js_goods_'+obj[i]['id']).remove();
                    }

                    if($('body').attr('pagename') == 'cart'){ //当前购物车页面
                        if(obj[i]['status'] == 1){ //下架的不计数
                            amount+=parseInt(obj[i]['amount']);
                            totalMoney +=  (obj[i]['amount'] *obj[i]['price']);
                        }
                    }else{
                        totalMoney +=  (obj[i]['amount'] *obj[i]['price']);
                        amount+=parseInt(obj[i]['amount']);
                    }

                    //有商品数量的显示减号与商品数量
                    $('#js_goods_'+obj[i]['id'] +' .g_btn .cat_cart_num').removeClass('hide');
                    $('#js_goods_'+obj[i]['id'] +' .g_btn .num').removeClass('hide');
                }
            }
            $('#CartNo').html(amount);
            if(!amount){
                $('#CartNo').css('display','none');
                $('#cart_foot').css('display','none');
                $('#submit-button').css('display','none');
                $('#itemg-title').addClass('hide');
                $('#emptycart').removeClass('hide');
            }
            $('#cart_foot .totalMoney').html('&yen;'+totalMoney);
        }
    }else{
        $('#CartNo').css('display','none');
    }
}

function getCartData(){
    var myfood = $.cookie("myfood"), $html = '', totalMoney = 0, totalNum= 0;
    if (myfood) {
        var obj = $.parseJSON(myfood);
        if (obj) {
            for (var i in obj) {
                $html += '<div style="margin:5px auto" class="itemg-li jsCart" id="js_goods_' + obj[i]['id'] + '" data-id="' + obj[i]['id'] + '" data-price="' + obj[i]['price'] + '"  data-indexpic="' + obj[i]['indexpic'] + '" data-name="' + obj[i]['name'] + '" data-stock="' + obj[i]['stock'] + '" data-negative="' + obj[i]['negative'] + '">';
                $html += '<a href="/Product/view.html?id=' + obj[i]['id'] + '">';
                $html += '<div class="itemg-img fl tc">';
                $html += '<img alt="' + obj[i]['name'] + '" src="' + obj[i]['indexpic'] + '" width="100"/>';
                $html += '</div>';
                $html += '<div class="itemg-name fr">';
                $html += '<p style=" height: 39px;overflow: hidden;">' + obj[i]['name'] + '</p>';
                $html += '<p><span class="green num-item">&yen;' + obj[i]['price'] + '</span></p>';
                $html += '</div>';
                $html += '</a>';
                $html += '<div class="clr"></div>';
                $html += '<div class="item-foot tc ">';
                $html += '<div class="fl icon_trash _trash " onClick="delGood(' + obj[i]['id'] + ');"></div>';
                if(obj[i]['status'] ==1){
                    $html += '<div class=" fr item-foot-r">';
                    $html += '<div class="fl g_btn cartbtn" style="margin-top: 6px;">';
                    $html += '<div class="cat_cart_num fl " onclick="prepGood('+obj[i]['id']+')"></div>';
                    var goodsnum = 0;
                    if(parseInt(obj[i]['amount'])>parseInt(obj[i]['stock']) && obj[i]['stock'] !=1){ //库存小于当前购物车商品数量
                        goodsnum = parseInt(obj[i]['stock']);
                    }else{
                        goodsnum = parseInt(obj[i]['amount']);
                    }
                    $html += '<input type="text" class="num cartgoodnum fl tc" onkeyup="setGoodNum(' + obj[i]['id'] + ',$(this).val());"  data-id="' + obj[i]['id'] + '" id="js_goods_num_' + obj[i]['id'] + '" value="' + goodsnum + '"/>';
                    $html += '<div class="add_cart_num  fl" data-id="' + obj[i]['id'] + '" onClick="addgood(' + obj[i]['id'] + ',event);"></div>';
                    $html += '</div>';
                    $html += '<div class="fr ptotal cartptotal fc_orange" style="margin-top: 10px;font-size: 16px;">Total: <span class="num-item js_total">&yen;' + (goodsnum *obj[i]['price'])+ '</span></div>';

                    $html += '</div>';
                }else{
                    $html += '<div class=" fr item-foot-r fc_orange" style="text-align: left;width: 65%">out of stock</div>';
                }

                $html += '</div>';
                $html += '<div class="clr"></div>';
                $html += '<hr width="108%" color="#cccccc" size="1px" style="margin-left: -2%; "/>';
                $html += '</div>';
                $('#content').html($html);

                if(obj[i]['status'] == 1){ //下架的不计数
                    totalMoney +=  parseFloat(obj[i]['amount'] *obj[i]['price']);
                    totalNum +=  obj[i]['amount'];
                }

            }
        }
        $('#itemg-title').removeClass('hide');
        $('#emptycart').addClass('hide');
        $('#cart_foot').show();
        $('#cart_foot .totalMoney').html('&yen;'+totalMoney);
    }else{
        $('#itemg-title').addClass('hide');
        $('#emptycart').removeClass('hide');
        $('#cart_foot').addClass('hide');
    }
}

/**
 * 选择头部地址
 */
function setHeadAddr(addr){
    if(addr == 'Xian'){
        addr = "Xi'an";
    }
    setCookie('headaddr',addr);
   $('#headaddr').html(addr);
}


/**
 * 进入结算页面
 * @returns {boolean}
 */
function goCashier(){
    var myfood =  $.cookie("myfood");
    var obj = $.parseJSON(myfood);
    if(obj){
        for(var i in obj){
            if(parseInt(obj[i]['amount'])>parseInt(obj[i]['stock']) && obj[i]['negative']!=1){
                clearpopj('Insufficient stock for '+obj[i]['name'],'error',true);
                return false;
            }
        }
        window.location.href = '/m_cashier.html?gocashier=gocashier';
    }else{
        clearpopj("No products to check out!", "error",true);
        return false;
    }
}

/*阻塞标志，防止重复下单；预设不阻塞*/
window.subBlock=false;
/**
 * 提交订单
 * @returns {boolean}
 */
function submitOrder(){
    if(subBlock){
        return false;
    }
    var myfood =  $.cookie("myfood"),totalMoney= 0,delivery_fee =parseFloat($('#delivery_fee').html()),deliverydate= $('#delivertimeselect').val()+' ',order='';
    var myfood_array = $.parseJSON(myfood);
    if(!myfood_array){
        clearpopj("No products to check out!", "error",true);
        subBlock = false;//解除阻塞
        return false;
    }
    for(var i in myfood_array){
        if(parseInt(myfood_array[i]['amount'])>parseInt(myfood_array[i]['stock']) && myfood_array[i]['negative']!=1){
            clearpopj(' Insufficient stock for'+myfood_array[i]['name'],'error',true);
            subBlock = false;//解除阻塞
            return false;
        }
        if(myfood_array[i]['status'] == 1){
            order +=  myfood_array[i]['id'] + "," + myfood_array[i]['amount']+ "," + myfood_array[i]['price'] +"|"; //订单信息
        }
    }
    if (!$("#UseAddressID").val()) {
        clearpopj('Please select a shipping address!', "error",true);
        subBlock = false;//解除阻塞
        return false
    };
    $(".click input[type=hidden]").each(function () {
        deliverydate += $(this).val()+' ';
    })
    var data = {
        shop_id : $("#shop_id").val(),
        UseAddressID : $("#UseAddressID").val(),
        cityname : $("#cityname").val(),
        paymethod : $('#paymethod').val(),
        invoice : $('#invoice').val(),
        info : $.trim($("#info").val()),
        delivertime : deliverydate,
        order : order
    }
    loading();
    subBlock = true;
    $.post('/home/order/submitOrder.html',data,function(data){
        subBlock = false;//解除阻塞
        closeLoad();
        if(data.code == 200){
            clearCart(); //清空购物车
            if(data.data){
                clearpopj(data.message, "success",true,data.data);
            }
        }else{
            clearpopj(data.message,'error',true)
            return false;
        }
    })
}

/**
 * 获取配送费
 * @param money
 * @param obj
 */
function getdeliveryFee(money,obj){
    $.post('/home/shop/getdeliveryFee.html',{money:money},function(data){
        if(data.code == 200){
            $(obj).html('&yen;'+data.data)
            $(obj).attr('deliveryFee',data.data)
        }
    })
}

/**
 * 获取订单总金额
 * @param totalMoney
 * @param obj
 * @param deliveryFee
 */
function getAmountMoney(totalMoney,allMoneyobj,deliveryobj,discountobj,city){
    var discount = getDiscount(totalMoney,city),discountmoney=0,dischtml= ' <div id="disc">';
    var delivery_fee = getdeliveryFee(totalMoney);
	if(!delivery_fee){
	    delivery_fee = 0;
    }
    if(discount['money']){
        discountmoney = discount['money'];
    }
    var allMoney = (totalMoney-parseFloat(discountmoney))+parseFloat(delivery_fee);
    for (var i in discount){
        if(regex(i,'number')){
            dischtml+=discount[i]['name']+':&nbsp;-&nbsp;&yen;'+fomatFloat(discount[i]['money'])+'&nbsp;&nbsp;&nbsp;&nbsp;' ;
    }
    }
    dischtml+='</div><div id="disc2"></div>'
    if(discount){
        $('#disc-box').show();
        $('#disc-box').append(dischtml);
        discGD();
    }
    //if(discount>0){
    //    var discountplan =discount;
    //}else{
    //    var discountplan =40;
    //}
    $(deliveryobj).html('&yen;'+fomatFloat(delivery_fee));
    //$('#discountplan').html('&yen;'+discountplan);
    $(discountobj).html('-&yen;'+fomatFloat(discountmoney));
    $(allMoneyobj).html('&yen;'+fomatFloat(allMoney));//总金额= 配送费+商品总金额

}

 function getCartGoodStock($obj){
     var myfood = $.cookie("myfood"), $goodIds='', key = "myfood";
     if (myfood) {
         var myfood_array = $.parseJSON(myfood);
         if (myfood_array) {
             for (var i in myfood_array) {
                 $goodIds += ',' + myfood_array[i]['id'];
             }
             if($goodIds){
                 $.post('/home/cart/getCartGoodStock.html',{goodIds:$goodIds},function(data){
                     if(data.code ==200){
                         var obj = data.data;
                         for (var i in obj) {
                             if(obj[i]['id']){
                                 if( obj[i]['negative']!=1 && parseInt(myfood_array[obj[i]['id']]['amount']) > parseInt(obj[i]['stock'])) { //库存小于当前购物车商品数量
                                     myfood_array[obj[i]['id']]['amount'] = obj[i]['stock'];
                                 }
                                 myfood_array[obj[i]['id']]['id'] = obj[i]['id'];
                                 myfood_array[obj[i]['id']]['name'] = obj[i]['title'];
                                 myfood_array[obj[i]['id']]['price'] = obj[i]['price'];
                                 myfood_array[obj[i]['id']]['indexpic'] = obj[i]['indexpic'];
                                 myfood_array[obj[i]['id']]['stock'] = obj[i]['stock'];
                                 myfood_array[obj[i]['id']]['status'] = obj[i]['status'];
                                 myfood_array[obj[i]['id']]['negative'] = obj[i]['negative'];
                             }
                         }
                         var json = $.toJSON(myfood_array);
                         $.cookie(key,json,{
                             "path":"/"
                         });
                     }  if($obj=='iscart'){
                         getCartData();
                     }
                 })

             }

         }
     }
     $('#itemg-title').addClass('hide');
};

function cancelOrder(orderno){
    var title = "Are you sure you want to cancel the order?";
    swal({
        title: '',
        text: title,
        type: 'warning',
        showCancelButton: true,
        closeOnConfirm: false,
        confirmButtonText: "Yes",
        //confirmButtonColor: "#35D374"
    }, function() {
        $.post('/Home/Order/cancelOrder.html',{orderno:orderno},function(data){
            if(data.code==200){
                clearpopj(data.message,'success',true,'self');
            }else{
                clearpopj(data.message,'error',true);
            }
        })
    });

    return false;
}
