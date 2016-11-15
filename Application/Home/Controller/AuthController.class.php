<?php

namespace Home\Controller;

use Common\Model\UserModel;

class AuthController extends BaseController {
	public function _initialize() {
		// 加入权限判断
		$this->checkLogin ();
		parent::_initialize ();
	}
	
	private function checkLogin() {
		// 用户权限检查
		if (get_userid () == 0) {
			// 提示错误信息
			$this->redirect ( 'Login/index' );
		}else{
            //没有头像或没有微信名的用户重新获取用户信息
            $user = UserModel::getUserById(get_userid ());
            if($user['wechatid']||empty($user)){
                if(empty($user['indexpic']) || empty($user['weixin'])){
                    openid(false);
                    $this->redirect ( 'Login/index' );
                }
            }
        }
	}
	
	
}
?>