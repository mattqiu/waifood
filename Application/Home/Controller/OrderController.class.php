<?php
// 订单处理类
namespace Home\Controller; 
use Common\Model\AddressModel;
use Common\Model\CodeModel;
use Common\Model\ConfigModel;
use Common\Model\OrderModel;

class OrderController extends BaseController {
    /**
     * 取消订单
     *
     * @param string $orderno
     */
    public function cancelOrder()
    {

        $orderno = I('orderno');
        $where['orderno'] = $orderno;
        $where['status'] = array( 'in', array( 0, 1)); // 状态为：提交和已确认
        $where['userid'] = get_userid();
        $order = M('order')->where($where)->find();
        if ($order) {
            OrderModel::modifyStockAndSoldForOrder($orderno,OrderModel::RET_STOCK);//退库存减销量
            M('order')->where($where)->setField('status', 4);
//            set_order_onoff($orderno,1);
            apiReturn(CodeModel::CORRECT,'Successful.');
        }else {
            apiReturn(CodeModel::ERROR,'Failed, unexpected problem.');
        }
    }

    //支付详细
	public function payout($orderno='',$price=0){
		$where = array ();
		$where ['orderno'] = $orderno; 
		$db = M ( 'order' )->where ( $where )->find ();
		$this->assign ( 'db', $db );
		 
		$this->assign ( 'title', '订单详情' );
		$this->display ('Pay/orderView');
		
	}
	
	public function wxpay($orderno=''){
		$orderno=str_replace('/home/order/wxpay/orderno_','',__SELF__);
		$orderno=str_replace('.html','',$orderno);
		//we($orderno);
		$where = array ();
		$where ['orderno'] = $orderno;
		$db = M ( 'order' )->where ( $where )->find ();
		$this->assign ( 'db', $db );

		$this->assign ( 'title', '订单详情' );
		$this->display ('Pay/orderView');
	}
	
	
	/**
	 * 积分兑换：1.登录 2.库存 3.积分数
	 * @param number $item_ids
	 * @param number $num
	 * @param string $ext
	 */
	public function addPointOrder($item_id = 0, $num = 1, $ext = null) {
		$userid=get_userid();
		if($userid=="0"){
			$this->error('请您登录后再申请积分兑换');
		}
		$where=array();
		$where['id']=$item_id;
		$where['status']=1;
		$where['sortpath']=array('like','%,'.C('DEFAULT_CREDIT_CHANNEL').',%');
		$data=M('product')->field('id,stock,price')->where($where)->find();
	
		if($data){
			if($data['stock']<$num){
				$this->error('所选商品库存不足');
			}
            $member = UserModel::getUserById($userid);
			if(isN($member['address'])){
				$this->error('您的详细地址不全，请先补充（会员中心 > <a href="'.U('Member/info').'" style="color:red">修改资料</a>）');
			}else{
	
			}
			$credit=get_usercredit($userid);
			$need=$data['price']*$num;
			if($credit<$need){
				$this->error('您的积分不足（需要积分：'.$need.'，您只有：'.$credit.'）');
			}
				
			//1. 写入数据库
			$orderno = get_order_no();
			//2. 参数4：积分兑换
			$ret = $this->insertCredit ( $userid, 4, $need, $orderno );
			if($ret){
				$product = M('product')->field('title,indexpic')->where('id='.$item_id)->find();
				$data=array();
				$data['productid']=$item_id;
				$data['productname']=$product['title'];
				$data['indexpic']=$product['indexpic'];
				$data['userid']=$userid;
				$data['orderno']=$orderno;
				$data['num']=$num;
				$data['username']=$member['userreal'];
				$data['telephone']=$member['telephone'];
				$data['address']=$member['address'];
				$data['amount']=$need;
				$data['status']=0;
				$data['pay']=1;
				$data['paytime']=time_format();
				$data['addip']=get_client_ip();
				//$this->error($data);
				$db=M('orderpoint')->data($data)->add();
				if($db){
					$this->success($data['id']);
				}
				else{
					$this->error('未知错误');
				}
			}else{
				$this->error('积分扣除失败');
			}
				
				
		}else{
	
			$this->error('所选商品不存在');
		}
	}
	
	public function submitSeatOrder($shop_id=0){
		if(get_userid()==0){
			$this->redirect('Login/index');
		}
		$orderno=$this->createSeatOrder($shop_id);
		if($orderno){
			$where=array();
			$where['orderno']=$orderno;
			$db=M('order_seat')->where($where)->find();
			$db['type']='seat';
			$this->assign('db',$db);
			$this->display('Shop/success');
		}else{ 
			$this->display('Shop/error');
		}
	}

	/**
	 * 确认订单
	 * @param string $orderno
	 */
	public function confirmOrder($orderno=''){
	
		$where ['orderno'] = $orderno;
		$where ['status'] = 2;
		$where['userid']=get_userid();
		$db = M ( 'order' )->where ( $where )->find ();
		if (! $db == false) {
			M ( 'order' )->where ( $where )->setField('status',3);
			
			//加积分
			$creditrate = (C ( 'config.CREDIT_RATE' ) ? C ( 'config.CREDIT_RATE' ) : 0);
			$creditrate = (is_numeric ( $creditrate ) ? $creditrate : 0);
				
			if ($creditrate != 0) {
				$where = array ();
				$where ['orderno'] = $orderno;
				$db = M ( 'order' )->field ( 'userid,amount' )->where ( $where )->find ();
				$uid = $db ['userid'];
				$amount = $db ['amount'];
				if (is_numeric ( $uid ) && $uid != 0) { 
					// 参数1：订单消费
					$ret =$this->insertCredit ( $uid, 1, $amount*$creditrate, $orderno );
					
					
					// 推荐者加积分:$fatherid,$creditrate1
					$creditLast=0;
					if(C('BACK_TYPE')==1){
						//按总价返
						$creditrate1 = (C ( 'config.CREDIT_FATHER_RATE' ) ? C ( 'config.CREDIT_FATHER_RATE' ) : 0);
						$creditrate1 = (is_numeric ( $creditrate1 ) ? $creditrate1 : 0);
						$creditLast = $amount*$creditrate1;
					}else{
						//按商品返
						$creditrate1 = $this->getBackMoney($orderno);
						$creditLast = $creditrate1;
					}
					if ($creditLast != 0) {
						$fatherid = M('member')->where('id='.$uid)->getField('fatherid');
						if($fatherid!=0){
							//参数4：邀请返利
							if(C('BACK_MONEY_TYPE')==1){
								$this->insertCredit ( $fatherid, 4, $creditLast, $orderno );
							}else{
								$this->insertBalance ( $fatherid, 4, $creditLast, $orderno );
							}
						}
					}
					if ($ret) {
						M ( 'order_detail' )->where ( 'orderno=' . $orderno )->setField('status',3); 
					}  
				}
			}  
			
			
			$this->success('订单确认成功！');
		}else{
			$this->error('对不起，该订单无法确认！');
		}
	}

    /**
     * 订单提交
     * @param number $shop_id
     */
    public function submitOrder(){
        $userid = get_userid();
        if(!regex($userid,'number')){
            apiReturn(CodeModel::ERROR,'Please sign in.');
        }
        $data = I('post.');
        $orderno = OrderModel::createOrder($data,$userid);
        if($orderno){
            $where=array();
            $where['orderno']=$orderno;
            $order=M('order')->where($where)->find();
            if($order['paymethod'] == OrderModel::PAYPAL){//paypal 支付
                $url = "/m_pay?orderno={$order['orderno']}";
            }elseif($order['paymethod'] == OrderModel::PAY_WEICHAR){//微信支付
                $url = "/index/payWeixin?orderno={$order['orderno']}";
            }else{
                $url = '/member/order.html';
            }
            apiReturn(CodeModel::CORRECT,'Successful.',$url);
        }else{
            $this->assign('title','Failed.');
            $this->display('Shop/error');
        }
    }

    public function modifyOrderPaymethod(){
        $orderno = I('post.orderno');
        $paymethod = I('post.paymethod');
        if($orderno && $paymethod){
            $data['paymethod'] = $paymethod;
            if(false !==OrderModel::modifyOrder($orderno,$data)){
                apiReturn(CodeModel::CORRECT);
            }
        }else{
            apiReturn(CodeModel::ERROR,M()->_sql());
        }
    }


	public function submitOrder1(){
	    if(get_userid()==0){
	        $this->redirect('Login/index');
	    }

	    $orderno=$this->createOrder();

	    if($orderno){
	        $where=array();
	        $where['orderno']=$orderno;
	        $db=M('order')->where($where)->find();
	        $db['type']='dish';
	        $this->assign('db',$db);
	        $this->assign('title','Succeed.');
	        $this->display('Shop/success');
	    }else{
	        $this->assign('title','Failed.');
	        $this->display('Shop/error');
	    }
	}

	/**
	 * 根据门店id生成订单
	 *
	 * @param number $shop_id
	 * @return string boolean
	 */
	protected function createOrder($shop_id = 0) {
	    if (IS_POST) {
	        // TODO:需要验证是否登录
	        $orderno = get_order_no ();
	        $ctrl = A ( 'Home/Cart' );
	        $db = $ctrl->getCartInfo ( $shop_id );
	        if ($db ['cart_num'] > 0) {
	            $data = empty ( $data ) ? $_POST : $data;
	            if(!is_date($data['delivertime'][0])){
	            	$this->error('Sorry, please select the delivery date.');
	            }
	            $data['delivertime'] = arr2str($data['delivertime'], ' ');
	             
	            // 校验订单：1. 查库存
	            $check = $this->checkOrder($data);
	            if ($check > 0) {
	            	//$this->error('Sorry, the  product numbered [' . $check . '] inventory shortage.', '', 6);
	            	$this->error('The stock is insufficient, we will try to have it soon.[#'.$check.']');
	            }

	            $addressid = $data['UseAddressID'];
                $address = AddressModel::getUserAddressById($addressid, get_userid ());
	            if($address){
				    if(isN($address['userreal'])){
				        $address['userreal']=get_username(get_userid());
				    }
	                $data ['username']=$address['userreal'];
	                $data ['telephone']=$address['telephone'];
	                $data ['sex']=$address['sex'];
	                $data ['address']=$address['address'];
// 	                $data ['provinceid']=$address['provinceid'];
// 	                $data ['cityid']=$address['cityid'];
// 	                $data ['districtid']=$address['districtid'];
// 	                $data ['proname']=$address['proname'];
 	                $data ['cityname']=$address['cityname'];
// 	                $data ['disname']=$address['disname'];
	                $data ['email']=$address['email'];
	                $data ['remark']=$address['info'];
	            }else{
	                $this->error ( 'Sorry,address error.' );

	            }
	           //使用优惠券抵扣
				if($data['usecoupon']){
				    //金额以购物车商品总额为准
				    $maxuse=get_coupon_maxuse($db['cart_amount']);
				    if($data['usecoupon']>$maxuse){
				        $data['usecoupon']=$maxuse;
				    }
				    $data['couponamount']=$data['usecoupon'];
				}else{
				    $data['couponamount']=0;
				}
				unset($data['usecoupon']);
                unset($data['consingee']);
	            $data ['orderfrom'] = 1;//微信订单
	            $data ['orderno'] = $orderno;
	            $data ['num'] = $db ['cart_num'];
	            $data ['amount'] = $db ['cart_amount']+ $db ['cart_shipfee']- $data['couponamount'];
	            $data ['amountall'] = $db ['cart_amount'];
	            $data ['shipfee'] = $db ['cart_shipfee'];
	            $data ['userid'] = get_userid ();
	            $data ['usertype'] = get_cate(get_userid (),'member','usertype');

	            $rate=lbl('rate');
	            if(is_decimal($rate)){
	                $data ['rate'] = $rate;
	            }
	            if($data['paymethod']==4){
	                $data['status']=0;
	            }else{
	                $data['status']=0;
	            }

	            $data ['addip'] = get_client_ip ();
	            $data ['shop_id'] = $shop_id;

	            unset($data['UseAddressID']);
	            //如果有积分支付，默认0
	            $credit=(!is_numeric($data['credit'])?0:$data['credit']);
	            if($credit!=0){
	                //如果积分不足
	                if(($credit>get_usercredit($data ['userid']))){
	                    $this->error('Points less!');
	                }

	                //计算积分抵扣的金额，要读取CREDIT_MONEY_RATE
	                $credit_money_rate=(!is_numeric(C('config.CREDIT_MONEY_RATE'))?0:C('config.CREDIT_MONEY_RATE'));
	                $creditamount=$credit*$credit_money_rate;
	                $data['creditamount']=$creditamount;
	                $data ['amount'] = $data ['amount']-$creditamount;

	                //参数 3:积分抵扣
	                $ret = $this->insertCredit ( $data ['userid'], 3, $credit, $orderno );

	            }

	            $db = M ( 'order' )->add ( $data );
	            if ($db != false) {

	                //抵扣优惠券
	                create_coupon(get_userid(),$data['couponamount'],4,$orderno);

	                $this->createSubOrder ( $orderno, $shop_id,$data['status'] );
	                $ctrl->updateCartInfo ();

	                $mailhtml = '';
	                $mailhtml=$this->mailhtml($orderno);

	                $data=array();
	                $where=array();
	                $where['orderno']=$orderno;
	                $data=M('order')->where($where)->find();

	                //客户邮件：send_mail();
	                $to=M('member')->where('id='.$data['userid'])->getField('email');
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
	                    if(send_mail($to,$subject,$body)){

	                    }
	                }

	                //管理员邮件：
	                //send_mail();
	                $to=ConfigModel::getConfig('WEB_SITE_COPYRIGHT');//获取管理员邮箱C('config.WEB_SITE_COPYRIGHT');
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

	                    $mailhtml=$this->mailhtml($orderno,1);
	                    $body.=$mailhtml;
	                    if(send_mail($to,$subject,$body)){
	                    }
	                }

	                //微信客服消息
	                $data = array();
	                $data['touser'] = M('member')->where('id='.get_userid())->getField('wechatid');
	                $data['msgtype'] = 'text';
	                $data['text'] =array('content'=>'thank you for order, we will deliver on time.');
                    $weChat = get_wechat_obj();
	                $ret = $weChat->sendCustomMessage($data);

	                set_order_onoff($orderno);
	                return $orderno;


	            } else {
	                return false;
	            }
	        } else {
	            // 购物车为空
	            return false;
	        }
	    } else {
	        // 非法提交
	        return false;
	    }
	}

	private function mailhtml($orderno='',$admin='') {
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
	 * 查库存
	 * @param string $data
	 */
	protected function checkOrder($data=null){
	
	    $cart = session ( 'cart_name' );
	    $items = $cart ['cart_items'];
	    $creditid=C('DEFAULT_CREDIT_CHANNEL');
	    foreach ( $items as $key => $value ) {
	        $id=str2arr($key,'_');
	        $id=$id[0];
	        $product=M('content')->find($id);
	        if($product['stock']<$value['num']){
	            //break;
	            return $id;
	            break;
	        }
	        //剔除积分产品
	        if($creditid){
	            if(in_array($creditid,str2arr( $product['sortpath']))){
	                unset ( $_SESSION ['cart_name'] ['cart_items'] [$id.'_'.$product['ext']] );
	            }
	        }
	    }
	    return 0;
	}
	/**
	 * 读取购物车信息，生成子订单
	 *
	 * @param unknown $orderno
	 */
	protected function createSubOrder($orderno = '', $shop_id = 0, $status = 0) {
		$isShop = false;
        $isService = false;
	    $cart = session ( 'cart_name' );
	    $items = $cart ['cart_items'];
	    $amountall=0;
	    
	    foreach ( $items as $key => $value ) {
	        if ($items [$key] ['shop_id'] == $shop_id) {
	            $productid = $items [$key] ['id'];
	            $ext = $items [$key] ['ext'];
	            $data = array ();
	            $db = M ( 'content' )->find ( $productid );
	            if (false != $db) {
	                $data ['orderno'] = $orderno;
	                $data ['productid'] = $db ['id'];
	                $data ['productname'] = $db ['title'];
	                $data ['indexpic'] = $db ['indexpic'];
	                $data ['sortpath'] = $db ['sortpath'];
	                $data ['supplyid'] = $db ['supplyid'];
	                $data ['supplyname'] = $db ['supplyname'];
	                $data ['unit'] = $db ['unit'];
	                $data ['namecn'] = $db ['namecn'];
	                $data ['price'] = get_price($productid,$db ['price'],$ext);//$db ['price'];
	                $data ['num'] = $items [$key] ['num'];
	                $data ['ext'] = $items [$key] ['ext'];
	                $data ['status'] = $status;
	                $data ['userid'] = get_userid();
	                M ( 'order_detail' )->add ( $data );
	                
	                if (strpos($db['sortpath'], ',2,')) {
                        $isShop = true;
                    }
                    if (strpos($db['sortpath'], ',3,')) {
                        $isService = true;
                    }
	                //1. 减少库存
	                M('content')->where('id='.$productid)->setDec('stock',$data['num']);
	                M('content')->where('id='.$productid)->setInc('sold',$data['num']);
	                $amountall+=$data['price']*$data['num'];
	                
	                unset ( $_SESSION ['cart_name'] ['cart_items'] [$productid.'_'.$ext] );
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
        $where = array();
        $where['orderno'] = $orderno;
        $find=M('order')->where($where)->find();
        if($find){
			$data1=array();
			$data1['amountall']=$amountall;
			$data1['amount']=$amountall+$find['shipfee']-$find['discount'];
			$data1['ordertype']=$ordertype;
			M('order')->where($where)->data($data1)->save();
        }
	}
	

	
	/**
	 * 普通订单（订餐）：支付成功
	 * @param string $orderno
	 * @param string $trade_no
	 * @param string $trade_status
	 * @param number $total_fee
	 * @return boolean
	 */
	public function payOrder($token=null,$out_trade_no = null,$trade_no= null,$trade_status= null,$total_fee=0,$openid='') {
		if($token!==md5(date('His'))){
			return false;
		}
		$orderno=$out_trade_no;
		if (isN ( $orderno )) { 
			return false;
		} else { 
			$where ['orderno'] = $orderno;
			$where ['status'] = 0;
			$where ['pay'] = 0;
			$db = M ( 'order' )->where ( $where )->find ();
			if (! $db == false) {
				$amount=$db['amount']*100;
				if($total_fee!=$amount){
					return false; 
				}else{
				$data=array();
				$data['id']=$db['id'];
				$data['pay']=1;	//已付款
				$data['paytime']=time_format();
				$data['paymethod']=1;	//1：支付宝
				$data['status']=1;	//订单自动确认
				$data['trade_no']=$trade_no;	//外部交易订单
				$data['openid']=$openid;	//微信 openid
				$ret=M('order')->save($data); 
				if($ret){
					$userid = $db['userid'];
					if($this->charge($userid,$amount,$trade_no)){
						if( $this->insertBalance ( $userid, 1, $amount, $orderno ) );
						return true;
					}else{
						return false;
					} 
				}else {
					return false;
				}
				}
				
				
			}else{
				return false;
			}
			
		}
	}
	
}
?>