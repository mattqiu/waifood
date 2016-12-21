<?php
namespace Shop\Controller;

use Common\Model\UserModel;
use Think\Controller;

class BaseController extends Controller
{

    public function _initialize()
    {
        //特殊节日优惠标示，结束后删除
        if(isset( $_REQUEST['hd']) &&  $_REQUEST['hd']){
            session('hd', $_REQUEST['hd']);
            cookie('hd', $_REQUEST['hd']);
        }
        $user = UserModel::getUser();
        if($user){
            $this->assign('user',$user);
        }
        // 载入模板
        if (C('config.WEB_SITE_TEMPLATE')) {
            C('DEFAULT_THEME', C('config.WEB_SITE_TEMPLATE'));
        }
        $this->setFatherId();
    }

    /**
     * 获取推广者ID
     */
	public function setFatherId(){
		$fid=I(C('COUPON.FID'),0); 
		$cid=Cookie('fid');
		if($cid!=$fid){
			Cookie('fid',$fid);
		}
	}
}
?>