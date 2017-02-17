<?php

namespace Home\Controller;
 
class PayController extends BaseController {
    private $callbackurl = 'http://www.waifood.com/Shop/Pay/notify';
    private $returnurl = 'http://www.waifood.com/member/order';
    public function index() {
        if (IS_POST) {
            //页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
            $paytype = I('post.paytype');

            $pay = new \Think\Pay($paytype, C('payment.' . $paytype));
            $orderno=I('post.WIDout_trade_no');
            $totalfee=I('post.WIDtotal_fee');
            $subject=I('post.WIDsubject');
            
            if($paytype=='palpay'){
            	//paypal要转汇率 
            	$db=M('order')->where(array('orderno'=>$orderno))->find();
            	$rate=$db['rate']; 
				$totalfee=round($db['amount']/$rate, 2);
				
            } 
            $vo = new \Think\Pay\PayVo();
            $vo->setBody('')
                    ->setFee($totalfee) //支付金额
                    ->setOrderNo($orderno)
                    ->setTitle($subject)
                    ->setCallback($this->callbackurl)
                    ->setUrl($this->returnurl)
                    ->setParam(array('order_id' => $orderno));
            echo $pay->buildRequestForm($vo);
        } else {
            //在此之前goods1的业务订单已经生成，状态为等待支付
            exit();
            //$this->display();
        }
    }

    
    /**
     * 订单支付成功
     * @param type $money
     * @param type $param
     */
    public function pay($money, $param) {
        if (session("pay_verify") == true) {
            session("pay_verify", null);
        	exit('Succeed.');
            
            //处理goods1业务订单、改名good1业务订单状态
           // M("Goods1Order")->where(array('order_id' => $param['order_id']))->setInc('haspay', $money);
        } else {
            E("Access Denied");
        }
    }


    // 微信支付相关设置
    
    /**
     * 返回支付参数，调起支付界面
     *
     * @param string $code
     */
    public function call($code = '', $orderno = '') {
        $where = array ();
        $where ['orderno'] = $orderno;
        $where ['status'] = 0;
        $order = M ( 'order' )->where ( $where )->find ();
        if (! $order) {
            $this->error ( '对不起，订单错误！' );
        }
     
    
        // 先获取openid
        vendor ( 'WxPayPubHelper/WxPayPubHelper' );
        $jsApi = new \JsApi_pub ();
    
        $jscallurl = C ( 'WXPAY.JS_API_CALL_URL' ) . '%26orderno=' . $orderno;
        if ($code == '') {
            // 触发微信返回code码
            $url = $jsApi->createOauthUrlForCode ( $jscallurl );
            Header ( "Location: $url" );
            exit ();
        } else {
            // 获取code码，以获取openid
            $jsApi->setCode ( $code );
            $openid = $jsApi->getOpenId ();
        }
        
        // we(get_current_url());
        $out_trade_no = $order ['orderno'];
        $body = $order ['ordername'];
        $total_fee = $order ['amount'] * 100;
        // =========步骤2：使用统一支付接口，获取prepay_id============
        // 使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub ();
    
        $unifiedOrder->setParameter ( "openid", "$openid" ); // openid
        $unifiedOrder->setParameter ( "body", $body ); // 商品描述
        $unifiedOrder->setParameter ( "out_trade_no", "$out_trade_no" ); // 商户订单号
        $unifiedOrder->setParameter ( "total_fee", $total_fee ); // 总金额
        $unifiedOrder->setParameter ( "notify_url", C ( 'WXPAY.NOTIFY_URL' ) ); // 通知地址
        $unifiedOrder->setParameter ( "trade_type", "JSAPI" ); // 交易类型
    
        $prepay_id = $unifiedOrder->getPrepayId ();
    
        // we($unifiedOrder);
        // =========步骤3：使用jsapi调起支付============
        $jsApi->setPrepayId ( $prepay_id );
    
        $jsApiParameters = $jsApi->getParameters ();
    
        $this->assign ( 'jsApiParameters', $jsApiParameters );
    
        $this->assign ( 'openid', $openid );
        $this->assign ( 'amount', $order ['amount'] );
    
        // 返回地址
        $this->getRetUrl ( $order );
    
        $this->assign ( 'title', '支付中...' );
        $this->display ();
    }
    
    
    
    /**
     * 支付通知
     */
    public function payNotify() {
        vendor ( 'WxPayPubHelper/WxPayPubHelper' );
        $notify = new \Notify_pub ();
    
        // 存储微信的回调
        $xml = $GLOBALS ['HTTP_RAW_POST_DATA'];
        $notify->saveData ( $xml );
        // 使用通用通知接口
        if (! $notify->data ['appid']) {
            echo ('FAIL');
            exit ();
        }
    
        //取支付订单号
        $orderno = $notify->data ['out_trade_no'];
    
      
      
        // 验证签名，并回应微信。
        // 对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        // 微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        // 尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($notify->checkSign () == FALSE) {
            $notify->setReturnParameter ( "return_code", "FAIL" ); // 返回状态码
            $notify->setReturnParameter ( "return_msg", "签名失败" ); // 返回信息
        } else {
            $notify->setReturnParameter ( "return_code", "SUCCESS" ); // 设置返回码
        }
        $returnXml = $notify->returnXml ();
        echo $returnXml;
    
        // ==商户根据实际情况设置相应的处理流程，此处仅作举例=======
    
        // 以log文件形式记录回调信息
        logdebug ( "【接收到的notify通知】:\n" . $xml . "\n" );
    
        if ($notify->checkSign () == TRUE) {
            if ($notify->data ["return_code"] == "FAIL") {
                // 此处应该更新一下订单状态，商户自行增删操作
                logdebug ( "【通信出错】:\n" . $xml . "\n" );
            } elseif ($notify->data ["result_code"] == "FAIL") {
                // 此处应该更新一下订单状态，商户自行增删操作
                logdebug ( "【业务出错】:\n" . $xml . "\n" );
            } else {
                // 此处应该更新一下订单状态，商户自行增删操作
                logdebug ( "【支付成功】:\n" . $xml . "\n" );
                $this->payOk ( $notify->data, $xml );
            }
            	
            // 商户自行增加处理流程,
            // 例如：更新订单状态
            // 例如：数据库操作
            // 例如：推送支付完成信息
        }
    }
    
    /**
     * 付款成功处理订单
     *
     * @param unknown $orderinfo
     */
    private function payOk($data = null, $xml = null) {
        $orderno = $data ['out_trade_no']; 
        $pay = array ();
        $pay ['payinfo'] = $xml; // 服务器返回支付信息
        $pay ['status'] = 1; // 支付状态：成功
        $pay ['tradeno'] = $data ['transaction_id']; // 外部交易号
        $pay ['paid'] = $data ['total_fee']; // 实际支付金额
        $pay ['banktype'] = $data ['bank_type']; // 支付银行
        $pay ['openid'] = $data ['openid']; // 支付者openid
        $pay ['paytime'] = time_format (); // 支付时间
        $where = array ();
        $where ['orderno'] = $orderno;
        $db = M ( 'order' )->where ( $where )->data ( $pay )->save ();
        if ($db) { 
            // 通知相应的处理接口
            //member_order_notify ( $db );
        }
    }
    
}
