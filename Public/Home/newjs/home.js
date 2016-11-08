$(function(){
    loadGood();
})

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
/**
 *添加商品
 * @param id
 * @param event
 * @returns {boolean}
 */
function addgood(id,event,isbuynow){
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
        if(isbuynow == true){
            window.location.href = '/m_cart.html';
        }
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
                if( $('#js_goods_'+id).hasClass('jsCart')){
                    $('#js_goods_'+id).remove();
                }
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
    $.cookie("myfood", "", {"path": "/"});
}

function fly(event){
    if($("#CartNo").css('display') == 'none'){
        var offset = $("#offset").offset();
    }else{
        var offset = $("#CartNo").offset();
    }

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
                    $('#js_goods_'+obj[i]['id']+' .js_total').html(parseFloat((obj[i]['amount']*obj[i]['price'])));
                    $('#CartNo').html(amount);
                    if(parseInt(obj[i]['amount'])<1){
                        $('#js_goods_'+obj[i]['id']).remove();
                    }
                }
            }
            if(!amount){
                $('#CartNo').css('display','none');
            }
            $('#cart_foot .totalMoney').html('&yen;'+totalMoney);

        }
    }else{
        $('#CartNo').css('display','none');
    }
}

/**
 * 进入结算页面
 * @returns {boolean}
 */
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
    var myfood =  $.cookie("myfood"),totalMoney= 0,delivery_fee =parseFloat($('#delivery_fee').html()),deliverydate= $('#delivertimeselect').val()+' ',order='';
    var myfood_array = $.parseJSON(myfood);
    if(!myfood_array){
        jAlert("Order content cannot be empty!",SYSTITLE);
        return false;
    }
    for(var i in myfood_array){
        order +=  myfood_array[i]['id'] + "," + myfood_array[i]['amount']+ "," + myfood_array[i]['price'] +"|"; //订单信息
    }
    if (!$("#UseAddressID").val()) {
        jAlert("I'm sorry, please select a shipping address!",SYSTITLE);
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
        info : $.trim($("#Info").val()),
        delivertime : deliverydate,
        order : order
    }

    $.post('/home/order/submitOrder.html',data,function(data){
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

function getdeliveryFee(money,obj){
    $.post('/home/shop/getdeliveryFee.html',{money:money},function(data){
        if(data.code == 200){
            $(obj).html('&yen;'+data.data)
            $(obj).attr('deliveryFee',data.data)
        }
    })
}
function getAmountMoney(totalMoney,obj,deliveryFee){
    $.post('/home/shop/getdeliveryFee.html',{money:totalMoney},function(data){
        if(data.code == 200){
            $(obj).html('&yen;'+data.data);
            $(deliveryFee).html('&yen;'+data.data);
            var $total = parseFloat(totalMoney)+parseFloat(data.data)
            console.log($total);
            $(obj).html('&yen;'+$total);//总金额= 配送费+商品总金额
        }
    })
}