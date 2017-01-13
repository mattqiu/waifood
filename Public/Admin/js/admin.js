/////////////////////////////////////////////////商品管理///////////////////////////////////////////////////////////////////////
/************保存选择商品（用于）*****************/
function addgood(id){
    var $goodKey = 'admingood';
    var myfood = $.cookie($goodKey),
        indexpic = $('#js_goods_'+id).data("indexpic"),
        negative = $('#js_goods_'+id).data("negative"),
        num = $('#js_goods_'+id).data("num"),
        name = $('#js_goods_'+id).data("name");
    if(!num){
        num = 0;
    }
    var myfood_array  = $.parseJSON(myfood);
    // 看该产品是否存在
    if(myfood_array){
        if(!myfood_array[id]){
            myfood_array[id] = {"id":id,"name":name,"indexpic":indexpic,"negative":negative,"num":num};
        }
    }else{
        myfood_array = {};
        myfood_array[id] = {"id":id,"name":name,"indexpic":indexpic,"negative":negative,"num":num};
    }
    var json = $.toJSON(myfood_array);
    $.cookie($goodKey,json,{
        "path":"/"
    });
}

function delgood(id){
    var $goodKey = 'admingood';
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


/****************************************中英文详情切换***************************/
$('#language_tab li').click(function(){
    $('#language_tab li').removeClass('onselect');
    $(this).addClass('onselect');
    if($(this).data('content') == 'cn'){
        $('#content_box .content_en').hide();
        $('#content_box .content_cn').show();
    }else{
        $('#content_box .content_cn').hide();
        $('#content_box .content_en').show();
    }
})
/****************************************输入中文时获取中文对应的拼音首字母***************************/
$('#infoform input[name=short_name]').keyup(function(){
    var str = $(this).val().trim();
    if(str){
        var arrRslt = makePy(str);
        $('#infoform input[name=pinyin]').val(arrRslt);
    }else{
        $('#infoform input[name=pinyin]').val('');
    }
})
/****************************************商品类型选择***************************/
$('#infoform input[name=good_type]').parents().siblings('.idealforms_select_menu').find('li').click(function(){
    if($(this).data('value')==2){ //组合商品
        $('#infoform input[name=stock]').val(0);
        $('#infoform input[name=stock]').css('background','#e0e0e0')
        selectOk($(this).data('value'));
        $('#goods_box').show();
        $('.selgroupgoodbtn').show();
    }else{
        $('#goods_box').hide();
        $('.selgroupgoodbtn').hide();
    }
})

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