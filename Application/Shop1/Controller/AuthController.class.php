<?php
namespace Shop\Controller;

class AuthController extends BaseController
{

    public function _initialize()
    {
        // 加入权限判断
        // session('userid',1);
        $this->checkLogin();
        parent::_initialize();
    }

    private function checkLogin()
    {
        // 用户权限检查
        if (get_userid() == 0) {
            $this->redirect('Login/index');
        }
    }
}
?>