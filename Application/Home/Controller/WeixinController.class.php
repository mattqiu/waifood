<?php
// 购物车类
namespace Home\Controller;

use Common\Model\OrderModel;
use Common\Model\UserModel;
use Home\Model\WeixinModel;
use Think\Controller;
use Think\Log;

class WeixinController extends Controller {

    protected $conf='';
    private  $returnUrl=''; //回调地址

    public function _initialize() {
        $this->conf['appid'] = C('WECHAT_APPID');
        $this->conf['mchid'] = C('WEICHAT_MCHID');
        $this->conf['key'] = C('WEICHAT_KEY');
        $this->conf['appsecret'] = C('WECHAT_APPSECRET');
        $this->returnUrl= C('DOMAIN')."home/";
    }

    /**
     * 微信登录回调方法
     * @throws Exception
     */
    public function weixin_callback(){
        $code = I('get.code');
        if (empty($code)) {
            GLog('weixin:login:code','code is empty');
            return false;
        }
        $state = I('get.state');
        $verifyState = session('verify_state') ? session('verify_state'): I('get.rand');
        if (!$state or $state != $verifyState) {
            GLog('weixin:login:state','Before and after the state');
            return false;
        }
        $appid = C('WECHAT_APPID');
        $appsecret = C('WECHAT_APPSECRET');
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code" ;
        $data = json_decode(WeixinModel::http_get($url), true);
        trace("user ".var_export($data ,true));
        GLog('weixin:login:token',json_encode($data));
        if (!isset($data['access_token'])) {
            GLog('weixin:login:token','获取access_token错误返回');
            return false;
        }
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $data['access_token'] . '&openid=' . $data['openid'];
        $user = json_decode(WeixinModel::http_get($url), true);
        trace("user ".var_export($user ,true));
        openid($data['openid']);//缓存openid
        S('openid_' . openid(),$user);//缓存微信用户信息
        GLog('weixin:login:user',json_encode($user));
        if(UserModel::getUserByOpenid($data['openid'])){
            if( UserModel::loginWechat($data['openid'])){
                if( session('gocashier')){
                    session('gocashier','');
                    redirect('/m_cashier');
                }else{
                    redirect('/');
                }
            }
        }else{
            if( UserModel::createWechatUser($data['openid'])){
                if( session('gocashier')){
                    session('gocashier','');
                    redirect('/m_cashier');
                }else{
                    redirect('/');
                }
            }
        }

    }

    /******************************************微信支付******************************************************************/
    /**
     * 商户个人微信-订单-二维码支付
     */
    public function payCode(){
        $orderno = I('orderno', 0);
        if (!$orderno) {
            exit;
        }
        $path = WeixinModel::getOrderSeflWxQrcodePay($orderno);
        if ($path) {
            echo file_get_contents($path);
        }
        exit;
    }

    /**
     * 微信付款后异步请求
     */
    public function weixinCallback(){
        include(APP_PATH.'../WxPay.pub.config.php');
        include(APP_PATH.'../WxPayPubHelper.php');
        $notify = new \Notify_pub();
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        $orderId =  $notify->data['out_trade_no'];
        $wx_order_id=$notify->data['transaction_id'];
        GLog("weixin","weixinCallback orderid:".$orderId,Log::INFO);
        if(strpos($orderId, "_") !== false){
            $arr = explode("_", $orderId);
            $orderId = $arr[0];
        }
        GLog("weixin","weixin conf:".json_encode( $this->conf));
        GLog("weixin","orderId:".$orderId,Log::INFO);
        $orderInfo = OrderModel::getOrderByOrderno($orderId);
        GLog("weixin","weixinCallback data:".json_encode($notify),Log::INFO);
        if(empty($orderInfo)){
            GLog("weixin","cann't get order info",Log::ERR);
        }else{
            // 初始化配置信息
            new \WxPayConf_pub($this->conf);
            if($notify->checkSign() == FALSE){
                GLog("weixin","check sign fail ",Log::ERR);
            }else{
                if($notify->data['return_code'] == 'FAIL') {
                    GLog("weixin",'支付 return_code fail ',Log::ERR);
                }elseif($notify->data['result_code'] == 'FAIL'){
                    GLog("weixin",'支付 result_code fail',Log::ERR);
                }else{
                    $path = RUNTIME_PATH . '/WeiXinPay/'.$orderId.'.png';
                    $totalPrice = round($notify->data['total_fee']/100,2);
                    GLog("weixin","weixinCallback notify:".json_encode($notify->data));
                    $rs = OrderModel::finishOnlineOrderPay($orderId,$totalPrice,OrderModel::PAY_WEICHAR,$wx_order_id);
                    delfile($path);
                    if($rs){
                        $notify->setReturnParameter('return_code', 'SUCCESS');//设置返回码
                        echo $notify->returnXml();
                        exit();
                    }else{
                        GLog("weixin","更新订单支付状态失败,orderId:".$orderId,Log::ERR);
                    }
                }
            }
            exit();
        }
    }

    /**
     *  订单二维码支付-微信主动获取订单信息地址
     */
    public function weixinGetInfo(){
        include(APP_PATH.'../WxPay.pub.config.php');
        include(APP_PATH.'../WxPayPubHelper.php');
        $nativeCall = new \NativeCall_pub();
        //接收微信请求
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        GLog("weixin","weixinGetInfo receive xml:".$xml,Log::INFO);
        $nativeCall->saveData($xml);
        $productId = $nativeCall->getProductId();
        GLog("weixin","get productId:".$productId,Log::INFO);
        $order = OrderModel::getOrderByOrderno($productId);
        if (empty($order)) {
            GLog("weixin","get order empty:",Log::ERR);
            $nativeCall->setReturnParameter('return_code', 'SUCCESS');//返回状态码
            $nativeCall->setReturnParameter('result_code', 'FAIL');//业务结果
            $nativeCall->setReturnParameter('err_code_des', '此商品无效');//业务结果
        } elseif ($order['pay'] == OrderModel::PAID) {
            GLog("weixin","get order is payed:",Log::INFO);
            $nativeCall->setReturnParameter('return_code', 'SUCCESS');//返回状态码
            $nativeCall->setReturnParameter('result_code', 'FAIL');//业务结果
            $nativeCall->setReturnParameter('err_code_des', '已支付');//业务结果
        } else {
            if (empty($this->conf)) {
                GLog("weixin","account is empty",Log::ERR);
                $nativeCall->setReturnParameter('return_code', 'SUCCESS');//返回状态码
                $nativeCall->setReturnParameter('result_code', 'FAIL');//业务结果
                $nativeCall->setReturnParameter('err_code_des', '未开通微信支付');//业务结果
            } else {
                new \WxPayConf_pub($this->conf);
                if ($nativeCall->checkSign() == FALSE) {
                    GLog("weixin","check sign fail",Log::ERR);
                    $nativeCall->setReturnParameter("return_code","FAIL");//返回状态码
                    $nativeCall->setReturnParameter("return_msg","签名失败");//返回信息
                } else {
                    $unifiedOrder = new \UnifiedOrder_pub();
                    $unifiedOrder->setParameter('body', 'order ID： '.$order['orderno']);//商品描述
                    $unifiedOrder->setParameter('detail', 'weichat pay');//商品详情
                    //$unifiedOrder->setParameter('detail', $order['order_content']);//商品详情
                    $unifiedOrder->setParameter('out_trade_no', $order['orderno']."_".rand(0, 1000));//商户订单号
                    $unifiedOrder->setParameter('total_fee', $order['amount']*100);
                    $unifiedOrder->setParameter('notify_url' ,WeixinModel::ORDER_WX_NOTIFY_URL);//通知地址
                    $unifiedOrder->setParameter('trade_type', 'NATIVE');//交易类型
                    $unifiedOrder->setParameter('product_id', $productId);//用户标识
                    $prepay_id = $unifiedOrder->getPrepayId();
                    $nativeCall->setReturnParameter('return_code', 'SUCCESS');//返回状态码
                    $nativeCall->setReturnParameter('result_code', 'SUCCESS');//业务结果
                    $nativeCall->setReturnParameter('prepay_id', $prepay_id);//预支付ID
                }
            }
        }
        $returnXml = $nativeCall->returnXml();
        GLog("weixin",'返回信息:'.json_encode($returnXml),Log::INFO);
        echo $returnXml;
    }

    /**
     * 获取code
     */
    public function jsapicall(){
        $uri = session('requesrUri');
        $code = I('get.code');
        if (!$uri or !$code) {
            redirect('/home');
        } else{
            redirect($uri . '&code=' . $code);
        }
        exit;
    }

}
?>