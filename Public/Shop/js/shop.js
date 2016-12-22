
$(function(){
    $(".nav_center").smartFloat(0);
    var pagename = $("body").attr('pagename');
    switch(pagename){
        case "cart":{getCartGoodStock('iscart');break}
        case "cashier":{getSettleGood();break}
    }
    loadGood();
})


function closeModel(obj){
    $('.lean_overlay').hide();
    $(obj).hide();
}

var $goodKey = 'myfood_shop';

/**
 *添加商品
 * @param id
 * @returns {boolean}
 */
function addgood(id,event,page){
    var myfood = $.cookie($goodKey),
        id = $('#js_goods_'+id).data("id"),
        price = $('#js_goods_'+id).data("price"),
        indexpic = $('#js_goods_'+id).data("indexpic"),
        name = $('#js_goods_'+id).data("name"),
        stock = $('#js_goods_'+id).data("stock"),
        amount = 1;
    if(page){
        if(page =='list' || page =='view'){
            var addnum = parseInt($('#js_good_num_'+id).val());
            if(addnum){
                amount = addnum;
            }
        }
    }
      var myfood_array  = $.parseJSON(myfood);
    // 看该产品是否存在
    if(myfood_array){
        if(myfood_array[id]){
            var single = myfood_array[id];
            myfood_array[id]['amount'] = parseInt(single['amount']) +amount;
        }else{
            myfood_array[id] = {"id":id,"name":name,"price":price,"amount":amount,"indexpic":indexpic,"stock":stock};
        }
    }else{
        myfood_array = {};
        myfood_array[id] = {"id":id,"name":name,"price":price,"amount":amount,"indexpic":indexpic,"stock":stock};
    }
    if( myfood_array[id]['amount'] > stock){ //库存不足
        clearpopj("Insufficient stock!", "error",true);
        return false;
    }
    if(myfood_array[id]['amount'] >1 && page != 'view'){
        $('#js_goods_'+id+' .good-num-prep').removeClass('fc_eee');
    }
    var json = $.toJSON(myfood_array);
    $.cookie($goodKey,json,{
        "path":"/"
    });

    loadGood(page);
    if(page != 'cart'){
        flyCart(event, id,page);
    }
    if(page == 'cart' ){ //购物车、详情页加减商品，实时显示商品数量
        $('#js_good_num_'+id).val( myfood_array[id]['amount']);
    }
    if(page == 'cart'){
        $('.subtotal_'+id).html('&yen;'+parseInt(myfood_array[id]['amount'])* parseFloat(myfood_array[id]['price']));
    }
}

/**
 * 减少商品
 * @param id
 */
function prepGood(id,page){
    var myshop = $.cookie($goodKey);
    var myshop_array = {};
    if(myshop){
        myshop_array = $.parseJSON(myshop);
        if(myshop_array[id]){   //存在
            var single = myshop_array[id];
            if( myshop_array[id]['amount'] >1){
                myshop_array[id]['amount'] = parseInt(single['amount']) - 1;
                if(page == 'cart'){
                    $('.subtotal_'+id).html('&yen;'+parseInt(myshop_array[id]['amount'])* parseFloat(myshop_array[id]['price']));
                }
                $('#js_good_num_'+id).val(myshop_array[id]['amount']);
            }else{ //没商品数量了
                    $('#js_goods_'+id+' .good-num-prep').css('color','#eeeeee');
                    return false;
                //if(page =='cart'){
                //    $(         '#js_goods_'+id).remove();
                //}
                //myshop_array[id]= undefined;
            }
        }else{
            $('#js_goods_'+id+' .good-num-prep').css('color','#eeeeee');
            return false;
            //if(page =='cart'){
            //    $('#js_goods_'+id).remove();
            //}
            //myshop_array[id]= undefined;
        }
    }
    var json = $.toJSON(myshop_array);
    $.cookie($goodKey,json,{
        "path":"/"
    });
    loadGood(page);
}

/**
 * 加减购物数量
 * @param id
 * @param type 类型 ： add cat
 */
function setGoodNum(id,type){
    if(id){
        var num = parseInt($('#js_good_num_'+id).val());
        if(type =='cat'){
            if(num>1){
                num -=1;
            }else{
                num = 1;
            }
        }else{
            num +=1;
        }
    }else{
        num = 1;
    }
    if(num>1){
        $('#js_goods_'+id+' .good-num-prep').removeClass('fc_eee');
        //$('#js_goods_'+id+' .good-num-prep').css('color','#757575');
    }else{
        $('#js_goods_'+id+' .good-num-prep').addClass('fc_eee');
        //$('#js_goods_'+id+' .good-num-prep').css('color','#eeeeee');
    }
    $('#js_good_num_'+id).val(num);
}


/**
 * 删除购物车指定商品
 */
function delGood(id,page){
    swal({
        title: '',
        text: 'Are you sure you want to delete?',
       type: 'warning',
        showCancelButton: true,
        closeOnConfirm: false,
        confirmButtonText: "Yes",
        //confirmButtonColor: "#35D374"
    }, function() {
        $('#js_goods_'+id).slideUp();
        $('.showSweetAlert .cancel').click();
        var myshop = $.cookie($goodKey);
        var myshop_array = {};
        if(myshop){
            myshop_array = $.parseJSON(myshop);
            if(myshop_array[id]){//存在
                myshop_array[id]= undefined;
            }
        }
        var json = $.toJSON(myshop_array);
        $.cookie($goodKey,json,{
            "path":"/"
        });
        loadGood(page);
    });
}


/*加载缓存中的购物车数据到页面*/
function loadGood(page){
    var myfood =  $.cookie($goodKey),amount= 0,totalMoney= 0,$html='';
    if(myfood){
        var obj = $.parseJSON(myfood);
        if(obj){
            for(var i in obj) {
                //头部滚动购物车框
                $html+='<div class="cart_good_item" data-id="'+obj[i]['id']+'">';
                $html+='<img src="'+obj[i]['indexpic']+'" class="cgood_img" alt=""/>';
                $html+='<div class="cgood_info">';
                $html+='<div class="cg_title"><a href="/product/view.html?id='+obj[i]['id']+'">'+obj[i]['name']+'</a></div>';
                $html+='<div class="cg_info"><span class="price">&yen;'+obj[i]['price']+'</span> x'+obj[i]['amount']+'</div>';
                $html+='</div>';
                $html+='<div class="clr"></div>';
                $html+='</div>';
                if(obj[i]['amount']) {
                    amount += parseInt(obj[i]['amount']);
                    totalMoney += (obj[i]['amount'] * obj[i]['price']);
                }
                if(obj[i]['amount'] <2){
                    $('#js_goods_'+obj[i]['id']+' .good-num-prep').addClass('fc_eee');
                    ///$('#js_goods_'+obj[i]['id'] +' .good-num-prep').css('color','#eeeeee');
                }
            }
            $('#CartNo').html(amount);
            $('.js_good_total_num').html(amount);
            $('.good_total').html('&yen;'+totalMoney);
            $('#cart_good_box').html($html);
            if(page == 'cart'){
                getGoodCartInfo();
            }
        }

    }
}

/**
 * 获取商品信息
 * @param $objPage
 */
function getCartGoodStock($objPage){
    var myfood = $.cookie($goodKey), $goodIds='',$totalmoney=0;
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
                                if(obj[i]['stock']>1 && myfood_array[obj[i]['id']]['amount']==0){
                                    myfood_array[obj[i]['id']]['amount']=1;
                                }
                                if(parseInt(  myfood_array[obj[i]['id']]['amount'])>parseInt(obj[i]['stock'])) { //库存小于当前购物车商品数量
                                    myfood_array[obj[i]['id']]['amount'] = obj[i]['stock'];
                                }
                                //重新将商品信息放入本地购物车中（有可能后台更新数据）
                                myfood_array[obj[i]['id']]['id'] = obj[i]['id'];
                                myfood_array[obj[i]['id']]['name'] = obj[i]['title'];
                                myfood_array[obj[i]['id']]['price'] = obj[i]['price'];
                                myfood_array[obj[i]['id']]['indexpic'] = obj[i]['indexpic'];
                                myfood_array[obj[i]['id']]['stock'] = obj[i]['stock'];
                                myfood_array[obj[i]['id']]['status'] = obj[i]['status'];
                                if(obj[i]['status'] ==1){ //计算正常状态的商品总金额
                                    $totalmoney +=parseFloat(obj[i]['price'])*parseInt(myfood_array[obj[i]['id']]['amount']);
                                }
                            }
                        }
                        var json = $.toJSON(myfood_array);
                        $.cookie($goodKey,json,{
                            "path":"/"
                        });
                    }
                    if($objPage=='iscart'){
                        getCartData();
                        return true;
                    }
                })
            }
        }
    }
    $('#good_cart').addClass('hide');
    $('.cart_foot_info').addClass('hide');
    $('#empty_cart_box').removeClass('hide');
};

function buynow(id){
    var key = 'settlement',
        id = $('#js_goods_'+id).data("id"),
        price = $('#js_goods_'+id).data("price"),
        indexpic = $('#js_goods_'+id).data("indexpic"),
        name = $('#js_goods_'+id).data("name"),
        stock = $('#js_goods_'+id).data("stock"),
        amount = $('#js_good_num_'+id).val();

    if($.cookie(key) ||  $.parseJSON($.cookie(key))){ //清空结算购物车
        $.cookie(key, "", {"path": "/"});
    }
    var myfood_array = {};
        myfood_array[id] = {"id":id,"name":name,"price":price,"amount":amount,"indexpic":indexpic,"stock":stock,'status':1};
    if( myfood_array[id]['amount'] > stock){ //库存不足
        clearpopj("Insufficient stock!", "error",true);
        return false;
    }

    var json = $.toJSON(myfood_array);
    $.cookie(key,json,{
        "path":"/"
    });
    window.location.href='/index/cashier.html';
}


function getCartData(){
    var myfood = $.cookie($goodKey), totalMoney = 0, totalNum= 0, outofstock='',idnum=1,
        $html = '<tr> <th width="60"> <div class="checkboxFive" onclick="selectAll();"><label for="checkboxFiveInput" style="width: 60px;left: 0;top: -6px;line-height: 17px;;"><input type="checkbox" value="1" id="checkboxFiveInput" checked name="" /><span class="fl cbox"></span> All </label> </div> </th><th width="60">No</th><th width="405">Product Name</th> <th width="150">Unit Price</th> <th width="100">Quantity</th><th width="140">Subtotal</th><th width="85">Action</th></tr>';
    if (myfood) {
        var obj = $.parseJSON(myfood);
        if (obj) {
            for (var i in obj) {
                if(obj[i]['status'] ==1) {
                    $html += '<tr class="js_goods_' + obj[i]['id'] + '"  id="js_goods_' + obj[i]['id'] + '" data-id="' + obj[i]['id'] + '" data-price="' + obj[i]['price'] + '" data-stock="' + obj[i]['stock'] + '" data-indexpic="' + obj[i]['indexpic'] + '" data-name="' + obj[i]['title'] + '">';
                    $html += '<td align="left"  id="good-li-' + obj[i]['id'] + '">';
                    $html += '<div class="checkboxFive" onclick="getGoodCartInfo();">';
                    $html += '<label style="width: 60px;left: 0;top: -6px;line-height: 17px;">';
                    $html += '<input type="checkbox" checked value="' + obj[i]['id'] + '" name="id" />';
                    $html += '<span class="fl cbox"></span>';
                    $html += '</label>';
                    $html += '</div>';
                    $html += '</td>';
                    $html += '<td align="center">' + idnum+ '</td>';
                    $html += '<td align="left"><a href="/Product/view.html?id=' + obj[i]['id'] + '">';
                    $html += '<img alt="{$vo.name}" src="' + obj[i]['indexpic'] + '" width="60" height="60" style="top: 0" /><span class="goodtitle"  style="position: relative;top: 20px;">' + obj[i]['name'] + '</span></a>';
                    $html += '</td>';
                    $html += '<td align="center"><span class="jiage">&yen;' + obj[i]['price'] + '</span></td>';
                    $html += '<td align="center">';
                    $html += '<div class="quantity">';
                    if(obj[i]['amount']>1){
                        $html += '<span class="good-num-prep" unselectable="on" style="-moz-user-select:none;" onselectstart="return false;" onclick="prepGood(' + obj[i]['id'] + ',\'cart\');">-</span>';
                    }else{
                        $html += '<span class="good-num-prep fc_eee" unselectable="on" style="-moz-user-select:none;" onselectstart="return false;" onclick="prepGood(' + obj[i]['id'] + ',\'cart\');">-</span>';
                    }
                    $html += '<input type="text" id="js_good_num_' + obj[i]['id'] + '" onkeyup="cartSetGoodNum('+obj[i]['id']+');" class="tc good_num" style="width: 32px;height: 23px;" value="' + obj[i]['amount'] + '"/>';
                    $html += '<span class="good-num-add" unselectable="on" style="-moz-user-select:none;" onselectstart="return false;" onclick="addgood(' + obj[i]['id'] + ',event,\'cart\');">+</span>';
                    $html += '</div>';
                    $html += '</td>';
                    $html += '<td align="center" class="fc_green subtotal_' + obj[i]['id'] + '">&yen;' + parseFloat(obj[i]['price']) * parseFloat(obj[i]['amount']) + '</td>';
                    $html += '<td align="center"><a href="javascript:void(0);" class="delete"  onclick="delGood(' + obj[i]['id'] + ',\'cart\')">delete</a></td>';
                    $html += '</tr>';
                    //下架的不计
                    if(obj[i]['amount']>0){
                        totalMoney +=  parseFloat(obj[i]['amount'] *obj[i]['price']);
                        totalNum += parseInt(obj[i]['amount']);
                    }
                }else if(obj[i]['id']){ //下架商品
                    outofstock += '<tr class="js_goods_' + obj[i]['id'] + '"   id="js_goods_' + obj[i]['id'] + '" data-id="' + obj[i]['id'] + '" data-price="' + obj[i]['price'] + '" data-stock="' + obj[i]['stock'] + '" data-indexpic="' + obj[i]['indexpic'] + '" data-name="' + obj[i]['title'] + '">';
                    outofstock += '<td align="left"  id="good-li-' + obj[i]['id'] + '">';
                    outofstock += '<div style="line-height: 20px;color: red;text-align: center;">out of stock</div>';
                    outofstock += '</td>';
                    outofstock += '<td align="center">' + idnum + '</td>';
                    outofstock += '<td align="left"><a href="/Product/view.html?id=' + obj[i]['id'] + '">';
                    outofstock += '<img alt="{$vo.name}" style="top: 0;" src="' + obj[i]['indexpic'] + '" width="60" height="60"  /><span class="goodtitle" style="position: relative;top: 20px;">' + obj[i]['name'] + '</span></a>';
                    outofstock += '</td>';
                    outofstock += '<td align="center"><span class="jiage">&yen;' + obj[i]['price'] + '</span></td>';
                    outofstock += '<td align="center">';
                    outofstock += '<div class="quantity">';
                    if(obj[i]['amount']>1){
                        outofstock += '<span class="good-num-prep" unselectable="on" style="-moz-user-select:none;" onselectstart="return false;" onclick="prepGood(' + obj[i]['id'] + ',\'cart\');">-</span>';
                    }else{
                        outofstock += '<span class="good-num-prep fc_eee" unselectable="on" style="-moz-user-select:none;" onselectstart="return false;" onclick="prepGood(' + obj[i]['id'] + ',\'cart\');">-</span>';
                    }
                    outofstock += '<input type="text" id="js_good_num_' + obj[i]['id'] + '" class="tc good_num" style="width: 32px;height: 23px;" value="' + obj[i]['amount'] + '"/>';
                    outofstock += '<span class="good-num-add" unselectable="on" style="-moz-user-select:none;" onselectstart="return false;" onclick="addgood(' + obj[i]['id'] + ',event,\'cart\');">+</span>';
                    outofstock += '</div>';
                    outofstock += '</td>';
                    outofstock += '<td align="center" class="fc_green subtotal_' + obj[i]['id'] + '">&yen;' + parseFloat(obj[i]['price']) * parseFloat(obj[i]['amount']) + '</td>';
                    outofstock += '<td align="center"><a href="javascript:void(0);" class="delete"  onclick="delGood(' + obj[i]['id'] + ',\'cart\')">delete</a></td>';
                    outofstock += '</tr>';
                }
                idnum++
            }
            //<div style="line-height: 2px; color: #999999"><img src="__PUBLIC__/Shop/images/waste.png" width="25" alt=""/>empty cart</div>
            var tablecontent =$html+outofstock;
            $('#good_cart').html(tablecontent);
            $('.cart_foot_info').removeClass('hide');
            $('.clear_cart').removeClass('hide');
            $('#good_cart').removeClass('hide');
            $('#empty_cart_box').addClass('hide');
            $('#totalAmount').html('&yen;'+totalMoney);//总金额
            $('#quantity').html(totalNum);
            $('.goodtitle').each(function(){
                if($(this).height()>30){
                    $(this).css('top', '5px');
                }
            })

            return true;
        }
    }
    $('.clear_cart').addClass('hide');
    $('#good_cart').addClass('hide');
    $('.cart_foot_info').addClass('hide');
    $('#empty_cart_box').removeClass('hide');
}

function cartSetGoodNum(id){
    var number = parseInt($('#js_good_num_'+id).val()),myfood_array={};
    if(!number){
        number =1;
    }
    if(number>0 && id >0){
        var myfood = $.cookie($goodKey);
        if(myfood){
             myfood_array = $.parseJSON(myfood);
            if(myfood_array && myfood_array[id]){
                if(number > myfood_array[id]['stock']){ //如果输入的数字大于库存，默认库存
                    number = parseInt(myfood_array[id]['stock']);
                }
                myfood_array[id]['amount'] = number;
                var json = $.toJSON(myfood_array);
                $.cookie($goodKey,json,{
                    "path":"/"
                });
                if(number>1){
                    $('#js_goods_'+id+' .good-num-prep').css('color','#757575');
                }
                $('#js_good_num_'+id).val(number);
                getGoodCartInfo();
            }
        }
    }
}


//购物车全选
function selectAll(){
    if($('#checkboxFiveInput').is(':checked')){
        var totalMoney=0
        $('#good_cart input[type=checkbox]').each(function(i) {
            if(i>0){
                $(this).click();
            }
        });
    }else{
        $('#good_cart input[type=checkbox]').removeAttr('checked');
    }
}

function getGoodCartInfo(page){
    var totalMoney = 0,totalNum= 0,myfood_array={};
    var myfood = $.cookie($goodKey);
    if(myfood){
        myfood_array = $.parseJSON(myfood);
        if(myfood_array){
            $('#good_cart input[type=checkbox]').each(function() {
                var id =$(this).val();
                if($(this).is(':checked')){
                    if(id>1 && myfood_array[id]){
                        var goodTotalMoney = parseInt(myfood_array[id]['amount']) * parseFloat(myfood_array[id]['price'])
                        totalMoney+=goodTotalMoney;
                        totalNum+=parseInt(myfood_array[id]['amount']);
                        if(!page){
                            $('#js_goods_'+id+' .subtotal_'+id).html('&yen;'+goodTotalMoney);
                        }
                    }
                }
            });
        }
    }
    $('#totalAmount').html('&yen;'+totalMoney);//总金额
    $('#quantity').html(totalNum);
}


//////////////////////////////结算/////////////////////////////////////
function gocashier(){
    var key = 'settlement',
        settlegood ={},
        flag = false,
    myfood = $.cookie($goodKey);
    if (myfood) {
        var myfood_array = $.parseJSON(myfood);
    }
    $.cookie(key, null, {"path": "/"});
    if (myfood_array) {
        $('#good_cart input[type=checkbox]').each(function(i) {
            if($(this).is(':checked')){
                if($(this).val() > 1){
                    flag = true;
                    settlegood[$(this).val()] = myfood_array[$(this).val()];
                }
            }
        });
    }
    if(flag){
        var json = $.toJSON(settlegood);
        $.cookie(key,json,{
            "path":"/"
        });
        window.location.href='/index/cashier.html';
    }else{
        clearpopj("No products to check out!", "error",true);
        return false;
    }
}

/**
 *
 */
function getSettleGood(){
    var myfood = $.cookie('settlement'), totalMoney = 0, totalNum= 0,idnum= 1,
        $html = '<tbody class="hide"><tr><th width="100">No</th><th width="736">Product Name</th> <th width="150">Unit Price</th> <th width="100">Quantity</th><th width="140">Subtotal</th></tr>';
    if (myfood) {
        var obj = $.parseJSON(myfood);
        if (obj) {
            for (var i in obj) {
                if(obj[i]['status'] ==1) {
                    $html+='<tr >';
                    $html+='<td align="center">' +idnum+ '</td>';
                    $html+='<td align="left"><a href="/Product/view.html?id=' + obj[i]['id'] + '">';
                    $html+='<img src="' + obj[i]['indexpic'] + '" width="60" height="60"  /><span class="goodtitle">' + obj[i]['name'] + '</span></a>';
                    $html+='</td>';
                    $html+='<td align="center"><span class="jiage">&yen;' + obj[i]['price'] + '</span></td>';
                    $html+='<td align="center">' + obj[i]['amount'] + '</td>';
                    $html+='<td align="center" class="fc_green subtotal_' + obj[i]['id'] + '">&yen;'+parseFloat( obj[i]['price']* parseInt(obj[i]['amount']))+'</td>';
                    $html+='</tr>';
                    totalMoney +=  parseFloat(obj[i]['amount'] *obj[i]['price']);
                    totalNum +=  obj[i]['amount'];
                    idnum++;
                }
            }
        }
        var discount = getActiviDiscount(totalMoney);
        if(discount>0){
            var discountplan =discount;
        }else{
            var discountplan =40;
        }
        var delivery_fee = getdeliveryFee(totalMoney);
        var allMoney = (totalMoney-parseFloat(discount))+parseFloat(delivery_fee);

        $html+='</tbody><tfoot class="bg_white tfood_info"><tr class=" bg_white" style="background: #FFffff;line-height: 30px;border-top: 1px solid #EEEEEE;"><td colspan="3" align="left">&nbsp;</td><td colspan="2" align="right"> <div>Amount:&nbsp;<span class="amount_money">&yen;'+totalMoney+'</span></div><div >Delivery Fee:&nbsp;<span class="delivery_fee">&yen;'+delivery_fee+'</span></div><div >Discount(&yen;<span>'+discountplan+'</span> OFF &yen;400+ SITEWIDE):&nbsp;<span class="discount fc_red">&yen;'+discount+'</span></div> <div>Total Amount:&nbsp;<span class="totalSubMoney">&yen;'+allMoney+'</span></div></td></tr><tfoot>';
        $('#cashier_table').attr('data-delivery_fee',delivery_fee);
        $('#cashier_table').html($html);
    }
}
/*阻塞标志，防止重复下单；预设不阻塞*/
window.subBlock=false;var ot = '';
/**
 * 提交订单
 * @returns {boolean}
 */
function submitOrder(){
    if(subBlock){
        return false;
    }
    var myfood =  $.cookie("settlement"),delivery_fee =parseFloat($('#cashier_table').data('delivery_fee')),order='';
    if(!myfood){
        clearpopj("No products to check out.", "error",true);
        subBlock = false;
        return false;
    }
    var myfood_array = $.parseJSON(myfood);
    if(!myfood_array){
        clearpopj("No products to check out.", "error",true);
        subBlock = false;//解除阻塞
        return false;
    }
    for(var i in myfood_array){
        if(parseInt(myfood_array[i]['amount'])>parseInt(myfood_array[i]['stock'])){
            clearpopj('Insufficient stock for '+myfood_array[i]['name'], "error",true);
            subBlock = false;//解除阻塞
            return false;
        }
        order +=  myfood_array[i]['id'] + "," + myfood_array[i]['amount']+ "," + myfood_array[i]['price'] +"|"; //订单信息
    }

    if (!$("#UseAddressID").val()) {
        clearpopj('Please select a shipping address!', "error",true);
        subBlock = false;//解除阻塞
        return false
    };

    var deliverydatetimes = '';
    for(var i in deliverydate){
        if(deliverydate[i].length>0){
            deliverydatetimes +=i;
            deliverydatetimes +=' '+deliverydate[i]+' ';
        }
    }
    var data = {
        shop_id : $("#shop_id").val(),
        UseAddressID : $("#UseAddressID").val(),
        cityname : $("#cityname").val(),
        paymethod :$('#paymethod').val(),
        invoice :$('#invoice').val(),
        info : $.trim($("#info").val()),
        delivertime : deliverydatetimes,
        order : order
    }
    loading();
    subBlock = true;
    $.post('/shop/order/submitOrder.html',data,function(data){
        subBlock = false;//解除阻塞
        closeLoad();
        if(data.code == 200){
            clearCart(); //清空购物车
            if(data.data){
                if($('#paymethod').val() ==5){ //微信扫码支付
                    window.location.href='/index/pay.html?orderno='+data.data.orderno;
                }else{
                    clearpopj(data.message, "success",true,data.data);
                }
            }
        }else{
            clearpopj(data.message, "error",true);
            return false;
        }
    })
}

//清除购物车已购商品
function clearCart(){
    var myfood =  $.cookie("settlement");
    if(!myfood){
        return false;
    }
    var myshop = $.cookie($goodKey);
    var mycart_array = $.parseJSON(myshop);
    var myfood_array = $.parseJSON(myfood);
    if(myfood_array){
        for(var i in myfood_array){
            if(myfood_array[i]['id']){
                mycart_array[myfood_array[i]['id']]= undefined;
            }
        }
    }
    var json = $.toJSON(mycart_array);
    $.cookie($goodKey,json,{
        "path":"/"
    });
    loadGood();

    $.cookie("settlement", null, {"path": "/"});

};

/**
 * 取消订单
 * @param id
 * @param orderno
 */
function cancelOrder(id,orderno) {
    swal({
        title: '',
        text: ' Are you sure to cancel the order?',
        type: 'warning',
        showCancelButton: true,
        closeOnConfirm: false,
        confirmButtonText: "OK",
        //confirmButtonColor: "#35D374"
    }, function() {
        $.post('/order/cancelOrder.html',{orderno:orderno},function(data){
            if(data.code == 200){
                $('.js_onging_order_num').html(parseInt($('.js_onging_order_num').html())-1);
                $('.js_all_order_num').html(parseInt($('.js_all_order_num').html())-1);
                $('#order_row_'+id).remove();
                clearpopj(data.message, "success",true);
            }else{
                clearpopj(data.message, "error",true);
            }
        })
    });
};


/**
 * 清空购物车
 */
function clearCartAll(){
    $('#good_cart').remove();
    $('#cart_good_box').html('');
    $('.cart_foot_info').addClass('hide');
    $('.clear_cart').addClass('hide');
    $('.empty_cart_box').removeClass('hide');
    $('#CartNo').html('0');
    $('#cart_box .js_good_total_num').html('0');
    $('#cart_box .good_total').html('&yen;0');
    $.cookie($goodKey, null, {"path": "/"});
    $.cookie("settlement", null, {"path": "/"});

}
