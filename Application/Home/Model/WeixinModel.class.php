<?php
namespace  Home\Model;
use Common\Model\OrderModel;
use Common\Model\UserModel;
use Think\Log;
use Think\Model;

class WeixinModel extends Model {
    // 微信支付-异步回调地址
    const ORDER_WX_NOTIFY_URL = "http://www.waifood.com/home/weixin/weixinCallback";


    /**
     * GET 请求
     * @param string $url
     */
    public static  function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    public static function _weiXinVersion($ver){
        if (FROM_WEIXIN) {
            $version = end(explode('MicroMessenger/', $_SERVER['HTTP_USER_AGENT']));
            if ($version{0} >= $ver) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param type $orderId
     * @return boolean|string
     */
    public static function getOrderSeflWxQrcodePay($orderno){
        $order = OrderModel::getOrderByOrderno($orderno);
        if (empty($order)) {
            return false;
        }
        $path = RUNTIME_PATH . '/WeiXinPay/';
        if (!is_dir($path)) {
            mkdir($path , 0755, TRUE);
        }
        $path = $path . $orderno . '.png';
        $createtime = time()-filemtime ($path);
        if ($createtime>60 || !file_exists($path)){
            if(file_exists($path)){
                delfile($path);
            }
/*            new \WxPayConf_pub($conf);
            //设置静态链接
            $nativeLink = new \NativeLink_pub();
            //设置静态链接参数
            $nativeLink->setParameter("product_id",$orderno);//商品id
            //获取链接
            $product_url = $nativeLink->getUrl();
            //使用短链接转换接口
            $shortUrl = new \ShortUrl_pub();
            //设置必填参数
            //appid已填,商户无需重复填写
            //mch_id已填,商户无需重复填写
            //noncestr已填,商户无需重复填写
            //sign已填,商户无需重复填写
            $shortUrl->setParameter("long_url",$product_url);//URL链接
            //获取短链接
            $codeUrl = $shortUrl->getShortUrl();*/

            include(APP_PATH.'../WxPay.pub.config.php');
            include(APP_PATH.'../WxPayPubHelper.php');
            $conf['appid'] = C('WECHAT_APPID');
            $conf['mchid'] = C('WEICHAT_MCHID');
            $conf['key'] = C('WEICHAT_KEY');
            $conf['appsecret'] = C('WECHAT_APPSECRET');

            new \WxPayConf_pub($conf);
            $nativeLink = new \NativeLink_pub();
            $nativeLink->setParameter('product_id', $orderno);
            $codeUrl = $nativeLink->getUrl();
            Vendor('phpqrcode');
            \QRcode::png($codeUrl, $path, 'M', 4, 2);
        }
        return $path;
    }

    /**
     * 微信-订单支付
     * 异步通知地址: http://www.waifood.com/home/weixin/weixinCallback
     * @param type $order
     * @return boolean
     */
    public static function getOrderSelfWxPay($order,$userId){
        if($order['pay'] == OrderModel::PAID){
            GLog('weixin pay','订单已支付');
            return true;
        }
        $paydata = array();
        $paydata['orderno'] = $order['orderno'];
        $paydata['total_price'] = $order['amount'];
        $paydata['order_content'] = 'weixin zhi fu';
        $paydata['notify_url'] =WeixinModel::ORDER_WX_NOTIFY_URL;
        $jsApiParameters = WeixinModel::wxPay($paydata,$userId);
        return $jsApiParameters;
    }

    /**
     * 微信支付
     * @param paydata 数据
     * @param userId 用户id
     * @return string
     */
    private static function wxPay($paydata,$userId){
        include(APP_PATH.'../WxPay.pub.config.php');
        include(APP_PATH.'../WxPayPubHelper.php');
        $conf['appid'] = C('WECHAT_APPID');
        $conf['mchid'] = C('WEICHAT_MCHID');
        $conf['key'] = C('WEICHAT_KEY');
        $conf['appsecret'] = C('WECHAT_APPSECRET');
        new \WxPayConf_pub($conf);
        $jsApi = new \JsApi_pub();
        if(!empty($userId)){
            $user = UserModel::getUserById($userId);
            $openId = $user['wechatid'];
            if(!empty($openId)){
                $prepay_id = self::getWexinJsPara($paydata,$openId);
                //=========步骤3：使用jsapi调起支付============
                $jsApi->setPrepayId($prepay_id);
                $jsApiParameters = $jsApi->getParameters();
                GLog("weixinjsPa","jsPa:".json_encode( $jsApiParameters));
                return $jsApiParameters;
            }
        }
        $code = I('code');
        if (!$code) {
            session('requesrUri', $_SERVER['REQUEST_URI']);
            $url = $jsApi->createOauthUrlForCode(urlencode(\WxPayConf_pub::JS_API_CALL_URL));
            Header("Location: $url");
            exit;
        }
        GLog("weixinpay","code".$code);
        //获取code码，以获取openid
        $jsApi->setCode($code);
        $url = $jsApi->createOauthUrlForOpenid();
        $data = json_decode(self::http_get($url),true);
        $openId = $data['openid'];
        GLog("get_wx_openId","weixin get openid data".json_encode($data));
        GLog("get_wx_openId","weixin get openid $openId");
        if(empty($openId)){
            GLog("weixinpay","缺少必要参数，openid不能为空",Log::ERR);
           return false;
        }else{
            $savedata['wechatid'] = $openId;
            UserModel::modifyMember($userId,$savedata);
        }
        $prepay_id = self::getWexinJsPara($paydata,$openId);
        //=========步骤3：使用jsapi调起支付============
        $jsApi->setPrepayId($prepay_id);
        $jsApiParameters = $jsApi->getParameters();
        GLog("weixinjsPa","jsPa:".json_encode( $jsApiParameters));
        return $jsApiParameters;
    }

    /**
     * 统一下单
     * @param $config
     * @param $paydata
     * @param string $openId
     * @return mixed
     */
    private static function getWexinJsPara($paydata,$openId){
        $unifiedOrder = new \UnifiedOrder_pub();
        $unifiedOrder->setParameter('openid', $openId);
        $orderIdNew = $paydata['orderno'];
        $unifiedOrder->setParameter('body',  'order ID： '.$paydata['orderno']);//商品描述
        $unifiedOrder->setParameter('detail', $paydata['order_content']);//商品详情
        $unifiedOrder->setParameter('out_trade_no', $orderIdNew);//商户订单号
        $unifiedOrder->setParameter("total_fee", $paydata['total_price']*100);//总金额
        $unifiedOrder->setParameter("notify_url",$paydata['notify_url']?$paydata['notify_url']:\WxPayConf_pub::NOTIFY_URL);//通知地址
        $unifiedOrder->setParameter("trade_type", 'JSAPI');//交易类型
        $prepay_id = $unifiedOrder->getPrepayId();
        if($prepay_id){
            GLog("weixinjs","prepay_id:".$prepay_id);
            return $prepay_id;
        }else{
            GLog("weixinjs","获取prepay_id失败:",Log::ERR);
            return false;
        }
    }

}

?>