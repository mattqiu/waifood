<?php
// 订单处理类
namespace Shop\Controller;

use Common\Model\AddressModel;
use Common\Model\CodeModel;
use Common\Model\OrderModel;
use Common\Model\UserModel;
use Common\Model\DateModel;

class OrderController extends AuthController
{
    /**
     * 取消订单
     * 
     * @param string $orderno            
     */
    public function cancelOrder($orderno = '')
    {
        $where['orderno'] = $orderno;
        $where['status'] = array(
            'in',
            array(
                0,
                1
            )
        ); // 状态为：提交和已确认
                                                    // $where ['pay'] = 0;
        $where['userid'] = get_userid();
        $db = M('order')->where($where)->find();
        if (! $db == false) {
            M('order')->where($where)->setField('status', 4);
            set_order_onoff($orderno,1);
            $this->success('订单取消成功！');
        } else {
            $this->error('对不起，该订单无法取消！');
        }
    }

    /**
     * 订单提交
     * @param number $shop_id
     */
    public function submitOrder(){
        $userid = get_userid();
        if(!regex($userid,'number')){
            apiReturn(CodeModel::ERROR,'Sorry, please login first!');
        }
        $data = I('post.');
        $orderno = OrderModel::createOrder($data,$userid);
        if($orderno){
            $where=array();
            $where['orderno']=$orderno;
            $order=M('order')->where($where)->find();
            if($order['paymethod'] == OrderModel::PAYPAL){
                $url = "/home/shop/pay.html?orderno={$order['orderno']}";
                apiReturn(CodeModel::CORRECT,'Place an order successfully',$url);
            }else{
                apiReturn(CodeModel::CORRECT,'Place an order successfully','/member/order.html');
            }
        }else{
            $this->assign('title','Failed.');
            $this->display('Shop/error');
        }
    }


    /**
     * 订单支付
     *
     * @param string $orderno
     * @param string $type
     */
    public function pay() {
        $orderno = I('orderno');
        $where = array ();
        $where ['orderno'] = $orderno;
        //$where ['paymethod'] = array ('neq',4 );
        $where ['status'] = array('neq',OrderModel::CANCELLED);
        $db = M ( 'order' )->where ( $where )->find ();
        if ($db) {
            $payapiurl = U('Shop/Pay/index');
            $amount = $db ['amount'];
            $html = '';
            $html .= '<form name="alipay_submit" action="' . $payapiurl . '" method="post" >';
            $html .= '  <input type="hidden" name="paytype" value="palpay"/>';
            $html .= '  <input type="hidden" name="WIDout_trade_no" value="' . $orderno . '"/>';
            $html .= '  <input type="hidden" name="WIDtotal_fee" value="' . $amount . '" />';
            $html .= '  <input type="hidden" name="WIDsubject"  value="[paypal]online pay system."  />';
            $html .= '  <button type="submit" style="text-align:center;display:none;">submit</button>';
            $html .= '</form>';
            $html .= '<script>document.forms["alipay_submit"].submit();</script>';
            echo($html);
        } else {
            $this->error ( 'Order ' . $orderno . ' does not exist or without paying!' );
        }
    }
}
?>