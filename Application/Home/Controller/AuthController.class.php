<?php

namespace Home\Controller;

use Common\Model\UserModel;

class AuthController extends BaseController {
	public function _initialize() {
        //特殊节日优惠标示，结束后删除
        if(isset( $_REQUEST['hd']) &&  $_REQUEST['hd']){
            session('hd', $_REQUEST['hd']);
            cookie('hd', $_REQUEST['hd']);
        }
//        if(UserModel::isAttention()){
//            $this->assign('subscribe', true); //关注
//        }else{
//            $this->assign('subscribe', false); //未关注
//        }
		// 加入权限判断
		$this->checkLogin ();
		parent::_initialize ();
	}
	
	private function checkLogin() {
        // 用户权限检查
        $user = UserModel::getUser();
		if(empty($user)){
            // 提示错误信息
			$this->redirect ( 'Login/index' );
		}else{
            if($user['wechatid']|| empty($user)){
                //没有微信名的用户重新获取用户信息
                if(empty($user['weixin'])){
                    openid(false);
                    S('oldUserOpenid',$user['wechatid']);
                    $this->redirect ( 'Login/index' );
                }
            }
        }
	}

	
}
?>