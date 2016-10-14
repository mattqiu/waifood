<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;
use Common\Model\AddressModel;
use Common\Model\CodeModel;
use Common\Model\UserModel;

class MemberController extends AuthController {
	// 修改登录密码
	public function pwd() {
		if (IS_POST) {
			$data = empty ( $data ) ? $_POST : $data;

			if(strlen($data['userpwd']==0)){
				$this->error ('sorry, the original password can not be empty!' );
			}
			if(strlen($data['userpwd1']<2)){
				$this->error ('sorry, the password must be at least 2!' );
			}
			
			if(isN($data['userpwd1'])||isN($data['userpwd2'])){
				$this->error ('Sorry, the new password can not be empty!' );
			}
			if(!($data['userpwd1']==$data['userpwd2'])){
				$this->error ('Sorry, enter the new password twice inconsistent!' );
			}
				
			$where=array();
			$where['id']=get_userid();
			$where['userpwd']=md5($data['userpwd']);
			$db=M('member')->where($where)->find();
			if ($db) {
				$db=M('member')->where($where)->setField('userpwd',md5($data['userpwd1']));
				if ($db !== false) {
					$this->success ( "Password changed successfully, please log in again!",U('Login/logout') );
				} else {
					$this->error ( 'Failed to change password!' );
				}
			} else {
				$this->error ('Sorry, the original password is incorrect!' );
			}
		} else {
			//seo信息
			$title = 'Modify Password'; 
			$this->assign('title',$title);
			$this->display ();
		}
	}
	
	public function invite(){
	    $title= 'Invite friends';
	    $this->assign('title',$title);
	    $this->display ();
	}
	
	/**
	 * 会员首页
	 */
	public function index($status = 0) {
        $userid =get_userid ();
        $db=UserModel::getUserById($userid);
        if($db){
            $where = array ();
            $where ['status'] = array('in',array(0,1,2));
            $where ['userid'] = get_userid ();
            $list = M ( 'order' )->where ( $where )->order ( 'id desc' )->limit ( 10 )->select ();
            $this->assign ( 'db', $db );
            $this->assign ( 'list', $list );
            $this->assign ( 'status', $status );
            $this->assign ( 'listcount', count ( $list ) );

            $this->assign ( 'orderstat', $this->orderStat(get_userid()));
            //seo信息
            $title = 'Member Center';
            $this->assign('title',$title);

            $this->display ();
		}else{
			session ( 'userid',null );
			$this->redirect('Login/index?member/index');
		}
	}
	

	/**
	 * 地址选择
	 */
	public function selectAddress($id=0) {
		// TODO:加分页
		if($id!=0){
			$this->setDefaultAddress($id);
			$this->redirect('Settle/cashier');
		}else{
		$where = array ();
		$where ['userid'] = get_userid ();
		$list = M ( 'address' )->where ( $where )->order ( 'isdefault desc,id desc' )->select ();
		$this->assign ( 'list', $list ); 
		$this->assign ( 'listcount', count ( $list ) );
		
		$title='Select shipping address';
		$this->assign('title',$title);
		$this->display ();
		}
	}
		

	/**
	 * 添加收货地址
	 */
	public function addAddress($url='') {
		If (IS_POST) {
			$data = empty ( $data ) ? $_POST : $data;
			
			if(isN($data['username'])){
				$this->error('Sorry,your name cannot be empty.');
			}
			if(isN($data['telephone'])){
				$this->error('Sorry,telephone number cannot be empty.');
			}
			
			$data ['userid'] = get_userid ();
			// $data['username']=get_username(get_userid());
			$data ['addip'] = get_client_ip ();
			$data ['provinceid'] = $data ['China_Province'];
			$data ['cityid'] = $data ['China_City'];
			$data ['districtid'] = $data ['China_District'];
			$data ['proname'] = M ( 'china_province' )->where ( 'ProId=' . $data ['China_Province'] )->getField ( 'ProName' );
			$data ['cityname'] = M ( 'china_city' )->where ( 'CityId=' . $data ['China_City'] )->getField ( 'CityName' );
			$data ['disname'] = M ( 'china_district' )->where ( 'Id=' . $data ['China_District'] )->getField ( 'DisName' );
			
			unset($data['China_Province'],$data['China_City'],$data['China_District'],$data['url']);
			$db = M ( 'address' )->add ( $data );
			if ($db) {
				$userreal=M('member')->where('id='.get_userid())->getField('telephone');
				if(isN($userreal)){
					$rs=array();
					$rs['userreal']=$data['username'];
					$rs['telephone']=$data['telephone'];
					$rs['sex']=$data['sex'];
					$rs['email']=$data['email'];
					$rs['address']=$data['address']; 
					M('member')->where('id='.get_userid())->save($rs);
				}
				
				$ctrl = A('Settle/Cart' );
				if ($ctrl->cart_num) {
					$this->success ( 'Address successfully added!', U ( 'Settle/cashier' ) );
				} else {
					if($url=="uc"){
						$this->success ( 'Address successfully added!', U ( 'Member/address' ) );
					}else{

						$this->success ( 'Address successfully added!', U ( 'Member/selectAddress' ) );
					}
					
				}
			} else {
				$this->error ( 'Sorry, address Failed to add!' );
			}
		} else {
			$this->assign ( 'title', 'Adding shipping address' );
			$this->display ();
		}
	}
	
	/**
	 * 省市县联动
	 * 
	 * @param string $tbl        	
	 * @param number $id        	
	 */
	public function getArea($tbl = 'china_province', $id = null) {
		$html = '';
		$html = get_area ( $tbl, $id );
		echo $html;
	}
	
	/**
	 * 修改收货地址
	 */
	public function editAddress($id=0,$url='') {
		If (IS_POST) {
			$data = empty ( $data ) ? $_POST : $data;
			
			if(isN($data['username'])){
				$this->error('Sorry,your name cannot be empty.');
			}
			if(isN($data['telephone'])){
				$this->error('Sorry,telephone number cannot be empty.');
			}
			
			$where ['id'] = $data ['id'];
			$where ['userid'] = get_userid ();
			// $data['username']=get_username(get_userid());
			$data ['addip'] = get_client_ip ();
			$data ['provinceid'] = $data ['China_Province'];
			$data ['cityid'] = $data ['China_City'];
			$data ['districtid'] = $data ['China_District'];
			$data ['proname'] = M ( 'china_province' )->where ( 'ProId=' . $data ['China_Province'] )->getField ( 'ProName' );
			$data ['cityname'] = M ( 'china_city' )->where ( 'CityId=' . $data ['China_City'] )->getField ( 'CityName' );
			$data ['disname'] = M ( 'china_district' )->where ( 'Id=' . $data ['China_District'] )->getField ( 'DisName' );

			unset($data['China_Province'],$data['China_City'],$data['China_District'],$data['url']);
			 
			$db = M ( 'address' )->where($where)->save ( $data );
			if ($db) {
				if($url=="uc"){

					$this->success ( 'Address modification success!', U ( 'Member/address' ) );
				}else{

					$this->success ( 'Address modification success!', U ( 'Member/selectAddress' ) );
				}
			} else {
				$this->error ( 'Sorry, address modification fails!' );
			}
		} else {
			$db = M ( 'address' )->where('userid='.get_userid())->find ( $id );
			$this->assign('db',$db);
			$this->assign ( 'title', 'Modify the shipping address!' );
			$this->display ();
		}
	}
	
	/**
	 * 设置默认地址
	 * @param number $id
	 */
	public function setDefaultAddress($id=0) {
		$where = array ();
		$where ['id'] = $id;
		$where ['userid'] = get_userid ();
		M ( 'address' )->where ( 'userid='.get_userid() )->setField ('isdefault',0);
		$db = M ( 'address' )->where ( $where )->setField ('isdefault',1);
		if($db){
			$this->success ( 'The default address setting success!' );
			} else {
			$this->error ( 'The default address is set to fail!' );
		}
	}
	/**
	 * 会员资料修改
	 */
	public function info1() {
        if(I('goto')=='cashier'){
            session('gocashier',true);
        }
        $id = I('addressid');
		$addr = AddressModel::getUserAddressById($id,get_userid ());
		$this->assign ( 'title', 'Personal information' );
		$this->assign ( 'db', $addr );
		$this->display ();
	}

    public function modifyShoppingAddr(){
        $consingee = I('post.username');
        $address = I('post.address');
        $tel = I('post.telephone');
        if(isN($consingee)){
            apiReturn(CodeModel::ERROR,'Sorry, name can not be empty!');
        }
        if(!regex($tel,'mob')){
            apiReturn(CodeModel::ERROR,'Sorry, the phone number format is wrong!');
        }
        if(isN($address)){
            apiReturn(CodeModel::ERROR,'Sorry,  address can not be empty!');
        }
        $data = I('post.');
        $userid = get_userid();
        if(true === AddressModel::addShoppingAddress($data,$userid)){
            $cashier= session('gocashier');
            if($cashier){
                session('gocashier',null);
                apiReturn(CodeModel::CORRECT,'','/settle/cashier');
            }else{
                apiReturn(CodeModel::CORRECT,'Personal information modified successfully!','/member/info');
            }
        }else {
            apiReturn(CodeModel::ERROR,'Personal information modified failed!');
        }
    }

	public function info() {
		if (IS_POST) {
			$db = D ( "member" );
			$data = empty ( $data ) ? $_POST : $data;
			$data = $db->create ( $data );
			if(isN($data['telephone'])){
				$this->error ('Sorry, the phone number can not be empty!' );
			}
			if(isN($data['email'])){
				$this->error ('Sorry, email can not be empty!' );
			}
			if(isN($data['address'])){
				$this->error ('Sorry,  address can not be empty!' );
			}

			if ($data != false) {
				$data ['id'] = get_userid ();
				if ($db->save ( $data ) !== false) {
				 $cashier= session('gocashier');
                   if($cashier){
                       session('gocashier',null);
                       redirect(U('Settle/cashier'));
                   }else{
                       $this->success('Personal information modified successfully!', U('Member/info'));
                   }
				} else {
					$this->error ( 'Personal information modified failed!' );
				}
			} else {
				$this->error ( $db->getError () );
			}
		} else {
            if(I('goto')=='cashier'){
                session('gocashier',true);
            }
			$db = M ( 'member' )->find ( get_userid () );
			$this->assign ( 'title', 'Personal information' );
			$this->assign ( 'db', $db );
			$this->display ();
		}
	}
	
	/**
	 * 删除收货地址
	 * 
	 * @param string $id        	
	 */
	public function deleteAddress($id = null) {
		$where = array ();
		$where ['id'] = $id;
		$where ['userid'] = get_userid ();
		$db = M ( 'address' )->where ( $where )->delete ();
		if ($db) {
			
			$this->success ( 'Address deleted!' );
		} else {
			$this->error ( 'Sorry, address delete failed!' );
		}
	}
	/**
	 * 地址列表
	 * 
	 * @param number $status        	
	 */
	public function address() {
		// TODO:加分页
		$where = array ();
		$where ['userid'] = get_userid ();
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( "address" )->where ( $where )->order ( 'isdefault desc,id desc' )->page ( $p, $row );
		$list = $rs->select ();
		$this->assign ( "list", $list );
		$count = $rs->where ( $where )->count ();
		
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%' );
			$page->setConfig ( 'prev', 'Prev' );
			$page->setConfig ( 'next', 'Next' );
			$this->assign ( 'page', $page->showm () );
		}
		
		$this->assign ( 'title', 'Address list' );
		$this->assign ( 'listcount', count ( $list ) );
		$this->display ();
	}

	/**
	 * 收藏列表
	 *
	 * @param number $status
	 */
	public function fav() {
		// TODO:加分页
		$where = array ();
		$where ['userid'] = get_userid ();
	
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
	
		$rs = M ( "fav" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
		$list = $rs->select ();
		$this->assign ( "list", $list );
		$count = $rs->where ( $where )->count ();
	
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%' );
			$page->setConfig ( 'prev', 'Prev' );
			$page->setConfig ( 'next', 'Next' );
			$this->assign ( 'page', $page->showm () );
		}
	
		$this->assign ( 'title', 'Favorite  list' );
		$this->assign ( 'listcount', count ( $list ) );
		$this->display ();
	}

	/**
	 * 浏览记录
	 */
	public function history() {
		$where=array();
		$arr=str2arr(cookie('view_history'));
		$arr=arr2clr($arr);
		$where['id']=array('in',$arr);
		$list=M('content')->where($where)->select();
		$this->assign ( 'listcount',  ( $list?$list:0 ) );
		$this->assign ( 'title', '浏览记录' );
		$this->assign('list',$list);
		$this->display();
	}
	
	public function clearHistory(){
		cookie('view_history',null);
		$this->success('浏览记录已清空！');
	}
	/**
	 * 删除收藏
	 *
	 * @param string $id
	 */
	public function deleteFav($id = null) {
		$where = array ();
		$where ['id'] = $id;
		$where ['userid'] = get_userid ();
		$db = M ( 'fav' )->where ( $where )->delete ();
		if ($db) {
				
			$this->success ( '收藏已删除！' );
		} else {
			$this->error ( '对不起，收藏删除失败！' );
		}
	}
	
	/**
	 * 订单列表
	 * 
	 * @param number $status        	
	 */
	public function order($status = 0) {
		$where = array ();
	 	$where ['status'] = $status; 
		$where ['userid'] = get_userid ();
		
		// 分页
		$p = intval ( I ( 'p' ) );
		$p = $p ? $p : 1;
		$row = C ( 'VAR_PAGESIZE' );
		
		$rs = M ( "order" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
		$list = $rs->select ();
		$this->assign ( "list", $list );
		$count = $rs->where ( $where )->count ();
		
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%' );
			$page->setConfig ( 'prev', 'Prev' );
			$page->setConfig ( 'next', 'Next' );
			$this->assign ( 'page', $page->showm () );
		}

		
		$this->assign ( 'orderstat', $this->orderStat(get_userid()));
		$this->assign ( 'title', 'My order' );
		$this->assign ( 'status', $status );
		$this->assign ( 'listcount', count ( $list ) );
		$this->display ();
	}

	/**
	 * 订单列表
	 *
	 * @param number $status
	 */
	public function coupon($status = 0) {
	    $where = array ();
	    if($status==0){
	        $where ['timefrom'] = array('elt',time_format());
	        $where ['timeto'] = array('egt',time_format());
	        //$where ['coupontype'] = 1;
	    } 
	    //$where ['coupontype'] = $status;
	    $where ['userid'] = get_userid ();
	    // 分页
	    $p = intval ( I ( 'p' ) );
	    $p = $p ? $p : 1;
	    $row = C ( 'VAR_PAGESIZE' );
	
	    $rs = M ( "coupon" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
	    $list = $rs->select ();
	    $this->assign ( "list", $list );
	    $count = $rs->where ( $where )->count ();
	
	    if ($count > $row) {
	        $page = new \Think\Page ( $count, $row );
	        $page->setConfig ( 'theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%' );
	        $page->setConfig ( 'prev', 'Prev' );
	        $page->setConfig ( 'next', 'Next' );
	        $this->assign ( 'page', $page->showm () );
	    }
	
	
	    $this->assign ( 'orderstat', $this->couponStat(get_userid()));
	    $this->assign ( 'title', 'My coupon' );
	    $this->assign ( 'status', $status );
	    $this->assign ( 'listcount', count ( $list ) );
	    $this->display ();
	}
	
	public function couponStat($userid){
	    $where=array();
	        $where ['timefrom'] = array('elt',time_format());
	        $where ['timeto'] = array('egt',time_format());
	    $where['userid']=$userid;
	    $count[]=M('coupon')->where($where)->count();
	    
	    
	    $where=array();
	    $where['userid']=$userid;
	    $count[]=M('coupon')->where($where)->count();
	    return $count;
	}
	
	/**
	 * 推广列表
	 *
	 * @param number $status
	 */
	public function friends($status = 0) {
	    $where = array ();
	    
	    $where ['fatherid'] = get_userid ();
	    // 分页
	    $p = intval ( I ( 'p' ) );
	    $p = $p ? $p : 1;
	    $row = C ( 'VAR_PAGESIZE' );
	
	    $rs = M ( "member" )->where ( $where )->order ( 'id desc' )->page ( $p, $row );
	    $list = $rs->select ();
	    $count = $rs->where ( $where )->count ();
	    foreach($list as $k=>$v){
	        $where1=array();
	        $where1['userid']=$v['id'];
	        $list[$k]['firstdate']=M('order')->where($where1)->order('id asc')->getField('addtime');
	        $list[$k]['lastdate']=M('order')->where($where1)->order('id desc')->getField('addtime');
	    }
	    $this->assign ( "list", $list );
	    if ($count > $row) {
	        $page = new \Think\Page ( $count, $row );
	        $page->setConfig ( 'theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%' );
	        $page->setConfig ( 'prev', 'Prev' );
	        $page->setConfig ( 'next', 'Next' );
	        $this->assign ( 'page', $page->showm () );
	    }
	
	 
	    $this->assign ( 'title', 'My friends list' ); 
	    $this->assign ( 'listcount', count ( $list ) );
	    $this->display ();
	}
	
	/**
	 * 查看订单详情
	 * 
	 * @param string $orderno        	
	 */
	public function orderView($orderno = null) {
		$where = array ();
		$where ['orderno'] = $orderno;
		$where ['userid'] = get_userid ();
		$db = M ( 'order' )->where ( $where )->find ();
		$this->assign ( 'db', $db );
		
		$where = array ();
		$where ['orderno'] = $orderno;
		$list = M ( 'order_detail' )->where ( $where )->order ( 'id asc' )->select ();
		$this->assign ( 'list', $list );
		
		$isShop=false;
		$isService=false;
		foreach ($list as $k=>$v){
		    if(strpos($v['sortpath'],',2,')){
		        $isShop=true;
		    }
		    if(strpos($v['sortpath'],',3,')){
		        $isService=true;
		    }
		}
		$this->assign ( 'isService', $isService );
		$this->assign ( 'isShop', $isShop );
		
		$this->assign ( 'title', 'View order detail-'.$orderno );
		$this->display ();
	}
	
	/**
	 * 查询用户：订单数量统计
	 * @param number $userid
	 */
	protected function orderStat($userid=0){
		$data=parse_field_attr(C('config.CONFIG_STATUS_LIST'));
		$arr=array();
		foreach ($data as $k=>$v){
			$where=array();
			$where['status']=$k;
			$where['userid']=$userid;
			$count=M('order')->where($where)->count();
			$arr[]=($count);
		}
		return($arr);
	}
	
	/**
	 * 删除【未处理】订单
	 * 
	 * @param string $orderno        	
	 */
	public function orderDelete($orderno = null) {
		$where = array ();
		$where ['status'] = 0;
		$where ['orderno'] = $orderno;
		$where ['userid'] = get_userid ();
		$db = M ( 'order' )->where ( $where )->delete ();
		if ($db) {
			$where = array ();
			$where ['orderno'] = $orderno;
			$db = M ( 'order_detail' )->where ( $where )->delete ();
			$this->success ( 'Order ' . $orderno . ' has been deleted.' );
		} else {
			$this->error ( 'Order ' . $orderno . ' delete failed.' );
		}
	}
	
	/**
	 * 账户 收支明细
	 * 
	 * @param number $status        	
	 */
	public function balance($balancetype = null) {
		// TODO:加分页
		$where = array ();
		$where ['userid'] = get_userid ();
		if ($balancetype != null) {
			$where ['balancetype'] = $balancetype;
		}
		$list = M ( 'balance' )->where ( $where )->order ( 'id desc' )->select ();
		$this->assign ( 'title', '账户收支明细' );
		$this->assign ( 'listcount', count ( $list ) );
		$this->assign ( 'list', $list );
		$this->display ();
	}
	
	/**
	 * 积分收支明细
	 * 
	 * @param number $status        	
	 */
	public function credit($credittype = null) {
		// TODO:加分页
		$where = array ();
		$where ['userid'] = get_userid ();
		if ($credittype != null) {
			$where ['credittype'] = $credittype;
		}
		$list = M ( 'credit' )->where ( $where )->order ( 'id desc' )->select ();
		$this->assign ( 'title', '积分收支明细' );
		$this->assign ( 'listcount', count ( $list ) );
		$this->assign ( 'list', $list );
		$this->display ();
	}
	
	/**
	 * 清洁
	 */
	public function cleaning()
	{
		$list = M('Ordernew')->alias('o')
			->join('INNER JOIN __ORDER_CLEANING__ oc ON oc.order_id=o.id')->where(array('o.member_id'=>get_userid (), 'o.type'=>3))->order('o.id desc')->select();
		$this->assign ( 'title', 'My cleaning' );
		$this->assign ( 'list', $list );
		$this->assign ( 'statusArr', C('ORDERNEW_STATUS'));
		$this->display();
	}
	
	/**
	 * 清洁详细
	 */
	public function cleaningView($orderno)
	{
		$where = array('o.orderno'=>$orderno, 'o.member_id'=>get_userid (), 'o.type'=>3);
		$order = M('Ordernew')->field('o.*,oc.*,oc.id as cleaning_id')->alias('o')->join('__ORDER_CLEANING__ oc ON oc.order_id=o.id')->where($where)->find();
		if( !$order ){
			$this->error('error');
		}
	
		$recordList = M('CleaningRecord')->where(array('cleaning_id'=>$order['cleaning_id']))->select();
	
		$this->assign ( 'title', 'Cleaning detail' );
		$this->assign ( 'statusArr', C('ORDERNEW_STATUS'));
		$this->assign ( 'db', $order );
		$this->assign ( 'recordList', $recordList );
		$this->display();
	}
	
	/**
	 * 租车
	 */
	public function carrental()
	{
		$list = M('Ordernew')->alias('o')
			->join('__ORDER_CARRENTAL__ oc ON oc.order_id=o.id')->where(array('o.member_id'=>get_userid (), 'o.type'=>4))->order('o.id desc')->select();
		
		$this->assign ( 'title', 'My car rental' );
		$this->assign ( 'list', $list );
		$this->assign ( 'statusArr', C('ORDERNEW_STATUS'));
		$this->display();
	}
	
	/**
	 * 租车详细
	 */
	public function carrentalView($orderno)
	{
		$where = array('o.orderno'=>$orderno, 'o.member_id'=>get_userid (), 'o.type'=>4);
		$order = M('Ordernew')->alias('o')->join('__ORDER_CARRENTAL__ oc ON oc.order_id=o.id')->where($where)->find();
		if( !$order ){
			$this->error('error');
		}
		
		$detailList = M('OrderCarrentalDetail')->where(array('order_id'=>$order['order_id']))->select();
		
		$this->assign ( 'title', 'Car rental detail' );
		$this->assign ( 'statusArr', C('ORDERNEW_STATUS'));
		$this->assign ( 'db', $order );
		$this->assign ( 'detailList', $detailList );
		$this->display();
	}
}
?>