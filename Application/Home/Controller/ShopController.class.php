<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;

use Common\Model\AddressModel;
use Common\Model\CodeModel;
use Common\Model\DateModel;
use Common\Model\UserModel;

class ShopController extends BaseController {

    public function getdeliveryFee(){
        $money = I('money');
        if($money){
           $deliveryFee = getShipfee(intval($money));
            apiReturn(CodeModel::CORRECT,'',$deliveryFee);
        }
    }
	/**
	 * 购物车
	 *
	 * @param number $shop_id
	 */
	public function cart($shop_id = 0) {
		// 购物车
		$ctrl = A ( 'Home/Cart' );
		$data = $ctrl->loadCart ();
		if ($data['cart_num'] == 0) {
			$this->assign ( 'cartnum', 0 );
		} else { 
			$this->assign ( 'cart', $data );
		}

		$this->assign ( 'cart_shipfee',  lbl ( 'freight' ));
		$this->assign ( 'title', 'Shopping cart' );
		$this->display ();
	}
	
	/**
	 * 购物车结算
	 *
	 * @param number $shop_id        	
	 */
	public function cashier() {
        // 用户权限检查
        if (get_userid() == 0) {
            if($_REQUEST['gocashier']){
                session('gocashier',true);
            }
            if(is_wechat()){
                $openid = openid();
                if (strlen($openid) == 28) {
                    openid($openid);
                    UserModel::loginWechat($openid);
                }
            }else{
                $this->redirect ( 'Login/index' );
            }
        }
        /*获取用户地址*/
        $addrId = I('addr_id');
        if(regex($addrId,'number')){
            $address = AddressModel::getUserAddressById($addrId,get_userid());
        }else{
            $addresslist =AddressModel:: getUserAddress(get_userid());
        }
        if ($address == false && $addresslist == false) {
            session('gocashier',true);
            $this->redirect ( 'Member/addAddress' );
        }else {
            if($addresslist){
                $this->assign ( 'address', $addresslist[0]);
            }else{
                $this->assign ( 'address', $address );
            }
        }
        //获取可配送日期
        $date = DateModel::getFutureDay(15,true);//获取未来15天的日期
        $this->assign ( 'date', $date );
        if($dateData = DateModel::getFutureDay(15)){
            $this->assign ( 'beyond',DateModel::DELIVERTIME_BEYOND);
            $this->assign ( 'dateData',$dateData);
        }

        $this->assign ( 'deliverinfo',lbl('deliverinfo') );
        $this->assign ( 'times',  $times = str2arr(lbl('delivertime'),"\r\n"));
        $this->assign ( 'title', 'Cashier' );
        $this->display ();
    }
	public function cashier1($shop_id = 0,$coupon='',$code='',$openid='',$usecoupon='') {
	    if(is_wechat()){
	        $weChat = get_wechat_obj ( );
	        if ($code == '') {
	            $url = $weChat->getOauthRedirect ( get_current_url () );
	            redirect ( $url );
	        } else {
	            $accessToken = $weChat->getOauthAccessToken ();
	            if ($accessToken) {
	                $openid = $accessToken ['openid'];
	                openid($openid );
	                //判断是否绑定，提示绑定
	                if(!is_bind($openid)){

	                    redirect(U('Login/bind'));
	                }
	            } else {
	                echo ('access denied.');
	                exit ();
	            }
	        }
	    }
		// 购物车
		$ctrl = A ( 'Home/Cart' );
		$data = $ctrl->loadCart ();
		if ($data['cart_num'] == 0) {
			$this->assign ( 'cartnum', 0 );
		} else {
            $addrId = I('addr_id');
            if(regex($addrId,'number')){
                $address = AddressModel::getUserAddressById($addrId,get_userid());
            }else{
                $addresslist =AddressModel:: getUserAddress(get_userid());
                //$address =AddressModel::getUserDefaultAddress( );
            }
			if ($address == false && $addresslist == false) {
			    session('gocashier',true);
				$this->redirect ( 'Member/addAddress' );
			} else {
                if($addresslist){
                    $this->assign ( 'addresslist', $addresslist );
                }else{
                    $this->assign ( 'address', $address );
                }
			}
			$this->assign ( 'cart', $data );
		}
        $date = DateModel::getFutureDay(15,true);//获取未来15天的日期
        $this->assign ( 'date', $date );

        if($dateData = DateModel::getFutureDay(15)){
            $this->assign ( 'beyond',DateModel::DELIVERTIME_BEYOND);
            $this->assign ( 'dateData',$dateData);
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
	public function pay($orderno = null,$paytype='paypal') {
		$where = array ();
        $where ['orderno'] = $orderno;
        $where ['paymethod'] = array ('neq',4 );
        $where ['status'] = array ('not in',	array (3,4 ));
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

    /**
	 * 订单支付==============={{{{{{{{$type不确定，确认后可删除}}}}}}=======================
	 *
	 * @param string $orderno
	 * @param string $type
	 */
	public function pay_old($orderno = null, $type = 'dish',$paytype='paypal') {
		$where = array ();
		switch ($type) {
			case 'dish' :
				$where ['orderno'] = $orderno;
				$where ['paymethod'] = array ('neq',4 );
				$where ['status'] = array ('not in',	array (3,4 )
				);
				$db = M ( 'order' )->where ( $where )->find ();
				break;

		}
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
			//$this->error ( '订单' . $orderno . '不存在或无需支付！' );
			$this->error ( 'Order ' . $orderno . ' does not exist or without paying!' );
		}
	}
}
?>