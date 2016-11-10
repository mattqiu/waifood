/**
 * 单选框设置
 * @param $obj 点击对象
 * @param valObj 赋值对象
 */
function setRadioVal($obj,valObj){
    if(!$($obj).hasClass('on')){
        $($obj).addClass('on').siblings().removeClass('on');
        $(valObj).val($($obj).attr('data-val'));
    }
}

//读取cookies
function getCookie(name)
{
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");

    if(arr=document.cookie.match(reg))

        return unescape(arr[2]);
    else
        return null;
}

//设置cookie
function getDate(time){
    var myDate = new Date(),
        month =  myDate.getMonth()+ 1+ "",
        day =  myDate.getDate()+ "";
    if(month.length = 1){
        month = month < 10?"0"+month:month;
    }
    if(day.length = 1){
        day = day < 10?"0"+day:day;
    }
  return  myDate.getFullYear()+'-'+month+'-'+day;
}

//设置cookie
function setCookie(name,value,time){
    var Days = 30;
    var exp = new Date();
    if(time){
        exp.setTime(exp.getTime() + time);
    }else{
        exp.setTime(exp.getTime() + Days*24*60*60*1000);
    }
    document.cookie = name + "="+ escape (value) + ";domain=.waifood.com;path=/;expires=" + exp.toGMTString();
}

/*各种正则检测*/
function regex(value, rule){
    switch(rule){
        case "tel":rule = /^((\d{3}-\d{8})|(8\d{10})|(9\d{9})|(\d{7})|(\d{4}-\d{8})|(\d{4}-\d{7}))$/;break; //普通电话号码
        case "short_tel":rule = /^(\d{3,6})$/;break;  //短号
        case "email":rule = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;break;
        case "qq":rule = /^\d{6,11}$/;break;
        case "number":rule = /^[1-9](\d+)?$/;break;
        case "time":rule = /^[0-9]{4}-[0-9]{2}-[0-9]{1,2}\s+[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/;break;//2012-03-13 11:09:11
        case "date" :rule = /^[0-9]{4}-[0-9]{2}-[0-9]{1,2}$/;break;
        case "integer" :rule = /^[-+]?[0-9](\d+)?$/;break; //正负数
        case "idCardNum" :rule = /^[0-9]{17}[0-9X]$/;break;
        case 'mob':rule=/^(\d{10,11})$/;break; //手机号码
    }
    return rule.test(value);
}

/*获取url的对应参数*/
function getUrlParam(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if(r != null){
        return unescape(r[2]); //返回参数值
    }
    return null;    //如果没有，则返回null
}

/**时间格式化
 * @param $time
 * @return string
 */
function getDateFormat($month){
   // var myDate = new Date();
   //// var date =  myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate();        //获取当前年份(2位)
   // var $m= parseInt(myDate.getMonth())+1;
    var $mn = '';
    switch($month){
        case 1 :$mn ='Jan';break;
        case 2 :$mn ='Feb';break;
        case 3 :$mn ='Mar';break;
        case 4 :$mn ='Apr';break;
        case 5 :$mn ='May';break;
        case 6 :$mn ='Jun';break;
        case 7 :$mn ='Jul';break;
        case 8 :$mn ='Aug';break;
        case 9 :$mn ='Sept';break;
        case 10 :$mn ='Oct';break;
        case 11 :$mn ='Nov';break;
        case 12 :$mn ='Dec';break;
    }
    return $mn;
}

//自动消失的弹出提示框
function clearpop(txt,time){
    if(!time){time=1500;}
    if($("#clearpop").length>0){
        $("#clearpop").html("<p>"+txt+"</p>").show();
    }else{
        var str='<div id="clearpop"><p>'+txt+'</p></div>';
        $("body").append(str);
    }
    setTimeout(function(){$("#clearpop").hide();},time);
}


/*弹出框使用方法:
 *getMask().maskShow({"speed":100,"filter":0.5,"jump_speed":0.7,"jump_num":0,"width":400,"tit":"弹出框标题","cont":"#cont"});
 *closeMask(); 关闭
 */
var mymask;
// 获取弹出框
function getMask(){
    if(mymask==null){mymask = new  Mask();}
    return mymask;
}
//点任何地方关闭弹出框
$('body').click(function(){
   closeMask();
})
//关闭弹出框
function closeMask(){
    $(".clo").click();
}
function Mask(){
    this.wrap = ".maskWrap";//遮罩层总背景
    this.box = ".homepop";//遮罩层
    this.move = ".move";//拖拽
    this.closeBox = ".clo";//关闭
    this.inside = ".inside";//负责存放内容
    this.pm = 5;//拖动层由于父层 padding 、margin 、 position引起的间差,目前设置为手动调节
    this.t = 100;//移动速度
    this.o = 0.5;//遮罩透明度
    this.k = 0.7;//回弹速度
    this.n = 1;//回弹次数
    this.width = 400;//弹出框宽度
    this.tit = "弹出框标题";//标题
    this.cont="#cont";//弹出框div的id/class
    this.closeCallBack=function(){};//回调函数
}
Mask.prototype = {
    constructor : Mask,
    maskShow : function(){
        if(arguments.length > 0){
            var arr = arguments[0];
            if(arr.pm !== undefined){
                this.pm = arr.pm;
            }
            if(arr.speed !== undefined){
                this.t = arr.speed;
            }
            if(arr.filter !== undefined){
                this.o = arr.filter;
            }
            if(arr.jump_speed !== undefined){
                this.k = arr.jump_speed;
            }
            if(arr.jump_num !== undefined){
                this.n = arr.jump_num;
            }
            if(arr.width !== undefined){
                this.width = arr.width;
            }
            if(arr.tit !== undefined){
                this.tit = arr.tit;
            }
            if(arr.cont !== undefined){
                this.cont = arr.cont;
            }
            if(arr.closeCallBack !== undefined){
                this.closeCallBack = arr.closeCallBack;
            }
        }
        //组建基本的遮罩层
        if($(this.box).html()==null){
            var str = '<div class="maskWrap"></div>';
            str+='<div class="homepop">';
            str+='<div class="move">';
            str+='  <a class="clo">关闭</a>';
            str+='  <span>'+this.tit+'</span>';
            str+='</div>';
            str+='<div class="inside"></div>';
            str+='</div>';
            $("body").prepend(str);
        }else{
            $(this.move+" span").empty();
            $(this.move+" span").html(this.tit);
            $(this.inside).children().hide();
        }
        $(this.inside).append($(this.cont));
        $(this.cont).show();
        var _closeBox  =  $(this.closeBox),
            _wrap  =  $(this.wrap),
            _move  =  $(this.move),
            _box  =  $(this.box),
            _callBack  = this.closeCallBack,
            pm =  this.pm,
            t  =  this.t,
            k  =  this.k,
            o  =  this.o,
            n  =  this.n;
        $(_box).width(this.width);//定义宽度
        var H = $(window).height(),
            W = $("body").width();
        w = _box.width(),
            h = _box.height(),
            v = $("body").height(),
            bH = v - H >= 0 ? v : H;//body高度
        window.onresize = function(){
            H = $(window).height(),
                W = $(window).width(),
                v = $("body").height(),
                bH = v - H >= 0 ? v : H;
        }
        var flag = false,
            time = null,
            zd = {x:0,y:0},
            md = {x:0,y:0};
        _move.mousedown(function(e){
            var p = _move.offset();
            md.x = e.pageX - p.left;
            md.y = e.pageY - p.top;
            flag = true;
        });
        $(document).mousemove(function(e){
            if(flag){
                time = setTimeout(function(){
                    z(e.pageX, e.pageY);
                }, 30);
                return false;
            }
        }).mouseup(function(){
            flag = false;
        });
        function z(X, Y){
            zd.x = X - md.x - pm;
            zd.y = Y - md.y - pm;
            if(X - md.x <= 0){
                zd.x = 0
            }
            if(Y - md.y <= 0){
                zd.y = 0
            }
            if(zd.x >= W - w){
                zd.x = W - w
            }
            if(zd.y >= bH - h){
                zd.y = bH - h
            }
            if(X <= 0 || Y <= 0 || X >= W || Y >= bH){
                flag = false;
            }
            _box.css({
                "left": zd.x + 'px',
                "top": zd.y + 'px'
            });
        }
        var cH = H/2 - h/2 + $(window).scrollTop(),
            vh= v - h >= 0 ? v : h,
            num = 1,
            j = 3;
        var tt=vh / 2 - h / 2;
        _wrap.show().css("height", bH + 'px').stop(true, false).animate({
            "opacity": o
        }, t, function(){
            _box.show().css({
                "left": W/2 - w/2 + 'px',
                // "top": -h + 'px'
                "top": -h + 'px'
            }).stop(true, false).animate({
                //  "top": vh/2-h/2 + 'px'
                "top": cH + 'px'
            }, t*k);
            function start(n){
                num *= k;
                j *= 2;
                if(n<=1){
                    return false;
                }
                _box.animate({
                    "top": cH - h/j + 'px'
                }, t*k*num).animate({
                    "top": cH + 'px'
                }, t*k*num);
                arguments.callee(n-1);
            }
            start(n);
        });
        _wrap.clearQueue();
        _closeBox.click(function(){//关闭
            _box.stop(true, false).animate({
                "top": -h + 'px'
            },{
                duration: t*6,
                queue: false
            })
                .delay(t/2, "del")
                .queue("del", function(next){
                    $(this).stop(true, false).animate({
                        "top": -h + 'px'
                    }, t, function(){
                        _wrap.animate({
                            "opacity": 0
                        }, t, function(){
                            $(this).hide();
                        });
                        _box.hide();
                    });
                    next();
                }).dequeue("del");
            _callBack();
            _box.clearQueue();
            return false;
        });
    }
}
/*table拖拽效果排序:
 *getHauling().haulingShow({"callBack":函数名,"box":".drag_module_box"});//参数选填
 * css部分
 *.drag_module_box {  position:relative;display:block;width:100%;height: auto;text-align:center;}包含框
 *.drag_module_main {  cursor:pointer;}//普通一共元素
 *.drag_module_maindash { position:absolute;display:table;width:100%;border:1px dotted black;z-index:22;background:#666;opacity: 0.5}//被拖拽对象
 *.drag_module_maindash td{border-bottom:1px dotted #000000;border-right:1px dotted #ccc;position:relative}
 *.drag_module_dash {line-height:28px;position:static; border:1px solid green;}//临时填充对象
 * .haulingBtn{}//拖拽触发对象
 */
var myHauling;
// 获取拖拽元素
function getHauling(){
    if(myHauling==null){myHauling = new  Hauling();}
    return myHauling;
}
function Hauling(){
    this.box = ".drag_module_box";//包含框
    this.list = "drag_module_main";//包含框
    this.currentObj = null;//拖拽对象
    this.tempObj = null;//临时对象
    this.currentId = 0;////被拖拽元素在列表中的id
    this.tarObj=null;//目标元素
    this.currentObjHeight = 0;//元素高度
    this.moveStatus = false;//拖拽状态
    this.mousePos = { x: 0, y: 0 };//鼠标元素偏移量
    this.currentPos ={ x: 0, y: 0, x1: 0, y1: 0 };//拖拽对象的当前坐标
    this.tarPos = { x: 0, y: 0, x1: 0, y1: 0 };//目标对象的坐标
    this.callBack=function(){};//回调函数
    this.dragModuleDashStr="";
}
Hauling.prototype = {
    constructor : Hauling,
    closeShow:function(){
        if (arguments.length > 0) {
            var arr = arguments[0];
            if (arr.box !== undefined) {
                this.box = arr.box;
            }
        }
        $( this.box ).undelegate();
    },
    haulingShow : function() {
//        参数：
        if (arguments.length > 0) {
            var arr = arguments[0];
            if (arr.box !== undefined) {
                this.box = arr.box;
            }
            if (arr.listClassStr !== undefined) {
//                console.log('arr.box:'+arr.box);
                this.list = arr.listClassStr;
            }
            if(arr.callBack!==undefined){
                this.callBack= arr.callBack;
            }
            if(arr.dragModuleDashStr!==undefined){
                this.dragModuleDashStr=arr.dragModuleDashStr;
            }
        }
        var  _box  = this.box,
            _currentObj = this.currentObj,
            _currentId = this.currentId ,
            _currentObjHeight=this.currentObjHeight ,
            _list=this.list,
            _tempObj =this.tempObj,
            _moveStatus =this.moveStatus,
            _mousePos = this.mousePos,
            _currentPos = this.currentPos,
            _tarPos =  this.tarPos,
            _tarObj= this.tarObj,
            _callBack= this.callBack,
            _dragModuleDashStr= this.dragModuleDashStr,
            _initLeft= 0,
            _initTop= 0,
            _currentBox=null;
        $(_box).delegate(".haulingBtn","mousedown",function(e){
            var thisObj=$(this);
            _currentObj = thisObj.closest("."+_list);
            _currentBox = thisObj.closest(_box);
            _initLeft=_currentBox.offset().left;
            _initTop=_currentBox.offset().top;
            _currentId = _currentObj.index();
            _currentObjHeight = _currentObj.height();// 当前项高度
            _moveStatus = true;
            _mousePos.x = e.pageX - _currentObj.offset().left;//鼠标元素相对偏移量
            _mousePos.y = e.pageY - _currentObj.offset().top;
            var tempLeft=_currentObj.offset().left - _initLeft;
            var tempTop= _currentObj.offset().top - _initTop ;
            _currentObj.attr("class","drag_module_maindash").css({left:tempLeft+'px',top: tempTop +"px"});
            $(_dragModuleDashStr).insertBefore(_currentObj);
        });
        //鼠标移动
        $(this.box).mousemove(function(e){
            if (!_moveStatus) return false;
            _currentPos.x = e.pageX - _mousePos.x  - _initLeft ;
            _currentPos.y = e.pageY - _mousePos.y - _initTop ;
            _currentPos.y1 = _currentPos.y + _currentObjHeight; // 拖拽元素随鼠标移动
            _currentObj.css({left: _currentPos.x + 'px',top: _currentPos.y + 'px'});// 拖拽元素随鼠标移动 查找插入目标元素
            var  $main = _currentBox.find("."+_list); // 局部变量：按照重新排列过的顺序  再次获取 各个元素的坐标，
            _tempObj = $(".drag_module_dash"); //获得临时 虚线框的对象
            $main.each(function () {
                _tarObj = $(this);
                _tarPos.x = _tarObj.offset().left;
                _tarPos.y = _tarObj.offset().top - _initTop ;
                _tarPos.y1 = _tarPos.y + _tarObj.height()/2;
                tarFirst = $main.eq(0); // 获得第一个元素
                tarLast=$main.last();
                tarFirstY = tarFirst.offset().top  - _initTop  + _currentObjHeight/2 ; // 第一个元素对象的中心纵坐标
                tarLastY = tarLast.offset().top   - _initTop  + _currentObjHeight/2 ; // 第一个元素对象的中心纵坐标
                if (_currentPos.y <= tarFirstY) {//拖拽对象 移动到第一个位置
                    _tempObj.insertBefore(tarFirst);
                }
                if (tarLastY<= _currentPos.y ) {//拖拽对象 移动到第最后位置
                    _tempObj.insertBefore(tarLast);
                }
                if (_currentPos.y >= _tarPos.y - _currentObjHeight/2 && _currentPos.y1 >= _tarPos.y1 ) { //判断要插入目标元素的 坐标后， 直接插入
                    _tempObj.insertAfter(_tarObj);
                }
            });
        });
        $(document).mouseup(function(){
            if (!_moveStatus) {
                return false;
            }else{
                if(_currentObj==null) return false;
                _currentObj.insertBefore(_tempObj);  // 拖拽元素插入到 虚线div的位置上
                _currentObj.attr("class",_list ); //恢复对象的初始样式
                $('.drag_module_dash').remove(); // 删除新建的虚线div
                var num = _currentObj.index();
                _moveStatus=false;
                if(num != _currentId){//不是回到初始位置
                    _callBack(_currentObj,$("."+_list));//参数当前拖拽对象|被拖拽对象作用群
                }
            }
            $(this.box).stop();
        });
    }
}