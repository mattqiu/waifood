<?php

namespace Home\Controller;

use Think\Controller;

class PublicController extends Controller {

    /**
     * 支付结果返回
     */
    public function notify() {
        $apitype = I('get.apitype'); 
        $pay = new \Think\Pay($apitype, C('payment.' . $apitype));

//         \Think\Log::write('1:'.arr2str($_POST));
//         \Think\Log::write('2:'.json_encode($_POST));
        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            exit();
            // exit('Access Denied1');
        }
        
        //\Think\Log::write('3:'.json_encode($notify));
        //验证
        if ($pay->verifyNotify($notify)) {
        	\Think\Log::write('4:'.json_encode($notify));
            //获取订单信息
            $info = $pay->getInfo();
        	\Think\Log::write('5:'.json_encode($info));
        	//$info={"status":true,"money":"0.01","out_trade_no":"100002","trade_no":"2014060470588932"}
            if ($info['status']) {
                $payinfo = M("Pay")->field(true)->where(array('out_trade_no' => $info['out_trade_no']))->find();
                if ($payinfo['status'] == 0 && $payinfo['callback']) {
                    session("pay_verify", true);
            
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
                   
                }
                if (I('get.method') == "return") {
                    redirect($payinfo['url']);
                } else {
                    $pay->notifySuccess();
                }
            } else {
                $this->error("Failed.");
            }
        } else {
        	$this->redirect('/');
            exit();
        	//E("Access Denied2");
        }
    }

}
