<?php
namespace Common\Model;
use Home\Model\WeixinModel;
use Think\Log;
use Think\Model;

class OrderModel extends Model{
    const WECAHT = 1;//微信订单
    const NORMAL = 0;//电脑订单

    const PAYPAL = 2;//Paypal支付
    const PAY_WEICHAR = 5;//微信支付

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

    public static function getOrderByOrderno($orderno){
        if($orderno){
                $ocn['orderno'] = $orderno;
                return M ( 'order' )->where($ocn)->find();
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
        if(!is_date(substr($data['delivertime'],0,10))){
            apiReturn(CodeModel::ERROR,'Sorry, please select the delivery date.');
        }
        $order = self::seperateOrder($data['order']);//获取商品
        if(empty($order)){
            apiReturn(CodeModel::ERROR,'Sorry, Order content cannot be empty!');
        }
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
            apiReturn(CodeModel::ERROR,'Sorry, address error.');
        }
        if(isMobile()){
            if(FROM_WEIXIN){
                $data ['orderfrom'] = self::WECAHT;//微信订单
            }else{
                $data ['orderfrom'] =  getMobile();//获取手机
            }
        }else{
            $data ['orderfrom'] = self::NORMAL;
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
        $discount =   ActiviModel::getActiviDiscount($amount);
        $data ['orderno'] = $orderno = get_order_no();
        $data ['num'] = $order['amountnum'];
        $data ['amount'] =($amount-$discount)+getShipfee($amount);//实际支付总金额=商品总价-折扣+配送费
        $data ['amountall'] =$amount+getShipfee($amount);//订单总金额
        $data ['shipfee'] = getShipfee($amount);
        $data ['discount'] = $discount;
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
        $body=self::mailhtml($orderno);
        $where['orderno']=$orderno;
        $data=M('order')->where($where)->find();
        //客户邮件：send_mail();
        $to=M('member')->where('id='.$userId)->getField('email');
        $subject='[waifood]order submit successfully';
       // $body=lbl('tpl_createorder');
        if(!isN($body)){
            if(!send_mail($to,$subject,$body)){
                GLog('create order - send email to user '.$to,'发送邮件失败');
            }
        }
        //管理员邮件：
        $to= ConfigModel::getConfig('WEB_SITE_COPYRIGHT');//获取管理员邮箱//C('config.WEB_SITE_COPYRIGHT');
        if($data ['orderfrom'] == self::NORMAL){$client = 'PC';}else{$client = '微信';}
        $subject="[新单/$client]".$data['amount'];
        $body=self::mailhtml($orderno,1);
        if(!send_mail($to,$subject,$body)){
            GLog('create order - send email to admin '.$to,'发送邮件失败');
        }
    }

    /**下单成功发送邮件（客户/管理员）
     * @param $orderno
     * @param string $admin
     * @return string
     */
    private static  function mailhtml($orderno,$admin='') {
        header('Content-Type:text/html;charset=utf-8');
        $userinfo = '';
        $html = '';
        $adminhtml = '';
        $city = '其他';
        $fapiao = 'No';
        $productAmount = 0;
        $where = array ();
        $where ['orderno'] = $orderno;
        $data = M ( 'order' )->where ( $where )->find ();
        if ($data) {
            if($data['invoice']==1){$fapiao = "Yes"; }
            if(strtolower(trim($data['cityname']))=='chengdu'){$city = "成都"; }
            elseif(strtolower(trim($data['cityname']))=='chongqing'){$city = "重庆"; }
            elseif(strtolower(trim($data['cityname']))=='xi\'an'){$city = "西安"; }
            elseif(strtolower(trim($data['cityname']))=='kunming'){$city = "昆明"; }
            $detail = M ( 'order_detail' )->where ( $where )->select ();
            $html.= "<table width='100%' style=\"padding: 0;margin: 0;border: #c0bfbf;font-size: 12px;line-height: 25px;border-collapse:collapse; font-family: 'verdana', 'Arial'\" cellpadding='0'  cellspacing='0' border='1'>\n";
            $html.= "  <tr style='background:#f7f7f7;font-size: 14px; '>\n";
            $html.= "    <th colspan='6' align='left' width='10%' style='padding-left: 16px'> Order details </th>\n";
            $html.= "  </tr> \n";
            $html.= "  <tr style='font-size: 14px; '>\n";
            $html.= "    <th width='10%'> No. </th>\n";
            $html.= "    <th > Product Name</th>\n";
            $html.= "    <th width='10%'> Unit</th>\n";
            $html.= "    <th width='10%'> Price</th>\n";
            $html.= "    <th width='10%'> Qty </th>\n";
            $html.= "    <th width='10%'> Subtotal</th> \n";
            $html.= "  </tr> \n";
            foreach($detail as $k=>$v){
                $productAmount += to_price($v['price']*$v['num']);
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
            $html.= "<td colspan=\"6\" align=\"right\" style='padding-right: 50px;'>Product Amount <span style=\"font-family: '宋体'\">商品总额:</span>&yen;$productAmount <br/>
Delivery Fee <span style=\"font-family: '宋体'\">运费:</span> ".($data['shipfee']==0?'FREE':'&yen;'.$data['shipfee'])."<br />Payable  <span style=\"font-family: '宋体'\">商品总额:</span>&yen;".$data['amount']."</td> \n";
            $html.= "  </tr> \n";
            $html.= "</table>\n";

            if($admin){ //发送给管理员
                $adminhtml="<div style=\"border-bottom: 1px solid #c0bfbf;line-height: 25px; font-size: 14px;word-break: break-all; font-weight: bold;font-family:Microsoft YaHei, '微软雅黑', '宋体'\">{$data['id']}号，{$city}，送货 <span style='font-weight: normal'>{$data['delivertime']}</span>，{$data['username']}，{$data['address']}</div>";
                if($fapiao=='Yes'){$fp = "是"; }else{$fp = "否";}
                $adminhtml.="<div style=\"line-height: 25px; font-size: 12px;word-break: break-all;font-family:Microsoft YaHei, '微软雅黑', '宋体'\">
                            <br />发票：<span style='font-weight: bold'>{$fp}</span>&nbsp;；&nbsp;&nbsp; 客户电话：{$data['telephone']}<br />
                             客户留言：{$data['info']}<br />
                             订单号：{$orderno}<br />
                             下单时间：{$data['addtime']}<br />
                             </div><br/><br/>";
                $html = $adminhtml.$html;
            }else{ //发送给客户
                $user = UserModel::getUserById($data['userid']);
                if($user){
                    $userinfo.= "<table width='100%' style=\"padding: 0;margin: 0;border: #c0bfbf;font-size: 12px;line-height: 25px;border-collapse:collapse;word-break: break-all; font-family: 'verdana', 'Arial'\" cellpadding='0'  cellspacing='0'  border='1'>\n";
                    $userinfo.= "<tr style='background:#f7f7f7;font-size: 14px '>\n";
                    $userinfo.= "<th colspan='2' align='left' width='10%' style='padding-left: 16px'> Order ID: $orderno</th>\n";
                    $userinfo.= "</tr> \n";
                    $userinfo.= "<tr align='left' >\n";
                    $userinfo.= "<td style='padding-left: 5px;padding-right: 5px'>Consignee <span style=\"font-family: '宋体'\">收件人:</span>{$data['username']}(".($data['telephone']?$data['telephone']:$user['telephone']).")<br/>
                                Delivery Time <span style=\"font-family: '宋体'\">收货时间:</span>{$data['delivertime']}<br/>
                                Delivery Address <span style=\"font-family: '宋体'\">地址:</span>{$data['address']}
                            </td>\n";

                    $userinfo.= "<td style='padding-left: 5px;padding-right: 5px;vertical-align: initial; '>
                                Customer <span style=\"font-family: '宋体'\">订购人:</span>{$user['username']}<br/>
                                Fapiao <span style=\"font-family: '宋体'\">发票:</span>$fapiao<br/>
                               Customer Message:{$data['info']}<br/>
                        </td>\n";
                    $userinfo.= "</tr> \n";
                    $userinfo.= "</table><br/><br/><br/>\n";
                    $html = $userinfo.$html;
                }
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

    /**
     *  完成在线订单支付后的处理
     *
     * @param int $orderno 订单id
     * @param float $payPrice  在线支付的金额 false 表示不校验
     *
     */
    public static function finishOnlineOrderPay($orderno,$payPrice=false,$paymethod=self::PAY_WEICHAR,$wxOrderId=null){
        $con['orderno'] = $orderno;
        $order = OrderModel::getOrderByOrderno($orderno);
        if(empty($order)){
            GLog("orderpay","接收到支付通知,未找到对应订单信息,order_id: $orderno",Log::ERR);
            return false;
        }
        if(self::PAID == $order['pay']){
            GLog("orderpay","订单已支付完成",Log::INFO);
            return true;
        }
        if($payPrice){
            if(!floateq($payPrice,$order['amount'])){
                GLog("orderpay","第三方支付价格 与订单价格不一致 $payPrice == ".$order['amount']." rs: false",Log::ERR);
                return false;
            }
        }
        //修改订单支付状态
        $data['paymethod'] =$paymethod;
        $data['paytime'] = date('Y-m-d H:i:s');
        $data['pay'] = self::PAID;
        if($wxOrderId){
            $data['tradeno'] = $wxOrderId;
        }
        $status = OrderModel::modifyOrderByCon($con,$data);
        if(false === $status){
            GLog("orderpay","更新订单支付状态失败",Log::ERR);
            return false;
        }
        return true;
    }

}