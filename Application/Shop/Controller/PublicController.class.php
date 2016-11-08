<?php

namespace Shop\Controller;

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
        GLog('pay',json_encode($notify));

        //验证
        if ($pay->verifyNotify($notify)) {
//         	\Think\Log::write('4:'.json_encode($notify));
            //获取订单信息
            $info = $pay->getInfo();
            GLog('pay',json_encode($info));
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
                if (I('get.method') == "return") {
                    redirect($payinfo['url']);
                } else {
                    GLog('pay','支付成功...');
                    $pay->notifySuccess();
                }
            } else {
                GLog('pay','支付失败');
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
