<?php
namespace Shop\Controller;

use Common\Model\UserModel;

class AuthController extends BaseController
{
    protected $userId = null;

    public function _initialize()
    {

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