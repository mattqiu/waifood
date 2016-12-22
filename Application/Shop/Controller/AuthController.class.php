<?php
namespace Shop\Controller;

use Common\Model\UserModel;

class AuthController extends BaseController
{
    protected $userId = null;

    public function _initialize()
    {
        //特殊节日优惠标示，结束后删除
        if(isset( $_REQUEST['hd']) &&  $_REQUEST['hd']){
            session('hd', $_REQUEST['hd']);
            cookie('hd', $_REQUEST['hd']);
        }
        $user = UserModel::getUser();
        if(!empty($user)){
            $this->userId = $user['id'];
            $user = UserModel::getUser();
            $this->assign('user',$user);
        }else{
            $this->redirect('/login/index');
        }
        parent::_initialize();
    }


}
?>