<?php

namespace Home\Controller;

class AuthController extends BaseController {
	public function _initialize() {
        ///$this->redirect ( 'WeixinLogin/index' );
        $this->redirect ( 'Login/index' );
		// 加入权限判断
		$this->checkLogin ();
		parent::_initialize ();
	}
	
	private function checkLogin() {
		// 用户权限检查
		if (get_userid () == 0) {
			// 提示错误信息
			$this->redirect ( 'Login/index' );
		}
	}
	
	
}
?>