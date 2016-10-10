<?php
namespace Shop\Controller;

use Think\Controller;

class BaseController extends Controller
{

    public function _initialize()
    {
        // 载入模板
        if (C('config.WEB_SITE_TEMPLATE')) {
            C('DEFAULT_THEME', C('config.WEB_SITE_TEMPLATE'));
        }
        // $this->checkOpenid();
        $this->setFatherId();
    }

    public function checkOpenid()
    {
        $openid = I('openid');
        $ctrl = A('Login');
        $ctrl->loginWechat($openid);
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