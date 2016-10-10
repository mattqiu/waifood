
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
function setCookie(name,value,time){
    var Days = 30;
    var exp = new Date();
    if(time){
        exp.setTime(exp.getTime() + time);
    }else{
        exp.setTime(exp.getTime() + Days*24*60*60*1000);
    }
    document.cookie = name + "="+ escape (value) + ";domain=.3cfood.com;path=/;expires=" + exp.toGMTString();
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
