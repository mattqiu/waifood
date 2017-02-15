/////////////////////////////////////////////////商品管理///////////////////////////////////////////////////////////////////////
/************保存选择商品（用于）*****************/
function addgood(id,$goodKey){
    var myfood = $.cookie($goodKey),
        indexpic = $('#js_goods_'+id).data("indexpic"),
        negative = $('#js_goods_'+id).data("negative"),
        num = $('#js_goods_'+id).data("num"),
        name = $('#js_goods_'+id).data("name"),
        price = $('#js_goods_'+id).data("price"),
        unit = $('#js_goods_'+id).data("unit");
    if(!num){
        num = 1;
    }
    var myfood_array  = $.parseJSON(myfood);
    // 看该产品是否存在
    if(myfood_array){
        if(!myfood_array[id]){
            myfood_array[id] = {"id":id,"name":name,"indexpic":indexpic,"negative":negative,"num":num,"unit":unit,"price":price};
        }
    }else{
        myfood_array = {};
        myfood_array[id] = {"id":id,"name":name,"indexpic":indexpic,"negative":negative,"num":num,"unit":unit,"price":price};
    }
    var json = $.toJSON(myfood_array);
    $.cookie($goodKey,json,{
        "path":"/"
    });
}

function delgood(id,$goodKey){
    var myfood = $.cookie($goodKey);
    var myfood_array  = $.parseJSON(myfood);
    // 看该产品是否存在
    if(myfood_array){
        if(myfood_array[id]){ //存在
            myfood_array[id]= undefined;
        }
    }
    var json = $.toJSON(myfood_array);
    $.cookie($goodKey,json,{ "path":"/" });
}




/********************************可负销售判读*****************************************/
$('#infoform input[name=negative]').parents().siblings('.idealforms_select_menu').find('li').click(function(){
    var good_type = $('#infoform input[name=good_type]').val();
    //复合组合商品可负销售取决于子商品是否支持可负销售
    if(good_type>0 && $(this).data('value') == 1){ //仅在选择可负销售是判断
        var key = 'admingood',
            myfood = $.cookie(key);
        if(myfood){
            var myfood_array = $.parseJSON(myfood);
            if (myfood_array) {
                for (var i in myfood_array) {
                    if (myfood_array[i]) {
                        if (good_type == 1) { //复合商品
                            if(!myfood_array[i]['negative']){ //子商品不支持可负销售
                                setSelectSelected('#infoform input[name=negative]', myfood_array[i]['negative']);
                                clearpopj('商品ID:'+myfood_array[i]['id']+'    不支持可负销售', "error",true);
                                return  false;
                            }
                        }else{ //其他组合商品只要有一个都不支持可负销售都不允许可负销售
                            if(!myfood_array[i]['negative']){ //子商品不支持可负销售
                                setSelectSelected('#infoform input[name=negative]', myfood_array[i]['negative']);
                                clearpopj('商品ID:'+myfood_array[i]['id']+'    不支持可负销售', "error",true);
                                return  false;
                            }
                        }
                    }
                }
            }
        }
    }
})
/*****************提交******************/
function modifyContent(){
    var images = '';
    var status = $('#infoform input[name=status]').val(), //商品状态
        pid = $('#infoform input[name=pid]').val(), // 分类
        supplyid = $('#infoform input[name=supplyid]').val(), //供应商
        title = $('#infoform input[name=title]').val(),//标题
        namecn = $('#infoform input[name=namecn]').val(),//中文标题
        short_name = $('#infoform input[name=short_name]').val(),//内部简称
        pinyin = $('#infoform input[name=pinyin]').val(),//简称拼音码
        price = $('#infoform input[name=price]').val(),//价格
        price1 = $('#infoform input[name=price1]').val(),//市场价
        stock = $('#infoform input[name=stock]').val(),//库存
        unit = $('#infoform input[name=unit]').val(),//库存单位
        stock_type = $('#infoform input[name=stock_type]').val(),//库存类型
        stock_info = $('#infoform textarea[name=stock_info]').val(),//库存关系
        shelf_life = $('#infoform input[name=shelf_life]').val(),//保质期
        expire_date = $('#infoform input[name=expire_date]').val(),//过期日
        origin_id = $('#infoform input[name=origin_id]').val(),//产地
        storage_id = $('#infoform input[name=storage_id]').val(),//产地
//                storage = $('#infoform input[name=storage]').val(),//保存方法
        brand = $('#infoform input[name=brand]').val(),//品牌
        notice = $('#infoform input[name=notice]').val(),//内部留言
        good_type = $('#infoform input[name=good_type]').val(),//商品类型
        negative = $('#infoform input[name=negative]').val(),//可负销售
        content = $('#infoform textarea[name=content]').val(),//英文详情
        contentcn = $('#infoform textarea[name=contentcn]').val(),//中文详情
        name = $('#infoform #name').val(),//内容标示-----？
        hits = $('#infoform input[name=hits]').val(),//点击次数
    //图片
        indexpic = $('#picform input[name=indexpic]').val(),//列表图
    //SEO
        keywords = $('#seoform input[name=keywords]').val(),// 关键字
        description = $('#seoform input[name=description]').val();//介绍

    var groupid = '',$flag=0;
    if(good_type ==2){
        $('#good_type_info .js_goods_li').each(function(){
            if($(this).find('input[name=num]').val()<1){
                $flag = 1;
            }
            groupid += $(this).data('id')+',';
            groupid += $(this).find('input[name=num]').val()+'|';
        })
    }else{ //正常商品
        groupid = '';
    }
     if(good_type ==2){
         if($flag==1){
             clearpopj('组合子商品数量必须大于0', "error",true);
             return  false;
         }
        if(!groupid){
            clearpopj('请至少有一件商品组合', "error",true);
            return  false;
        }
    }else{ //正常商品
        groupid = '';
    }

    //相册图
    $('#imgboxs img.goods_img').each(function(){
        images+=$(this).data('src')+'|';
    })
    if(!pid){
        clearpopj('请选择分类', "error",true);
        return  false;
    }
    if(!title){
        clearpopj('请填写商品名', "error",true);
        return  false;
    }
    if(!price){
        clearpopj('请填写商品价格', "error",true);
        return  false;
    }
    var $data = {
        id:$('#goodid').val(),
        status :status,
        pid :pid,
        supplyid :supplyid,
        title :title,
        namecn :namecn,
        short_name :short_name,
        pinyin :pinyin,
        price :price,
        price1 :price1,
        stock :stock,
        unit :unit,
        stock_type :stock_type,
        stock_info :stock_info,
        shelf_life :shelf_life,
        expire_date :expire_date,
        origin_id :origin_id,
        storage_id :storage_id,
        brand :brand,
        notice :notice,
        good_type :good_type,
        negative :negative,
        group_id :groupid,
        content :content,
        contentcn :contentcn,
        indexpic :indexpic,
        keywords :keywords,
        description :description,
        images :images,
        name :name
    }

    //没有上传图片的提交
    if(!indexpic){
        swal({
            title: '',
            text: '没有上传商品列表图，确认提交吗?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonText: "是的"
            //confirmButtonColor: "#35D374"
        }, function() {
            subContent($data);
        });
    }else{
        subContent($data);
    }
}
//提交
function subContent($data){
    $.post('/admin/cms/subContent.html',$data,function(data){
        if(data.code==200){
            clearpop(data.message, '','self');
        }else{
            clearpopj(data.message, "error",true);
        }
    })
}

function setGoodsStatus($id,val){
    var $msg ='',$html = '';
    if(val == 0 || !val){
        $msg ='请填写下架原因！';
    }else{
        $msg ='请填写上架原因！';
    }

    $html+='<form onsubmit="return false" id="goodsStatus" class="hide">';
    $html+='<input type="text" placeholder="'+$msg+'" class="input_text2" style="height: 35px;"/>';
    $html+='<p>';
    $html+='<a href="javascript:$(\'#goodsStatus input[type=text]\').val(\'实际库存为0\')" style="text-decoration:underline;">实际库存为0</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
    $html+='<a href="javascript:$(\'#goodsStatus input[type=text]\').val(\'采购延迟\')" style="text-decoration:underline;">采购延迟</a>&nbsp;&nbsp;|&nbsp;&nbsp;';
    $html+='<a href="javascript:$(\'#goodsStatus input[type=text]\').val(\'鲜货例行下架\')" style="text-decoration:underline;">鲜货例行下架</a>&nbsp;&nbsp;|&nbsp;&nbsp;<br/>';
    $html+='<a href="javascript:$(\'#goodsStatus input[type=text]\').val(\'供应商缺货\')" style="text-decoration:underline;">供应商缺货</a>';
    $html+='</p>';
    $html+='<p style="text-align: center">';
    $html+='<input type="button" class="cancel_btn" value="返回" onclick="closeMask()" />&nbsp;&nbsp;&nbsp;';
    $html+='<input type="submit" onclick="subGoodsStatus('+$id+','+val+')" class="sure_btn" value="提交" />';
    $html+='</p>';
    $html+='</form>';
    $('body').append($html);
    $("#goodsStatus").idealforms();
    getMask().maskShow({"tit": "上下架原因","width":350, "cont": "#goodsStatus"});

}

function subGoodsStatus($id,val){
   var inputValue =  $('#goodsStatus input[type=text]').val();
    if (!inputValue){
        clearpopj('请输入原因', "error",true);
        return  false;
    }
    $.post('/admin/cms/changeContentState',{id:$id,status:val,note:inputValue},function(data){
        if(data.code ==200){
            window.location.reload();
        }else{
            clearpopj(data.message, "error",true);
        }
    })
}

/*阻塞标志，防止重复下单；预设不阻塞*/
window.subBlock=false;
function subCGOrder(status,$goodKey,type,ordertype){
    var myfood =  $.cookie($goodKey),order='';
    if(!myfood){
        clearpopj('没有填写采购商品', "error",true);
        subBlock = false;
        return false;
    }
    var myfood_array = $.parseJSON(myfood);
    if(!myfood_array){
        clearpopj('没有填写采购商品', "error",true);
        subBlock = false;//解除阻塞
        return false;
    }
    for(var i in myfood_array){
        order +=  myfood_array[i]['id']+ "," +myfood_array[i]['name']+ "," +myfood_array[i]['goodtype']+ "," +myfood_array[i]['unit']+ "," +myfood_array[i]['num']+ "," +myfood_array[i]['price']+ "," +myfood_array[i]['amount'] + "," + getdefaultval(myfood_array[i]['true_num'])+ "," + getdefaultval(myfood_array[i]['true_price'])+ "," + getdefaultval(myfood_array[i]['true_amount'])+ "," + myfood_array[i]['createtime']+ "," + myfood_array[i]['leveltime']+ ","+ myfood_array[i]['dietime']+ ","+ getdefaultval(myfood_array[i]['barcode'])+"|"; //订单信息
    }

    if(!order){
        clearpopj('没有填写采购商品', "error",true);
        subBlock = false;//解除阻塞
        return false;
    }

    var data = {
        orderno:$('#selectInfo table .orderno').html(),
        operator:$('#selectInfo table .operator').html(),
        runtime:$('#selectInfo table input[name=runtime]').val(),
        supplyid:$('#selectInfo table input[name=supplyid]').val(),
        supplyid2:$('#selectInfo table input[name=supplyid2]').val(),
        total_amount:$('#selectInfo table .total_amount').html(),
        delivery_fee:$('#selectInfo table input[name=delivery_fee]').val(),
        other_fee:$('#selectInfo table input[name=other_fee]').val(),
        total_fee:$('#selectInfo table input[name=total_fee]').val(),
        note:$('textarea[name=note]').val(),
        ordertype:ordertype,
        order:order,
        status:status,
        type:type
    }
    subBlock = true;
    $.post('/admin/StockManage/subCGOrder',data,function(data){
        subBlock = false;//解除阻塞
        if(data.code == 200){
            $.cookie($goodKey,null,{ "path":"/" });
            clearpop(data.message,'','self');
        }else{
            clearpopj(data.message, "error",true);
            return false;
        }
    })
}

/**
 * 转换其他空值
 * @param val
 * @returns {*}
 */
function getdefaultval(val){
    if(!val ){
        return '';
    }else{
        return val;
    }
}

function getYlInfo(){
    var $goodKey = 'generategoodsyl';
    var myfood =  $.cookie($goodKey),order='';
    if(!myfood){
        clearpopj('没有选择原料商品', "error",true);
        return false;
    }
    var myfood_array = $.parseJSON(myfood);
    if(!myfood_array){
        clearpopj('没有选择原料商品', "error",true);
        return false;
    }
    for(var i in myfood_array){
        if(!myfood_array[i]['upnum']){
            clearpopj('请填写原料领用数量', "error",true);
            return false;
        }else if(myfood_array[i]['upnum'] > myfood_array[i]['stock']){
            clearpopj('库存不足', "error",true);
            return false;
        }
        if(myfood_array[i]['up_amount']>myfood_array[i]['stock_fee']){
            clearpopj('库存金额不足', "error",true);
            return false;
        }
        order +=  myfood_array[i]['id']+ "," +getdefaultval(myfood_array[i]['upnum'])+ "," + getdefaultval(myfood_array[i]['up_amount'])+"|"; //订单信息
    }

    if(!order){
        clearpopj('没有选择原料商品', "error",true);
        return false;
    }

    var data = {
        order:order,
        type:1// 1领用，2采购
    }
    return data;
}

function subgenerateOrder(status,type){
    var $goodKey = 'generategoodscp';
    var $yldata = getYlInfo();
    if($yldata ==false){
        subBlock = false;
        return false;
    }
    var myfood =  $.cookie($goodKey),order='';
    if(!myfood){
        clearpopj('请选择成品', "error",true);
        subBlock = false;
        return false;
    }
    var myfood_array = $.parseJSON(myfood);
    if(!myfood_array){
        clearpopj('请选择成品', "error",true);
        subBlock = false;//解除阻塞
        return false;
    }
    for(var i in myfood_array){
        order +=  myfood_array[i]['id']+ "," +myfood_array[i]['name']+ "," +myfood_array[i]['unit']+ "," +myfood_array[i]['num']+ "," +myfood_array[i]['price']+ "," +myfood_array[i]['amount'] + ","+getdefaultval(myfood_array[i]['note']) +"|"; //订单信息
    }
    if(!order){
        clearpopj('请选择成品', "error",true);
        subBlock = false;//解除阻塞
        return false;
    }
    var data = {
            orderno:$('#selectInfo table .orderno').html(),
            operator:$('#selectInfo table .operator').html(),
            runtime:$('#selectInfo table input[name=runtime]').val(),
            supplyid:$('#selectInfo table input[name=supplyid]').val(),
            supplyid2:$('#selectInfo table input[name=supplyid2]').val(),
            total_amount:$('#selectInfo table .total_amount').html(),
            delivery_fee:$('#selectInfo table input[name=delivery_fee]').val(),
            other_fee:$('#selectInfo table input[name=other_fee]').val(),
            total_fee:$('#selectInfo table input[name=total_fee]').val(),
            ordertype:3,
            order:order,
            yldata:$yldata,
            status:status,
            type:type
        }

        subBlock = true;
        $.post('/admin/StockManage/subGenerateOrder',data,function(data){
            subBlock = false;//解除阻塞
            if(data.code == 200){
                $.cookie($goodKey,null,{ "path":"/" });
                clearpop(data.message,'','self');
            }else{
                clearpopj(data.message, "error",true);
                return false;
            }
        })
}

































