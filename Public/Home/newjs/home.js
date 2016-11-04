$(function(){
    loadGood();
})

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

        if( myfood_array[id]['amount'] > stock){ //库存不足
            jAlert("Insufficient stock!",SYSTITLE);
            return false;
        }
        fly(event);
        var json = $.toJSON(myfood_array);
        $.cookie(key,json,{
            "path":"/"
        });
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
                 myshop_array[id]= undefined;
                $('#js_goods_'+id).remove();
            }
        }else{
            jAlert("Sorry, improve plate does not exist, please refresh retry!",SYSTITLE);
            return false;
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
    $('#js_goods_'+id).remove();
}

/**
 * 清空购物车
 */
function clearCart(){
    $.cookie("myfood", "", {"path": "/"});
}

function fly(event){
    var offset = $("#CartNo").offset();
    var flyer = $('<img width="30px;" class="u-flyer" src="http://www.waifood.com/public/home/images/flycart.png">');
    flyer.fly({
        start: {
            left:  event.clientX,
            top: event.clientY
        },
        end: {
            left: offset.left+1,
            top: offset.top+1,
            width: 0,
            height: 0
        },
        onEnd: function(){
        }
    });
}


/*加载缓存中的购物车数据到页面*/
function loadGood(){
    var myfood =  $.cookie("myfood"),amount= 0,totalMoney= 0,delivery_fee =parseFloat($('#cart_foot').data('delivery_fee'));
    if(myfood){
        var obj = $.parseJSON(myfood);
        if(obj){
            for(var i in obj) {
                amount+=parseInt(obj[i]['amount']);
                if($('#js_goods_num_'+obj[i]['id']).attr('type') =='text'){
                    $('#js_goods_num_'+obj[i]['id']).val(obj[i]['amount']);
                }else{
                    $('#js_goods_num_'+obj[i]['id']).html(obj[i]['amount']);
                }
                totalMoney +=  (obj[i]['amount'] *obj[i]['price']);
                $('#js_goods_'+obj[i]['id']+' .js_total').html(parseFloat((obj[i]['amount']*obj[i]['price'])));
                $('#CartNo').html(amount);
            }
            if(parseInt(obj[i]['amount'])<1){
                $('#js_goods_'+obj[i]['id']).remove();
            }
        }
        $('#cart_foot .jstotalmoney').html('&yen;'+totalMoney);
        $('#cart_foot .totalmoney').html('&yen;'+parseFloat(delivery_fee+totalMoney));

    }else{

    }
}

function goCashier(){
    var myfood =  $.cookie("myfood");
    var obj = $.parseJSON(myfood);
    if(obj){
        window.location.href = '/m_cashier.html?gocashier=gocashier';
    }else{
        jAlert("Order content cannot be empty!",SYSTITLE);
        return false;
    }
}

function submitOrder(){
    var myfood =  $.cookie("myfood"),totalMoney= 0,delivery_fee =parseFloat($('#delivery_fee').html()),deliverydate;
    var obj = $.parseJSON(myfood);
    if(!obj){
        jAlert("Order content cannot be empty!",SYSTITLE);
        return false;
    }
    if ($("#UseAddressID").val()) {
        jAlert("I'm sorry, please select a shipping address!",SYSTITLE);
        return false
    };
    var pm = $('input:radio[name="paymethod"]:checked').val();
    pm = (pm==0 ? 0: 1);


    deliverydate = $("#delivertime").val();
    if(deliverydate==''){
        jAlert("Sorry, please select the delivery date!",SYSTITLE);
        return false
    }

    var data = {
        shop_id : $("#shop_id").val(),
        OrderInfo : $("#UseAddressID").val(),
        cityname : $("#cityname").val(),
        invoice : $('input:radio[name="invoice"]:checked').val(),
        date :$('#delivertimeselect').val(),
        date :$('#delivertimeselect').val(),
        info : $.trim($("#Info").val()),
        pm : (pm==0 ? 0: 1),
        deliverydate : deliverydate
    }
    console.log(data)
return
    var title = "Are you sure you want to submit the order?";
    jConfirm(title,SYSTITLE,function(msg){
        if(msg){
       //     $("#form1").submit();
        }else{
            return false;
        };
    });

}


//提交订单
function createOrder(){
        var note = $.trim($("#noteH").text()),
            order = "",
            resId = $body.attr("res_id"),
            handle = $('#priceTotal').text(),//应付金额
            member_blance =  $('#member_blance').text(),//会员卡余额
            key = "myfood",
            myfood = $.cookie(key),
            price_count = 0,
            type = 0,
            cardState =  $('#sub_food_order').attr('rel'),//会员卡余额,
            is_online_pay =  $('.pay_list .hover').attr('rel');
        //会员卡支付判断会员卡余额
        if(member_blance - handle < 0 && is_online_pay==5){
            clearpop(jsLangChange('会员卡余额不足!'));
            subBlock = false;//解除阻塞
            return;
        }
        if(is_online_pay==5 && cardState==1){//会员卡被禁用
            clearpop(jsLangChange('该会员卡已被商家禁用，请尽快联系商家处理!'));
            subBlock = false;//解除阻塞
            return;
        }
        var from_type   = $body.attr("from_type");//正常订单
        var extra_fee   = $body.attr("extra_fee");
        if(myfood){
            var myfood_array = $.parseJSON(myfood);
            if(myfood_array[resId]){// 该餐厅存在
                for(var i in myfood_array[resId]){
                    order  = order + myfood_array[resId][i]['food_id'] + "," + myfood_array[resId][i]['amount'] +','+myfood_array[resId][i]['food_pack']+"|";
                    price_count = price_count + parseFloat(myfood_array[resId][i]['food_price'])*parseInt(myfood_array[resId][i]['amount']);
                }
                order = order.substr(0,order.length-1);
            }
        }
        price_count = price_count.toFixed(1);
        contact = $.trim($("#contact .contact").text());
        tel = $.trim($("#contact .tel").text());
        address = $.trim($("#contact .address").text());
        if(res_type == 2){
            if(tel.length == 0 || !(regex(tel,"mob") || regex(tel,"tel") || regex(tel,"short_tel"))){
                clearpop(jsLangChange('电话格式不正确'));
                subBlock = false;//解除阻塞
                return;
            }
            var send_time = $.trim($("select[name=send_time]").val());
            if(!send_time){
                clearpop(jsLangChange('送时不能为空'));
                subBlock = false;//解除阻塞
                return;
            }
            if(send_time==-1 || send_time<0){
                clearpop('已不在营业时间');
                subBlock = false;//解除阻塞
                return;
            }
            if(address.length == 0){
                clearpop(jsLangChange('地址不能为空'));
                subBlock = false;//解除阻塞
                return;
            }
            if(min_price - price_count > 0){
                clearpop(jsLangChange('未达起送价')+min_price);
                subBlock = false;//解除阻塞
                return;
            }
            send_time = $.trim($("select[name=send_time_bf]").val()) + " " + send_time;
        }else{
            tel = $.trim($("#peoples").val());
            address = $.trim($("#dasknum").val());
            contact = $.trim($("#contacts").val());
            var send_time = $.trim($("select[name=send_time_get]").val());;
            var reg = new RegExp("^[0-9]*$");
            if(!send_time){
                clearpop(jsLangChange('到时不能为空'));
                subBlock = false;//解除阻塞
                return;
            }

            if(!address || !tel){
                clearpop(jsLangChange('桌号')+"/"+jsLangChange('人数不能为空'));
                subBlock = false;//解除阻塞
                return;
            }
            send_time = $.trim($("select[name=send_time_get_bf]").val()) + " " +send_time;
            type = 3;  //堂吃
            extra_fee = 0;
        }
        if(order == ""){
            clearpop(jsLangChange('订单内容不能为空'));
            subBlock = false;//解除阻塞
            return;
        }
        var $is_form_weixin = parseInt($body.attr('form_weixin'));
        if(is_online_pay == ""){
            clearpop(jsLangChange('请先选择支付方式！'));
            subBlock = false;//解除阻塞
            return;
        }else if(is_online_pay == 3 && $is_form_weixin){
//        clearpop(jsLangChange('微信中不支持支付宝支付!'));
//        subBlock = false;//解除阻塞
//        return;
        }else if(is_online_pay ==2 && !$is_form_weixin){
            clearpop(jsLangChange('请在微信中下微信支付订单！'));
            subBlock = false;//解除阻塞
            return;
        }
        if(is_online_pay == 5){
            var mid =  $('.pay_list .hover').attr('mid');//会员Id
        }
        clearSubOrderCss();
        var activity_desc  = $("#activity").html();//优惠方案
        var money       = $("#money").html();//优惠金额
        var spread_id = $body.attr("spread_id");//盟主ID
        var pack_fee =   $('#pack_total_fee .js_pack_fee').text();
        var table_id=$("#dasknum").val();//桌号名称
        var tableUserId = $("#order_table_id").val();//桌位号
        $.post("/Common/Order/createOrder",{'code':code,"restaurant_id":resId,"tel":tel,
                "address":address,"contact":contact,"description":note,"order":order,
                "from_type":from_type,"send_time":send_time,"type":type,"extra_fee":extra_fee
                ,"pack_fee":pack_fee, "discounts":money,"activity_desc":activity_desc,'sign_type':res_type,
                "is_online_pay": is_online_pay, "table_id":tableUserId,"spread_id": spread_id,'mid':mid},function(data){
                subBlock = false;//解除阻塞
                if(data['code'] == 100){
                    clear_food_order(resId);
                    if(res_type==1){
                        clearpop(jsLangChange('恭喜您!下单成功。请按时到店就餐～'));
                    }else{
                        clearpop(jsLangChange('恭喜您!下单成功。坐等美食送上门～'));
                    }
                    setTimeout(function(){
                        window.location.href="/index/orderinfo?order_id="+data['data']['order_id'];
                    },1000);
                }else if(data['code'] == 206){    //支付跳转
                    clear_food_order(resId);
                    window.location.href=data['data']['pay_url'];
                }else if(data['code'] == 201){
                    clearpop(data.message);
                    $('#sub_food_order').removeAttr("disabled");
                    $('#sub_food_order').val('提交订单');
                    $('#sub_food_order').css('background','#ff8a00');
                    needAuthed(data.data);
                }else{
                    clearpop(data.message);
                    $('#sub_food_order').removeAttr("disabled");
                    $('#sub_food_order').val('提交订单');
                    $('#sub_food_order').css('background','#ff8a00');
                }
            }
            ,"json");
    }
