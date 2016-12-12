<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;

use Common\Model\AddressModel;
use Common\Model\CodeModel;
use Common\Model\OrderModel;
use Common\Model\UserModel;

class MemberController extends AuthController
{
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
 
    /**
     * 订单统计
     *
     * @param string $date1            
     * @param string $date2            
     */
    public function statistic($date1 = '', $date2 = '')
    {
        $ids = M('member')->where('fatherid=' . get_userid())->getField('id');
        $where = array(
            'userid' => array(
                'in',
                $ids
            )
        );
        if (is_date($date1)) {
            $where['addtime'][] = array(
                'egt',
                $date1
            );
        }
        if (is_date($date2)) {
            $ctrl = new \Org\Util\Date($date2);
            $date2 = $ctrl->dateAdd(1)->format();
            $where['addtime'][] = array(
                'elt',
                $date2
            );
        }
        
        $db = M('order')->where($where)->select();
        $this->assign('list', $db);
        $this->display();
    }

    /**
     * 会员首页
     */
    public function index()
    {
        $user = UserModel::getUserById(get_userid());
        $this->assign('user', $user);
        $this->assign('title', 'Me');
        $this->display();

    }

    /**
     * 会员编辑
     */
    public function editMember()
    {
        $user = UserModel::getUserById(get_userid());
        $this->assign('user', $user);
        $this->assign('title', 'Me');
        $this->display();
    }

    /**
     * 会员编辑
     */
    public function modifyMember()
    {
       $data = I('post.');
        $userid = $data['id'];
        unset($data['id']);
        if(UserModel::modifyMember($userid,$data)){
            $this->success('Modify the success!', U('Member/index'));
        } else {
            $this->error('Sorry,repair failure!');
        };
    }

    /**
     * 地址选择
     */
    public function selectAddress($id = 0)
    {
        // TODO:加分页
        if ($id != 0) {
            $this->setDefaultAddress($id);
            $this->redirect('m_cashier');
        } else {
            $where = array();
            $where['userid'] = get_userid();
            $list = M('address')->where($where)
                ->order('isdefault desc,id desc')
                ->select();
            $this->assign('list', $list);
            $this->assign('listcount', count($list));
            
            $this->assign('title', 'Address');
            $this->display('selectAddress');
        }
    }

    /**
     * 添加收货地址
     */
    public function addAddress()
    {
        If (IS_POST) {
            $data = empty($data) ? $_POST : $data;
            $data['userid'] = get_userid();
            // $data['username']=get_username(get_userid());
            $data['addip'] = get_client_ip();
            $data['provinceid'] = $data['China_Province'];
            $data['cityid'] = $data['China_City'];
            $data['districtid'] = $data['China_District'];
            $data['proname'] = M('china_province')->where('ProId=' . $data['China_Province'])->getField('ProName');
            $data['cityname'] = M('china_city')->where('CityId=' . $data['China_City'])->getField('CityName');
            $data['disname'] = M('china_district')->where('Id=' . $data['China_District'])->getField('DisName');
            $db = M('address')->add($data);
            if ($db) {
                $cart = session('cart_name');
                $num = $cart['cart_num'];
                if ($num) {
                    $this->success('Address successfully added!', U('m_cashier'));
                } else {
                    $this->success('Address successfully added!', U('Member/address'));
                }
            } else {
                $this->error('Sorry, address Failed to add');
            }
        } else {
            $addrId = I('addr_id');
            if(regex($addrId,'number')){
                $this->assign('addr',AddressModel::getUserAddressById($addrId,get_userid()));
            }else{
                $addr = array();
                $user = UserModel::getUserById(get_userid());
                $addr['username'] = $user['username'];
                $addr['telephone'] = $user['telephone'];
               $this->assign('addr',$addr);
            }
            if(I('goto')=='cashier'){
                session('gocashier',true);
            }
            $this->assign('title', 'Address');
            $this->display('addAddress');
        }
    }

    /**
     * 省市县联动
     *
     * @param string $tbl            
     * @param number $id            
     */
    public function getArea($tbl = 'china_province', $id = null)
    {
        $html = '';
        $html = get_area($tbl, $id);
        echo $html;
    }

    /**
     * 修改收货地址
     */
    public function editAddress($id = 0)
    {
        If (IS_POST) {
            $data = empty($data) ? $_POST : $data;
            $where['id'] = $data['id'];
            $where['userid'] = get_userid();
            // $data['username']=get_username(get_userid());
            $data['addip'] = get_client_ip();
            $data['provinceid'] = $data['China_Province'];
            $data['cityid'] = $data['China_City'];
            $data['districtid'] = $data['China_District'];
            $data['proname'] = M('china_province')->where('ProId=' . $data['China_Province'])->getField('ProName');
            $data['cityname'] = M('china_city')->where('CityId=' . $data['China_City'])->getField('CityName');
            $data['disname'] = M('china_district')->where('Id=' . $data['China_District'])->getField('DisName');
            $db = M('address')->where($where)->save($data);
            if ($db) {
                $this->success('Address modification success!', U('Member/address'));
            } else {
                $this->error('Sorry, address modification fails!');
            }
        } else {
            $db = M('address')->where('userid=' . get_userid())->find($id);
            $this->assign('db', $db);
            $this->assign('title', 'Address');
            $this->display('editAddress');
        }
    }

    /**
     * 设置默认地址
     *
     * @param number $id            
     */
    public function setDefaultAddress($id = 0)
    {
        $where = array();
        $where['id'] = $id;
        $where['userid'] = get_userid();
        M('address')->where('userid=' . get_userid())->setField('isdefault', 0);
        $db = M('address')->where($where)->setField('isdefault', 1);
        if ($db) {
            $this->success('默认地址设置成功！');
        } else {
            $this->error('默认地址设置失败！');
        }
    }

    /**
     * 添加或修改地址
     */
    public function modifyShoppingAddr(){
        $data = I('post.');
        $username = I('post.username');
        $address = I('post.address');
        $tel = $data['telephone'] =replaceTel(I('post.telephone'));
        if(isN($username)){
            apiReturn(CodeModel::ERROR,'Please input name');
        }
        if(!regex($tel,'mob')){
            apiReturn(CodeModel::ERROR,'Wrong phone number format.');
        }
        if(isN($address)){
            apiReturn(CodeModel::ERROR,'Sorry, address can not be empty!');
        }
        $userid = get_userid();
        if(true === AddressModel::modifyShoppingAddress($data,$userid)){
            $cashier= session('gocashier');
            if($cashier){
                session('gocashier',null);
                apiReturn(CodeModel::CORRECT,'Successful.','/m_cashier');
            }else{
                apiReturn(CodeModel::CORRECT,'Successful.','/member/address');
            }
        }else {
            apiReturn(CodeModel::ERROR,'Failed, unexpected problem.');
        }
    }

    /**
     * 会员资料修改
     */
    public function info()
    {
        if (IS_POST) {
            $db = D("member");
            $data = empty($data) ? $_POST : $data;
            $data = $db->create($data);
//             if (isN($data['userreal'])) {
//                 $this->error('Sorry, name can not be empty!');
//             }
            if (isN($data['telephone'])) {
                $this->error('Sorry, the phone number can not be empty!');
            }
            $data['telephone'] = replaceTel($data['telephone']);
            if(strlen($data['telephone'])<7){
                $this->error('Sorry, phone number format is wrong');
            }
            if (isN($data['email'])) {
                $this->error('Sorry, email can not be empty!');
            }
            if (isN($data['address'])) {
                $this->error('Sorry,  address can not be empty!');
            }
            if ($data != false) {
                $saveData['telephone'] = $data['telephone'];
                $saveData['address'] = $data['address'];
                if (AddressModel:: modifyDefaultAddress(get_userid(),$saveData) !==false) {
                   $cashier= session('gocashier');
                   if($cashier){
                       session('gocashier',null);
                       redirect('/m_cashier');
                   }else{
                       $this->success('Personal information modified successfully!', U('Member/info'));
                   }
                }
            } else {
                $this->error($db->getError());
            }
        } else {
            if(I('goto')=='cashier'){
                session('gocashier',true);
            }
            $db = UserModel::getUserById(get_userid());
            $this->assign('title', 'Personal information');
            $this->assign('db', $db);
            $this->display();
        }
    }

    /**
     * 会员资料修改
     */
    public function info1()
    {
        $db = UserModel::getUserById(get_userid());
        $this->assign('title', 'Personal information');
        $this->assign('db', $db);
        $this->display();
    }

    /**
     * 地址列表
     *
     * @param number $status            
     */
    public function address()
    {
        if($_GET['goto'] == 'cashier'){
            session('gocashier',true);
        }
        // TODO:加分页
        $where = array();
        $where['userid'] = get_userid();
        
        // 分页
        $p = intval(I('p'));
        $p = $p ? $p : 1;
        $row = C('VAR_PAGESIZE');
        
        $rs = M("address")->where($where)
            ->order('isdefault desc,id desc')
            ->page($p, $row);
        $list = $rs->select();
        $this->assign("list", $list);
        $count = $rs->where($where)->count();
        if ($count > $row) {
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%');
            $page->setConfig('prev', '上一页');
            $page->setConfig('next', '下一页');
            $this->assign('page', $page->showm());
        }
        $this->assign('title', 'Address');
        $this->assign('listcount', count($list));
        $this->display();
    }

    /**
     * 订单列表
     *
     * @param number $status
     */
    public function order($status = 0)
    {
        $where = array();
        //$where['status'] = $status;
        $where['status'] = array('neq',4);
        $where['userid'] = get_userid();

        // 分页
        $p = intval(I('p'));
        $p = $p ? $p : 1;
        $row = C('VAR_PAGESIZE');

        $rs = M("order")->where($where)
            ->order('id desc')
            ->page($p, $row);
        $list = $rs->select();
        $this->assign("list", $list);
        $count = $rs->where($where)->count();

        if ($count > $row) {
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme', '%UP_PAGE% %LINK_PAGE% %DOWN_PAGE%');
            $page->setConfig('prev', 'Prev');
            $page->setConfig('next', 'Next');
            $this->assign('page', $page->showm());
        }

        $this->assign('title', 'My orders');
        $this->assign('status', $status);
        $this->assign('listcount', count($list));

//         $num = array();
//         $num[0]=M('order')->where(array('status'=>0,'userid'=>get_userid()))->count();
//         $num[1]=M('order')->where(array('status'=>1,'userid'=>get_userid()))->count();
//         $num[2]=M('order')->where(array('status'=>2,'userid'=>get_userid()))->count();
//         $num[3]=M('order')->where(array('status'=>3,'userid'=>get_userid()))->count();
//         $this->assign('num', $num);

        $this->display();
    }

    /**
     * 加载更多
     */
    public function getOrderList(){
        $type = I('post.type');
        $where['userid'] = get_userid();
        if($type == 'all'){
            $where['status'] = array('lt',OrderModel::CANCELLED);
        }else{
            $where['_string'] = '`status` < '.OrderModel::COMPLETED;
        }
        $page = intval(I('post.page'));
        $page = $page ? $page : 1;
        $row = 10;
        $star = ($page-1)*$row;
        $count = M("order")->where($where)->count();//总数
        $list = M("order")->where($where)->order('id desc')->limit($star, $row)->select();
        $data['totalpage'] =  ceil($count/$row);
        foreach($list as &$val){
            $val['status_type'] = get_status($val['status']);
        }
        $data['list'] = $list;
        apiReturn(CodeModel::CORRECT,'',$data);
    }

    /**
     * 查看订单详情
     *
     * @param string $orderno            
     */
    public function orderView($orderno = null)
    {
        $where = array();
        $where['orderno'] = $orderno;
        $where['userid'] = get_userid();
        $db = M('order')->where($where)->find();
        $this->assign('db', $db);
        $where = array();
        $where['orderno'] = $orderno;
        $list = M('order_detail')->where($where) ->order('id asc') ->select();
        $this->assign('list', $list);
        $this->assign('title', 'Order detail');
        $this->display('orderView');
    }

    /**
     * 删除【未处理】订单
     *
     * @param string $orderno            
     */
    public function orderDelete($orderno = null)
    {
        $where = array();
        $where['status'] = 0;
        $where['orderno'] = $orderno;
        $where['userid'] = get_userid();
        $db = M('order')->where($where)->delete();
        if ($db) {
            $where = array();
            $where['orderno'] = $orderno;
            $db = M('order_detail')->where($where)->delete();
            $this->success('Order ' . $orderno . ' has been deleted.');
        } else {
            $this->error('Order ' . $orderno . ' delete failed.');
        }
    }
}
?>