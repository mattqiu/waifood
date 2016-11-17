<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;

class Settle1Controller extends BaseController {
	
	
	/**
	 * 购物车
	 *
	 * @param number $shop_id
	 */
	public function cart($shop_id = 0) {
		// 购物车
		$ctrl = A ( 'Shop/Cart' );
		$data = $ctrl->loadCart ();
		if ($data['cart_num'] == 0) {
			$this->assign ( 'cartnum', 0 );
		} else {
		    $isShop=false;
		    $isService=false;
		    foreach ($data['cart_items'] as $k=>$v){
		      if(strpos($v['sortpath'],',2,')){
		          $isShop=true;
		      }
		      if(strpos($v['sortpath'],',3,')){
		          $isService=true;
		      }
		    } 
			$this->assign ( 'isService', $isService );
			$this->assign ( 'isShop', $isShop );
			$this->assign ( 'cart', $data );
		}
	
		$this->assign ( 'title', 'My shopping cart' );
		$this->display ();
	}
	
	/**
	 * 购物车结算
	 *
	 * @param number $shop_id        	
	 */
	public function cashier($shop_id = 0,$usecoupon='') {
		// 购物车
		$ctrl = A ( 'Shop/Cart' );
		$data = $ctrl->loadCart (); 
		if ($data['cart_num'] == 0) { 
			$this->assign ( 'cartnum', 0 );
			redirect(U('/'));
		} else {
			
			$where = array ();
			$where ['userid'] = get_userid ();
			$order = 'isdefault desc,id desc';
			$address = is_address();//M ( 'address' )->where($where)->order ( $order )->find ();
			if ($address == false) {
			    session('gocashier',true);
				$this->redirect ( 'Member/addAddress' );
			} else {
				$this->assign ( 'address', $address );
			} 
			

			$isShop=false;
			$isService=false;
			foreach ($data['cart_items'] as $k=>$v){
			    if(strpos($v['sortpath'],',2,')){
			        $isShop=true;
			    }
			    if(strpos($v['sortpath'],',3,')){
			        $isService=true;
			    }
			}
			$this->assign ( 'isService', $isService );
			$this->assign ( 'isShop', $isShop );
			$this->assign ( 'cart', $data );
		}
		
		$mycoupon=get_my_coupon();
		$maxuse=get_coupon_maxuse($data['cart_amount']);
		if($usecoupon>$maxuse){
		    $usecoupon=$maxuse;
		}
		
		$this->assign ( 'mycoupon', $mycoupon );
		$this->assign ( 'maxuse', $maxuse );
		$this->assign ( 'usecoupon', to_price($usecoupon) );
		
		$this->assign ( 'title', 'Cashier' );
		$this->display ();
	}
	
	/**
	 * 订单支付
	 *
	 * @param string $orderno        	
	 * @param string $type        	
	 */
	public function pay($orderno = null, $type = 'dish',$paytype='paypal') {
		$where = array ();
		switch ($type) {
			case 'dish' :
				$where ['orderno'] = $orderno;
				$where ['paymethod'] = array (
						'neq',
						4 
				);
				$where ['status'] = array (
						'not in',
						array (
								3,
								4 
						) 
				);
				$db = M ( 'order' )->where ( $where )->find ();
				
				break;
				
			case 'charge' :
				dump ( 'charge' );
				break;
		}
		if ($db) {
			// 正常订单，选择支付方式:alipay
			 
		switch ($paytype) {
			case 'alipay' :
				//Alipay
				$payapiurl = U('Pay/index');
				$amount = $db ['amount'];
				$html = '';
				$html .= '<form name="alipay_submit" action="' . $payapiurl . '" method="post" >';
				$html .= '  <input type="hidden" name="paytype" value="alipay"/>';
				$html .= '  <input type="hidden" name="WIDout_trade_no" value="' . $orderno . '"/>';
				$html .= '  <input type="hidden" name="WIDtotal_fee" value="' . $amount . '" />';
				$html .= '  <input type="hidden" name="WIDsubject"  value="[alipay]online pay system."  />';
				$html .= '  <button type="submit" style="text-align:center;display:none;">submit</button>';
				$html .= '</form>';
				$html .= '<script>document.forms["alipay_submit"].submit();</script>';
				
				break;
			case 'paypal':
				//paypal
			
				$payapiurl = U('Pay/index');
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
					
				break;
		}
			echo ($html);
		} else {
			$this->error ( 'Order ' . $orderno . ' does not exist or without paying4!' );
		}
	}
}
?>