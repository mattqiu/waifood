<?php
// 本类由系统自动生成，仅供测试用途
namespace Shop\Controller;
use Common\Model\AddressModel;
use Common\Model\CodeModel;
use Common\Model\ContentModel;
use Common\Model\OrderModel;
use Common\Model\UserModel;
use Think\Page;

class MemberController extends AuthController {
    /**
     * 会员首页
     */
    public function index() {
        $user = UserModel::getUser();
        $where ['status'] = array('in',array(0,1,2));
        $where ['userid'] = $user['id'];
        $list = M ( 'order' )->where ( $where )->order ( 'id desc' )->limit ( 10 )->select ();
        $this->assign ( 'list', $list );
        $this->assign('user',$user);
        //seo信息
        $title = 'Member Center';
        $this->assign('title',$title);
        $this->display ();
    }

    /**
     * 添加修改地址页面
     */
    public function modifyAddress(){
        $id = I('id');
        if(regex($id,'number')){
            $address =AddressModel:: getUserAddressById($id,$this->userId);
            $this->assign('address',$address);
        }
        $title='Select shipping address';
        $this->assign('title',$title);
        $this->display ('modify_address');
    }

    /**
     * 添加或修改地址
     */
    public function modifyShoppingAddr(){
        $data = I('post.');
        $consingee = I('post.username');
        $address = I('post.address');
        $tel = $data['telephone'] = replaceTel($data['telephone']);
        if(isN($consingee)){
            apiReturn(CodeModel::ERROR,'Please input name');
        }
        if(!regex($tel,'mob')){
            apiReturn(CodeModel::ERROR,'Wrong phone number format.');
        }
        if(isN($address)){
            apiReturn(CodeModel::ERROR,'Please input address.');
        }
        $userid = $this->userId;
        if($re = AddressModel::modifyShoppingAddress($data,$userid)){
            $cashier= session('gocashier');
            if($cashier){
                session('gocashier',null);
                apiReturn(CodeModel::CORRECT,'','/settle/cashier');
            }else{
                apiReturn(CodeModel::CORRECT,'Successful.','/member/address');
            }
        }else {
            apiReturn(CodeModel::ERROR,'Failed, unexpected problem.');
        }
    }

	/**
	 * 删除收货地址
	 *
	 * @param string $id
	 */
	public function deleteAddress() {
        $id = I('id');
		$where = array ();
		$where ['id'] = $id;
		$where ['userid'] = $this->userId;
		$db = M ( 'address' )->where ( $where )->delete ();
		if ($db) {
            apiReturn(CodeModel::CORRECT,'Successful.');
		} else {
            apiReturn(CodeModel::ERROR,'Failed, unexpected problem.');
		}
	}

	/**
	 * 地址列表
	 *
	 * @param number $status
	 */
	public function address() {
        $address = AddressModel::getUserAddress($this->userId);
		$this->assign ( "list", $address );
		$this->assign ( 'title', 'Address list' );
		$this->display ();
	}

    /**
     * 订单列表
     *
     * @param number $status
     */
    public function order() {
        $con['userid'] = $this->userId;
        $con['_string'] = '`status` < '.OrderModel::COMPLETED;
        $ongoing = M ( "order" )->where ( $con )->count(); //进行中的订单数
        $con1['_string'] = '`status` < '.OrderModel::CANCELLED;
        $con1['userid'] = $this->userId;
        $all = M ( "order" )->where ( $con1 )->count();//所有订单数
        $status = I('status');
        if($status == 'all'){
            $count = $all;
            $where['_string'] = '`status` <= '.OrderModel::COMPLETED;
        }else{
            $count = $ongoing;
            $where['_string'] = '`status` < '.OrderModel::COMPLETED;
        }
        $where ['userid'] = $this->userId;
        $row = C ( 'VAR_PAGESIZE' );
        $page =  new Page($count,$row);
        $list = M("order")->where($where)
            ->limit($page->firstRow.",".$page->listRows)->order ( 'id desc' )->select();
        $this->assign("list",$list);
        $this->assign("ongoing",$ongoing);
        $this->assign("all",$all);
        $this->assign("page",$page->show());
        $this->assign ( 'title', 'My order' );
        $this->display ();
    }

    /**
     * 查看订单详情
     *
     * @param string $orderno
     */
    public function orderView() {
        $orderno = I('orderno');
        $where ['orderno'] = $orderno;
        $where ['userid'] = $this->userId;
        $order = M ( 'order' )->where ( $where )->find ();
        if($order){
            $con ['orderno'] = $orderno;
            $list = M ( 'order_detail' )->where ( $con )->order ( 'id asc' )->select ();
            $this->assign ( 'list', $list );
        }
        $this->assign ( 'order', $order );
        $this->assign ( 'title', 'View order detail-'.$orderno );
        $this->display ('orderView');
    }

    /**
     *修改密码页面
     */
    public function pwd(){
        $this->display ('changePwd');
    }

    /**
     * 修改密码
     */
    public function changeUserPwd(){
       $oldpwd = I('old_pwd');
       $pwd = I('pwd');
       $pwd1 = I('pwd1');
        if(!$oldpwd){
            apiReturn(CodeModel::ERROR,"Please enter the old password");
        }
        $user = UserModel::getUser();
        if($user['userpwd'] !== md5($oldpwd)){
            apiReturn(CodeModel::ERROR,"wrong password");
        }
        if(!$pwd || strlen($pwd)>20 ||strlen($pwd)<4){
            $this->error('Password should be at least 4 characters.');
        }
        if($pwd !==$pwd1){
            apiReturn(CodeModel::ERROR,'The two passwords are not same.');
        }
        $data['userpwd'] = md5($pwd);
        if(UserModel::modifyMember($this->userId,$data)){
            session_unset ();
            session_destroy ();
            session('[destroy]');//修改成功后，重新登录
            apiReturn(CodeModel::CORRECT,"Successful.");
        }else{
            apiReturn(CodeModel::ERROR,'Failed, unexpected problem.');
        }
    }

    public function info(){
        $user = UserModel::getUser();
        $this->assign ( 'user',$user);
        $this->display ();
    }

    public function modifyUserInfo(){
        $data = array_filter(I('post.'));
        $username = I('post.username');
        $email = I('post.email');
        $tel = I('post.telephone');
        if(!$username){
            apiReturn(CodeModel::ERROR,'Please input name');
        }
        if(!regex($email,'email')){
            apiReturn(CodeModel::ERROR,'Wrong Email format.');
        }
        if(!regex($tel,'mob')){
            apiReturn(CodeModel::ERROR,'Wrong phone number format.');
        }
        if(UserModel::modifyMember($this->userId,$data)){
            $user = UserModel::getUserById($this->userId);
            UserModel::setUser($user);
            apiReturn(CodeModel::CORRECT,'Successful.');
        }else {
            apiReturn(CodeModel::ERROR,'Failed, unexpected problem.');
        }
    }

}
?>