$(function(){
    var pagename = $("body").attr('pagename');
    switch(pagename){
        case "orderView":{paymethod();break}
        case "order":{paymethod();break}
    }
    loadGood();
    modelBox();
})



/**
 * 弹出框
 */
function modelBox(){
    if(!$('body').find('.lean_overlay').data('show')){
        var $html = '<div class="lean_overlay hide" data-show="1"></div><div id="showQr-box" style="display: ; padding: 2px;" class="hide leanModal" ><img src="/Public/Shop/images/qr.jpg" width="150" style=" margin-top: 5px;" alt=""/></div>'; //二维码
        $html += '<div id="showAddr"  style="width: 200px;" class="hide leanModal"> <div class="addr" onclick="setHeadAddr(\'Chengdu\')">Chengdu</div> <div class="addr"  onclick="setHeadAddr(\'Chongqing\')">Chongqing</div><div  class="addr" onclick="setHeadAddr(\'Xian\')" >Xi\'an</div><div class="addr" onclick="setHeadAddr(\'Kunming\')">Kunming</div><div class="addr" onclick="setHeadAddr(\'Other\')">Other</div></div>';//地址
        $('body').append($html);
    }

    $('.lean_overlay').click(function(){
        if($(this).css('display') !='none'){
            $(this).hide();
            $('.leanModal').hide();
        }
    })
    $('#showQr').click(function(){
        $('.lean_overlay').show();
        $('#showQr-box').show();
    })

    $('#choosHeadAddr').click(function(){
        $('.lean_overlay').show();
        $('#showAddr').show();
    })
    $('#showAddr .addr').click(function(){
        $('.lean_overlay').hide();
        $('.leanModal').hide();
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
    }
    $('.lean_overlay').show();
}

function gopay(obj){
    var $paymethod = $(obj).data('val');
    var orderno =  $('#paymethod').data('id');
    $.post('/home/order/modifyOrderPaymethod',{orderno:orderno,paymethod:$paymethod},function(data){
        if(data.code ==200){
            if($paymethod ==2){ //Paypal支付
                clearpopj('Modify payment success',"/m_pay?orderno="+orderno)
            }else{
                $('.lean_overlay').hide();
                $('.leanModal').hide();
                clearpopj('Modify payment success')
            }
        }else{
            clearpopj('Modify payment failure')
        }
    })
}

function getStockCount(){
    var myfood = $.cookie("myfood");
    if (myfood) {
        var obj = $.parseJSON(myfood);
        for (var i in obj) {
            if(obj[i]['id']){
                console.log(obj[i]['id'])
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
    if(number>0 && id >0){
        var key = "myfood",
            myfood = $.cookie(key),
            myfood_array = {};
         myfood_array = $.parseJSON(myfood);
        // 看该产品是否存在
        if(myfood_array && myfood_array[id]){
            if(myfood_array[id]){
                var single = myfood_array[id];
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
        stock = $('#js_goods_'+id).data("stock");

        if(stock<1){ //没有库存
            jAlert("Insufficient stock!",SYSTITLE);
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
                myfood_array[id] = {"id":id,"name":name,"price":price,"amount":1,"indexpic":indexpic};
            }
        }else{
            myfood_array = {};
            myfood_array[id] = {"id":id,"name":name,"price":price,"amount":1,"indexpic":indexpic};
        }
        console.log( myfood_array[id]['amount'])
        console.log(stock)
        if( myfood_array[id]['amount'] > stock){ //库存不足
            jAlert("Insufficient stock!",SYSTITLE);
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
    $.cookie("myfood", "", {"path": "/"});
}

function fly(event){
    if($("#CartNo").css('display') == 'none'){
        var offset = $("#offset").offset();
    }else{
        var offset = $("#CartNo").offset();
    }

    var flyer = $('<img width="30px;" class="u-flyer" src="http://www.waifood.com/Public/Home/images/flycart.png">');
    flyer.fly({
        start: {
            left:  event.clientX,
            top: event.clientY
        },
        end: {
            left: offset.left+3,
            top: offset.top+3,
            width: 0,
            height: 0
        },
        onEnd: function(){
        }
    });
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
                    amount+=parseInt(obj[i]['amount']);
                    if($('#js_goods_num_'+obj[i]['id']).attr('type') =='text'){
                        $('#js_goods_num_'+obj[i]['id']).val(obj[i]['amount']);
                    }else{
                        $('#js_goods_num_'+obj[i]['id']).html(obj[i]['amount']);
                    }
                    totalMoney +=  (obj[i]['amount'] *obj[i]['price']);
                    $('#js_goods_'+obj[i]['id']+' .js_total').html('&yen;'+parseFloat((obj[i]['amount']*obj[i]['price'])));
                    $('#CartNo').html(amount);
                    if(parseInt(obj[i]['amount'])<1){
                        $('#js_goods_'+obj[i]['id']).remove();
                    }
                     if($('body').attr('pagename') == 'cart'){ //当前购物车页面
                         //判断购物车库存
                         if(parseInt(obj[i]['stock']) >= parseInt(obj[i]['amount'])){
                             $('#js_goods_'+obj[i]['id']+' .cartptotal').html('Total: <span class="num-item js_total">&yen;' + (obj[i]['amount'] *obj[i]['price'])+ '</span>');
                         }else{
                             $('#js_goods_'+obj[i]['id']+' .cartptotal').html('out of stock');
                         }
                     }

                    //有商品数量的显示减号与商品数量
                    $('#js_goods_'+obj[i]['id'] +' .g_btn .cat_cart_num').removeClass('hide');
                    $('#js_goods_'+obj[i]['id'] +' .g_btn .num').removeClass('hide');
                }
            }
            if(!amount){
                $('#CartNo').css('display','none');
                $('#cart_foot').css('display','none');
                $('#submit-button').css('display','none');
            }
            $('#cart_foot .totalMoney').html('&yen;'+totalMoney);
        }
    }else{
        $('#CartNo').css('display','none');
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
            if(parseInt(obj[i]['amount'])>parseInt(obj[i]['stock'])){
                jAlert('Goods:'+obj[i]['name']+' Insufficient stock!',SYSTITLE);
                return false;
            }
        }
        window.location.href = '/m_cashier.html?gocashier=gocashier';
    }else{
        jAlert("Order content cannot be empty!",SYSTITLE);
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
        jAlert("Order content cannot be empty!",SYSTITLE);
        subBlock = false;//解除阻塞
        return false;
    }
    for(var i in myfood_array){
        if(parseInt(myfood_array[i]['amount'])>parseInt(myfood_array[i]['stock'])){
            jAlert('Goods:'+myfood_array[i]['name']+' Insufficient stock!',SYSTITLE);
            subBlock = false;//解除阻塞
            return false;
        }
        order +=  myfood_array[i]['id'] + "," + myfood_array[i]['amount']+ "," + myfood_array[i]['price'] +"|"; //订单信息
    }
    if (!$("#UseAddressID").val()) {
        jAlert("I'm sorry, please select a shipping address!",SYSTITLE);
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
            if(data.data){
                clearCart(); //清空购物车
                clearpopj(data.message,data.data,SYSTITLE)
            }
        }else{
            clearpopj(data.message,'',SYSTITLE)
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
function getAmountMoney(totalMoney,obj,deliveryFee){
    $.post('/home/shop/getdeliveryFee.html',{money:totalMoney},function(data){
        if(data.code == 200){
            $(obj).html('&yen;'+data.data);
            $(deliveryFee).html('&yen;'+data.data);
            var $total = parseFloat(totalMoney)+parseFloat(data.data)
            $(obj).html('&yen;'+$total);//总金额= 配送费+商品总金额
        }
    })
}
 function getCartGoodStock(){
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
                                 myfood_array[obj[i]['id']]['id'] = obj[i]['id'];
                                 myfood_array[obj[i]['id']]['name'] = obj[i]['title'];
                                 myfood_array[obj[i]['id']]['price'] = obj[i]['price'];
                                 myfood_array[obj[i]['id']]['indexpic'] = obj[i]['indexpic'];
                                 myfood_array[obj[i]['id']]['stock'] = obj[i]['stock'];
                             }
                         }
                         var json = $.toJSON(myfood_array);
                         $.cookie(key,json,{
                             "path":"/"
                         });
                     }
                 })
             }
         }
     }
};