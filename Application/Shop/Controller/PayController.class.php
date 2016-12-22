<?php

namespace Shop\Controller;
 
class PayController extends BaseController {

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
                    ->setCallback("Shop/Pay/pay")
                    ->setUrl(U("/"))
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
        GLog('paypal ','.,,,,,,,,,,,,,,,,,,,,,,');
        if (session("pay_verify") == true) {
            session("pay_verify", null);
        	exit('Succeed.');
            
            //处理goods1业务订单、改名good1业务订单状态
           // M("Goods1Order")->where(array('order_id' => $param['order_id']))->setInc('haspay', $money);
        } else {
            E("Access Denied");
        }
    }

}
