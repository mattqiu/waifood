<?php
namespace Common\Model;
use Think\Model;

class OrderModel extends Model{
    const WECAHT = 1;//微信订单
    const PAYPAL = 2;//Paypal支付

    const PAID = 1;//已支付
    const UNPAID = 0;//未支付

    const  DRAFT = 0;//
    const   CONFIRMED  = 1;//
    const    DELIVERING  = 2;//
    const    COMPLETED = 3;//
    const    CANCELLED = 4;//


    /**根据订单号获取订单详情
     * @param $orderNo
     * @return bool|mixed
     */
    public static function getOrderDetailByOrderno($orderNo){
        if($orderNo){
            $ocn['orderno'] = $orderNo;
           return M ( 'order_detail' )->where($ocn)->select();
        }
        return false;
    }

    public static function modifyOrder($orderno,$data){
        if($orderno && !empty($data)){
            $con['orderno'] = $orderno;
            return M ( 'order' )->where($con)->save($data );
        }
        return false;
    }

    /**
     * 根据条件更改订单
     * @param $con
     * @param $data
     * @return bool
     */
    public static function modifyOrderByCon($con,$data){
        if($con && !empty($data)){
            return M ( 'order' )->where($con)->save($data );
        }
        return false;
    }

    /**下单
     * @param $data
     * @param $userId
     * @return bool|string
     */
    public static function createOrder($data,$userId){
        if(empty($data['order'])){
            apiReturn(CodeModel::ERROR,'Sorry, Order content cannot be empty!');
        }
       // self::catStock('20161110121951');return;
        if(!is_date(substr($data['delivertime'],0,10))){
            apiReturn('Sorry, please select the delivery date.');
        }
        $order = self::seperateOrder($data['order']);//获取商品
        $amount = self::checkOrder($order);//检查库存
        $addressid = $data['UseAddressID'];
        $address = AddressModel::getUserAddressById($addressid,$userId);
        if($address){
            $data ['username']=$address['username'];
            $data ['telephone']=$address['telephone'];
            $data ['sex']=$address['sex'];
            $data ['address']=$address['address'];
            $data ['cityname']=$address['cityname'];
            $data ['email']=$address['email'];
        }else{
            apiReturn('Sorry, address error.');
        }

        if(is_wechat()){
            $data ['orderfrom'] = self::WECAHT;//微信订单
        }else{
            $data ['orderfrom'] =  getMobile();//获取手机
        }
        $rate=lbl('rate');
        if(is_decimal($rate)){
            $data ['rate'] = $rate;
        }
        if($data['paymethod']==4){ //货到付款
            $data['status']=0;
        }else{
            $data['status']=0;
        }
        $data ['orderno'] = $orderno = get_order_no();
        $data ['num'] = $order['amountnum'];
        $data ['amount'] =$amount+getShipfee($amount);//实际支付总金额
        $data ['amountall'] =$amount+getShipfee($amount);//订单总金额
        $data ['shipfee'] = getShipfee($amount);
        $data ['userid'] = $userId;
        $data ['usertype'] = get_cate(get_userid (),'member','usertype');
        $data ['addip'] = getRealIp();
        unset($data['order']);
        unset($data['UseAddressID']);
        $orderid = M ( 'order' )->add ( $data );
        if ($orderid != false) {
             self::createOrderDetail($order,$orderno,$userId,$data['status']);//添加订单详情
             self::catStock($orderno);
             self::sendEmail($orderno,$userId);
            return $orderno;
        }else {
            return false;
        }
    }

    /**
     * 下单成功发送对应邮件通知
     * @param $orderno
     * @param $userId
     */
    private static function sendEmail($orderno,$userId){
        $mailhtml=self::mailhtml($orderno);
        $where['orderno']=$orderno;
        $data=M('order')->where($where)->find();
        //客户邮件：send_mail();
        $to=M('member')->where('id='.$userId)->getField('email');
        $subject='[waifood]order submit successfully';
        $body=lbl('tpl_createorder');
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
            $body.=$mailhtml;
            if(!send_mail($to,$subject,$body)){
                GLog('create order - send email to user '.$to,'发送邮件失败');
            }
        }
        //管理员邮件：
        $to=C('config.WEB_SITE_COPYRIGHT');
        $subject='[waifood]new order from '.get_username(get_userid());
        $body=lbl('tpl_receiveorder');
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
            $mailhtml=self::mailhtml($orderno,1);
            $body.=$mailhtml;
            if(!send_mail($to,$subject,$body)){
                GLog('create order - send email to admin '.$to,'发送邮件失败');
            }
        }
    }

    private static  function mailhtml($orderno,$admin='') {
        $html = '';
        //$orderno = '201406031622041955';
        $where = array ();
        $where ['orderno'] = $orderno;
        $data = M ( 'order' )->where ( $where )->find ();
        if ($data) {
            $html.='<br /><br />Order details:<br />';
            $detail = M ( 'order_detail' )->where ( $where )->select ();

            $html.= "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" >\n";
            $html.= "  <tr>\n";
            $html.= "    <th width=\"10%\"> No. </th>\n";
            $html.= "    <th > Product Name</th>\n";
            $html.= "    <th width=\"10%\"> Unit</th>\n";
            $html.= "    <th width=\"10%\"> Price</th>\n";
            $html.= "    <th width=\"10%\"> Quantity </th>\n";
            $html.= "    <th width=\"10%\"> Subtotal</th> \n";
            $html.= "  </tr> \n";
            foreach($detail as $k=>$v){
                $html.= "    <tr align=\"center\">\n";
                $html.= "      <td>".$v['productid']."</td>\n";
                $html.= "      <td>".$v['productname']."</td>\n";
                $html.= "      <td>".$v['unit']."&nbsp;</td>\n";
                $html.= "      <td>".$v['price']."</td>\n";
                $html.= "      <td>".$v['num']."</td>\n";
                $html.= "      <td>".to_price($v['price']*$v['num'])."</td> \n";
                $html.= "    </tr> \n";
            }
            $html.= "  <tr>\n";
            $html.= "    <td colspan=\"6\" align=\"right\"> Delivery Fee: ".($data['shipfee']==0?'FREE':$data['shipfee'])."<br />Coupon: ".($data['couponamount'])."<br />".($data['discount']==0?'':' Discount: '.$data['discount'].'<br />')."Total:".$data['amount']."</td> \n";
            $html.= "  </tr> \n";
            $html.= "</table>\n";

            if($admin){
                $html.='<br />Receiving information:<br />Name：'.$data['username'].'<br />Mobile：'.$data['telephone'].'<br />Email：'.$data['email'].'<br /> Address：'.$data['proname'].''.$data['cityname'].''.$data['disname'].''.$data['address'].'';

                $html.='<hr /><br /><br />Delivery date:'.$data['delivertime'].'<br />';
                $html.='Remarks:'.$data['info'].'<br />';
            }
        }
        return($html);
    }

    /**
     * 添加订单详情，并减少库存
     * @param $order
     * @param $orderno
     * @param int $status
     */
    public static function createOrderDetail($order, $orderno,$userid,$status=0){
        $isShop = 0;
        $isService = false;
        foreach ( $order as $key => $value ) {
            $productid =$value['id'];
            if($productid>0){
                $data = array ();
                $good = M ( 'content' )->find ( $productid );
                if (false != $good) {
                    $data ['orderno'] = $orderno;
                    $data ['productid'] = $productid;
                    $data ['productname'] = $good ['title'];
                    $data ['indexpic'] = $good ['indexpic'];
                    $data ['sortpath'] = $good ['sortpath'];
                    $data ['supplyid'] = $good ['supplyid'];
                    $data ['supplyname'] = $good ['supplyname'];
                    $data ['unit'] = $good ['unit'];
                    $data ['namecn'] = $good ['namecn'];
                    $data ['price'] = $good['price'];
                    $data ['num'] = $value['num'];
                    $data ['status'] = $status;
                    $data ['userid'] = get_userid();
                    M ( 'order_detail' )->add ( $data );
                    if (strpos($good['sortpath'], ',2,')) {
                        $isShop = true;
                    }
                    if (strpos($good['sortpath'], ',3,')) {
                        $isService = true;
                    }
                }
            }
        }
        $ordertype = 0;
        if ($isShop && $isService) {
            $ordertype = 2;
        } else {
            if ($isService) {
                $ordertype = 1;
            }
        }
        $where['orderno'] = $orderno;
        $data1['ordertype']=$ordertype;
        M('order')->where($where)->data($data1)->save();
    }

    /**减库存，加销量
     * @param $id
     * @param $num
     */
    public static function catStock($orderno){
       $info =  self::getOrderDetailByOrderno($orderno);
        foreach ( $info as $key => $value ) {
            if($value['productid'] && $value['num']){
                $con['id'] = $value['productid'];
                M('content')->where($con)->setDec('stock',$value['num']);
                M('content')->where($con)->setInc('sold',$value['num']);
                $con2['stock'] = 0;
                $data['status'] = 0;
                M('content')->where($con2)->save($data);
            }
        }
    }


    /**
     * 检查库存，返回商品总金额
     * @param $data
     * @return float|int
     */
    public static function checkOrder($data){
        $amount = 0;
        foreach ( $data as $key => $val) {
            if($val['id']){
                $product=M('content')->find($val['id']);
                if($product['status'] == ContentModel::NORMAL){ //只获取上架的商品
                    if($product['stock']<$val['num']){
                        apiReturn(CodeModel::ERROR,'The stock is insufficient, we will try to have it soon.[#'.$product['title'].']');
                        break;
                    }
                    $amount+= floatval($product['price'] * $val['num']);
                }else{ //商品下架了

                    apiReturn(CodeModel::ERROR,$product['id'].'The stock is insufficient, we will try to have it soon.[#'.$product['title'].']');
                    break;
                }
            }

        }
        return $amount;
    }


    /**
     * 分拆order,
     * 如 1,2|(food_id,amount) 返回
     * array(array(food_id=>1,amount=>2),array(food_id=>3,weight_id=>4,amount=>4));
     */
    public static function seperateOrder($order){
        $orderTmp = explode("|", $order);
        $data = array();
        foreach($orderTmp as $key=>$val){
            if(stripos($val, ',')){ // 去除id 和数量
                $ids = explode(",", $val);
                $data[$key]['id'] = $ids[0];
                $data[$key]['num'] = $ids[1];
                $data[$key]['price'] = $ids[2];
                if(!isset($data['amountnum'])){
                    $data['amountnum'] = intval($ids[1]);
                }
                if(!isset($data['totalmoney'])){
                    $data['totalmoney'] = floatval($ids[1] * $ids[2]);
                }
                $data['amountnum'] += intval($ids[1]);
                $data['totalmoney'] += floatval($ids[1] * $ids[2]);
            }
        }
        return $data;
    }
}