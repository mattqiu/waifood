
$(function(){
    $(".nav_center").smartFloat(0);
    loadGood();
})

var $goodKey = 'myfood_shop';

function logout(id) {
    var title = "Are you sure you want to quit?";
    jConfirm(title,SYSTITLE,function(msg){
        if(msg){
            var url = CONST_CART.replace('Cart/URL', 'Login/logout');
            location = url;
        }else{
            return false;
        };
    });
    return false;
};


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
        jAlert("Insufficient stock!",SYSTITLE);
        return false;
    }

    var json = $.toJSON(myfood_array);
    $.cookie($goodKey,json,{
        "path":"/"
    });
    loadGood();
    fly(event, indexpic);
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
    $('#js_good_num_'+id).val(num);
}

/*加载缓存中的购物车数据到页面*/
function loadGood(){
    var myfood =  $.cookie($goodKey),amount= 0,totalMoney= 0,$html='';
    if(myfood){
        var obj = $.parseJSON(myfood);
        if(obj){
            for(var i in obj) {
                $html+='<div class="cart_good_item" data-id="'+obj[i]['id']+'">';
                $html+='<img src="'+obj[i]['indexpic']+'" class="cgood_img" alt=""/>';
                $html+='<div class="cgood_info">';
                $html+='<div class="cg_title">'+obj[i]['name']+'</div>';
                $html+='<div class="cg_info"><span class="price">&yen;'+obj[i]['price']+'</span> x'+obj[i]['amount']+'</div>';
                $html+='</div>';
                $html+='<div class="clr"></div>';
                $html+='</div>';
                amount+=parseInt(obj[i]['amount']);
                totalMoney +=  (obj[i]['amount'] *obj[i]['price']);
            }
            $('#CartNo').html(amount);
            $('.js_good_total_num').html(amount);
            $('.good_total').html('&yen;'+totalMoney);
            $('#cart_good_box').html($html);
        }

    }
}

function fly(event,indexpic){
    var position = $("#CartNo").position(),
        offset = $("#CartNo").offset(),
        flyer = $('<img width="80" class="u-flyer" src="'+indexpic+'">');
    flyer.fly({
        start: {
            left:  event.clientX-50,
            top: event.clientY-250
        },
        end: {
            left: offset.left,
            top: position.top-100,
            width: 0,
            height: 0
        },
        onEnd: function(){
           $('.u-flyer').remove();
           $('#cart_box').slideDown();
            setTimeout(function(){$('#cart_box').slideUp()},2000 )
        }
    });
}
/**
 * 进入购物车按钮
 */
function checkout(){
    var userid = $('#myaccount').data('userid');
    if(userid){

    }
}

/**
 * 获取商品信息
 * @param $objPage
 */
function getCartGoodStock($objPage){
    var myfood = $.cookie($goodKey), $goodIds='';
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
                            }
                        }
                        var json = $.toJSON(myfood_array);
                        $.cookie($goodKey,json,{
                            "path":"/"
                        });
                    }
                    if($objPage=='iscart'){
                        getCartData();
                    }
                })
            }
        }
    }
};

function getCartData(){
    var myfood = $.cookie("myfood"), $html = '', totalMoney = 0, totalNum= 0;
    if (myfood) {
        var obj = $.parseJSON(myfood);
        if (obj) {
            for (var i in obj) {
                $html += '<div class="itemg-li jsCart" id="js_goods_' + obj[i]['id'] + '" data-id="' + obj[i]['id'] + '" data-price="' + obj[i]['price'] + '"  data-indexpic="' + obj[i]['indexpic'] + '" data-name="' + obj[i]['name'] + '" data-stock="' + obj[i]['stock'] + '">';
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
                    $html += '<div class="fl g_btn cartbtn">';
                    $html += '<div class="cat_cart_num fl " onclick="prepGood('+obj[i]['id']+')"></div>';
                    var goodsnum = 0;
                    if(parseInt(obj[i]['amount'])>parseInt(obj[i]['stock'])){ //库存小于当前购物车商品数量
                        goodsnum = parseInt(obj[i]['stock']);
                    }else{
                        goodsnum = parseInt(obj[i]['amount']);
                    }
                    $html += '<input type="text" class="num cartgoodnum fl tc"  data-id="' + obj[i]['id'] + '" id="js_goods_num_' + obj[i]['id'] + '" value="' + goodsnum + '"/>';
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




















































