<?php

namespace Home\Controller;

use Think\Controller;

class PublicController extends Controller {

    /**
     * 支付结果返回
     */
    public function notify() {
        $apitype = I('get.apitype');
        GLog('home pay ','apitype:'.$apitype);
        $pay = new \Think\Pay($apitype, C('payment.' . $apitype));
        GLog('home pay ','pay data:'.json_encode($pay));
        if (IS_POST && !empty($_POST)) {
            GLog('home pay ','_POST data:'.json_encode($_POST));
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            GLog('home pay',' _GET data:'.json_encode($_GET));
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            GLog('home pay  ','data is empty');
            exit();
            // exit('Access Denied1');
        }
        GLog('home pay','notify data:'.json_encode($notify));
        //验证
        if ($pay->verifyNotify($notify)) {
            GLog('home pay','支付验证ok ');
            //获取订单信息
            $info = $pay->getInfo();
            GLog('home pay','订单信息: '.json_encode($info));
        	//$info={"status":true,"money":"0.01","out_trade_no":"100002","trade_no":"2014060470588932"}
            if ($info['status']) {
                $payinfo = M("Pay")->field(true)->where(array('out_trade_no' => $info['out_trade_no']))->find();
                if ($payinfo['status'] == 0 && $payinfo['callback']) {
                    session("pay_verify", true);
                    GLog('home pay','支付中...');
                       M("Pay")->where(array('out_trade_no' => $info['out_trade_no']))->setField(array('update_time' => time(), 'status' => 1));
                    	
                       //自加20140516
                       $exec=A('Order');
                    	$token=md5(date('His'));
                    	//paypal
//                     	$out_trade_no = $notify['invoice'];
//                     	$trade_no=  $notify['txn_id'];
//                     	$trade_status=$notify['payment_status'];
//                     	$total_fee=$notify['mc_gross'];
                    	
                    	//alipay
                    	$out_trade_no = $info['out_trade_no'];
                    	$trade_no=  $info['trade_no'];
                    	$trade_status=$info['status'];
                    	$total_fee=$info['money'];
                    	$exec->payOrder($token,$out_trade_no,$trade_no,2,$total_fee);
                    GLog('home pay','修改支付状态...');
                }
                GLog('home pay','支付状态ok');
                if (I('get.method') == "return") {
                    redirect($payinfo['url']);
                } else {
                    $pay->notifySuccess();
                }
            } else {
                GLog('home pay','订单信息无状态');
                $this->error("Failed.");
            }
        } else {
            GLog('home pay','支付验证失败');
        	$this->redirect('/');
            exit();
        	//E("Access Denied2");
        }
    }

}
