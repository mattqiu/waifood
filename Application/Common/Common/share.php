<?php

//验证验证码的信息
function isVerifyCorrect(){
    if(empty($_REQUEST['verify'])){
        return false;
    }
    $inVerfiy = $_REQUEST['verify'];
    $inVerfiy = strtoupper($inVerfiy);
    $verfiy = session('verify');
    if(md5($inVerfiy)==$verfiy){
        return true;
    }
    return false;
}

//生成验证码
 function verify(){
   \Common\Model\ImagesModel::buildImageVerify(rand(3, 5), 1, "png");
}

/**
 * 程  序：iswap.php判断是否是通过手机访问
 * 版  本：Ver 1.0 beta
 * 修  改：奇迹方舟(imiku.com)
 * 最后更新：2010.11.4 22:56
 * @return boolean 是否是移动设备
 * 该程序可以任意传播和修改，但是请保留以上版权信息!
 */
function isMobile(){
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if(isset($_SERVER['HTTP_X_WAP_PROFILE'])){
        return true;
    }
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if(isset($_SERVER['HTTP_VIA'])){
        //找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap")?true:false;
    }
    //脑残法，判断手机发送的客户端标志,兼容性有待提高
    if(isset($_SERVER['HTTP_USER_AGENT'])){
        $clientkeywords = array(
            'nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile',
            'MicroMessenger'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if(preg_match("/(".implode('|', $clientkeywords).")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if(isset($_SERVER['HTTP_ACCEPT'])){
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml')!==false)&&(strpos($_SERVER['HTTP_ACCEPT'], 'text/html')===false||(strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml')<strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
            return true;
        }
    }
    return false;
}

function isFromWeixin(){
    return isset($_SERVER['HTTP_USER_AGENT'])&&strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')!==false?true:false;
}

function isIphone(){
    if(!isset($_SERVER['HTTP_USER_AGENT'])){
        return false;
    }
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if(strpos($agent, "iphone") || strpos($agent, "ipad")){
        return true;
    }
    return false;
}

// 是否跳转到wap站点
function isEnterWap(){
    if(isInDefautlGroup()){
        return true;
    }
    return false;
}

// 处于默认分组
function isInDefautlGroup(){
    $group = getGroupName();
    if(!empty($group)){
        $group = strtolower($group);
        if(in_array($group, array("shop","home", "admin","adminzlwx","wap", "wechat",))){
            return false;
        }
    }
    return true;
}

function getAppName(){
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $sstmp = explode("/", $scriptName);
    return $sstmp[1];
}

// 获取当前项目分组名
//  返回 ‘’ 表示默认分组
// /android/index/user  -->android
// /index/user----> 'index'
// /food/index/user ---> 'index'
// /food/android/index/user ---->"android"
//  / ----->''
//  /?ee=ffff  ---->''
//  /food?eee=fff  ---->''
function getGroupName(){
    if(!empty($_SERVER['REQUEST_URI'])){
        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode("?", $uri);
        $path = $uri[0];
        $tmp = explode("/", $path);
        $curApp = getAppName();
        if(empty($tmp)||count($tmp)<2){
            return "";
        }else{
            if($tmp[1]==$curApp){
                if(!empty($tmp[2])){
                    return $tmp[2];
                }
                return "";
            }
            return $tmp[1];
        }
    }else{
        return "";
    }
}

function sendEmail($to,$subject){
    $body=lbl('tpl_register');
    if(!isN($body)){
        $preg="/{(.*)}/iU";
        $n=preg_match_all($preg,$body,$rs);
        $rs=$rs[1];
        if($n>0){
            foreach($rs as $v){
                if(isset($data[$v])){
                    $oArr[]='{'.$v.'}';
                    $tArr[]=$data[$v];
                    $body=str_replace($oArr,$tArr,$body);
                }
            }
        }
        if(send_mail($to,$subject,$body)){
            \Think\Log::write('send reg email success');
        }else{
            \Think\Log::write('send reg email error');
        }
    }
}

function getRealIp(){
    static $realip = NULL;
    if ($realip !== NULL)
    {
        return $realip;
    }
    if (isset($_SERVER))
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr AS $ip)
            {
                $ip = trim($ip);

                if ($ip != 'unknown')
                {
                    $realip = $ip;

                    break;
                }
            }
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            if (isset($_SERVER['REMOTE_ADDR']))
            {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                $realip = '0.0.0.0';
            }
        }
    }
    else
    {
        if (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_CLIENT_IP'))
        {
            $realip = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    return $realip;
}
?>