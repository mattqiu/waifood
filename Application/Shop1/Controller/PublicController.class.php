<?php

namespace Shop\Controller;

use Think\Controller;

class PublicController extends Controller {

    /**
     * 支付结果返回
     */
    public function notify() {
        $apitype = I('get.apitype');
        GLog('pay ','apitype:'.$apitype);
        $pay = new \Think\Pay($apitype, C('payment.' . $apitype));
        GLog('pay ','pay data:'.json_encode($pay));
//         \Think\Log::write('1:'.arr2str($_POST));
//         \Think\Log::write('2:'.json_encode($_POST));
        if (IS_POST && !empty($_POST)) {
            GLog('pay ','_POST data:'.json_encode($_POST));
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            GLog('pay',' _GET data:'.json_encode($_GET));
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            GLog('pay  ','data is empty');
            exit();
            // exit('Access Denied1');
        }
        GLog('pay','notify data:'.json_encode($notify));

        //验证
        if ($pay->verifyNotify($notify)) {
            GLog('pay','支付验证ok ');
//         	\Think\Log::write('4:'.json_encode($notify));
            //获取订单信息
            $info = $pay->getInfo();
            GLog('pay','订单信息: '.json_encode($info));
            if ($info['status']) {
                $payinfo = M("Pay")->field(true)->where(array('out_trade_no' => $info['out_trade_no']))->find();
                if ($payinfo['status'] == 0 && $payinfo['callback']) {
                    GLog('pay','支付中...');
                    session("pay_verify", true);
            
                   M("Pay")->where(array('out_trade_no' => $info['out_trade_no']))->setField(array('update_time' => time(), 'status' => 1));
                   //自加20140516
                   $exec=A('Order');
                    $token=md5(date('His'));
                    $out_trade_no = $notify['invoice'];
                    $trade_no=  $notify['txn_id'];
                    $trade_status=$notify['payment_status'];
                    $total_fee=$notify['mc_gross'];
                    $exec->payOrder($token,$out_trade_no,$trade_no,2,$total_fee);
                    GLog('pay','修改支付状态...');
                }
                GLog('pay','支付状态ok');
                if (I('get.method') == "return") {
                    redirect($payinfo['url']);
                } else {

                    $pay->notifySuccess();
                }
            } else {
                GLog('pay','订单信息无状态');
                $this->error("Failed.");
            }
        } else {
            GLog('pay','支付验证失败');
        	$this->redirect('/');
            exit();
        	//E("Access Denied2");
        }
    }

}
